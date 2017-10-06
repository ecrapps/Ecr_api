<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class GoperImportController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	function import(Request $request, Response $response, $args){
		$uploadedFiles = $request->getUploadedFiles();

		$file = fopen($uploadedFiles['file']->file, "r");

		// Check if headers are found on first row
		if (($data = fgetcsv($file, 0, ";")) !== FALSE) {
			// By default, set the headers to "not found"
			$headerIsCancelledFound = false;
			$headerTrainIdFound = false;
			$headerActualStartFound = false;
			$headerTypeFound = false;
			$labelColIsCancelled = "IsCancelledIMG (Train)";
			$labelColTrainId = "TrainID (Train)";
			$labelColActualStart = "ActualStart";
			$labelColType = "Type";

			// If headers are found, set headerFound to true
			//  and set index to column number
			for($i=0 ; $i<sizeof($data) ; $i++) {
				if ($data[$i] == $labelColIsCancelled) {
					$headerIsCancelledFound = true;
					$indexColIsCancelled = $i;
				}
				if ($data[$i] == $labelColTrainId) {
					$headerTrainIdFound = true;
					$indexColTrainId = $i;
				}
				if ($data[$i] == $labelColActualStart) {
					$headerActualStartFound = true;
					$indexColActualStart = $i;
				}
				if ($data[$i] == $labelColType) {
					$headerTypeFound = true;
					$indexColType = $i;
				}
			}

			if ($headerIsCancelledFound && $headerTrainIdFound && $headerActualStartFound && $headerTypeFound) {
				// Headers were found
				// Instanciate tables as empty tables
				$tabTrainIds = [];
				$tabClients = [];

				// Filling tabTrainIds and tabClients
				while (($data = fgetcsv($file, 0, ";")) !== FALSE) {
					// Only keep the earlisest of LEG type lines based on actualStart
					if ($data[$indexColType] == "LEG") {

						$keyTrainIdInTabTrainIds = array_search($data[$indexColTrainId], array_column($tabTrainIds, $indexColTrainId));
						if ($keyTrainIdInTabTrainIds !== false) {
							// Check if time is earlier than time already in table
							//  if so, change the time in the table to the new earliest actualStart
							if ($data[$indexColActualStart] < $tabTrainIds[$keyTrainIdInTabTrainIds][$indexColActualStart]) {
								$tabTrainIds[$keyTrainIdInTabTrainIds][$indexColActualStart] = $data[$indexColActualStart];
							}

						} else {
							array_push($tabTrainIds, $data);

							// Filling tabClients
							$elementsTrainId = explode('-', $data[$indexColTrainId]);
							$client = $elementsTrainId[2];
							if (!in_array($client, $tabClients)) {
								array_push($tabClients, $client);
							}
						}
					} // END if type == "leg"
				} // END while line

				// Create common tasks (only once per day)
				$checkIfCommonTasksAreCreated = "SELECT id ";
				$checkIfCommonTasksAreCreated .= "FROM goper_dailytasks ";
				$checkIfCommonTasksAreCreated .= "WHERE idTrain = '999-999' ";
				$checkIfCommonTasksAreCreated .= "AND DATE_FORMAT(dateUpdate, '%Y-%m-%d') = '" . date('Y-m-d') . "'";
				$checkIfCommonTasksAreCreatedResult = $this->container->db->query($checkIfCommonTasksAreCreated);

				if (!$checkIfCommonTasksAreCreatedResult) {
					$getCommonTasksIds = "SELECT * ";
					$getCommonTasksIds .= "FROM goper_tasks_common";
					$getCommonTasksIdsResult = $this->container->db->query($getCommonTasksIds);

					foreach ($getCommonTasksIdsResult as $commonTask) {
						$getCommonTaskDetails = "SELECT * ";
						$getCommonTaskDetails .= "FROM goper_tasks ";
						$getCommonTaskDetails .= "WHERE id = " . $commonTask['idTask'];
						$getCommonTaskDetailsResult = $this->container->db->query($getCommonTaskDetails);

						// Calculate task's deadline
						$deadline = date( "Y-m-d H:i", strtotime(date("Y-m-d ").$getCommonTaskDetailsResult[0]['taskDelay']) );

						// Insert task into db
						$createCommonTaskIntoDailyTasks = "INSERT INTO goper_dailytasks ";
						$createCommonTaskIntoDailyTasks .= "VALUES (NULL, '".$getCommonTaskDetailsResult[0]['id']."', '999-999', '', '', '', '0', '', '" . $deadline ."', '".date('Y-m-d H:i')."', '0')";
							$createCommonTaskIntoDailyTasksResult = $this->container->db->query($createCommonTaskIntoDailyTasks);
					}
				}

				// Create tasks for each client (only once per day)
				foreach ($tabClients as $trigrammeClient) {
					$checkIfClientTasksAreCreated = "SELECT id ";
					$checkIfClientTasksAreCreated .= "FROM goper_dailytasks ";
					$checkIfClientTasksAreCreated .= "WHERE idTrain = '999-".$trigrammeClient."' ";
					$checkIfClientTasksAreCreated .= "AND DATE_FORMAT(dateUpdate, '%Y-%m-%d') = '" . date('Y-m-d') . "'";
					$checkIfClientTasksAreCreatedResult = $this->container->db->query($checkIfClientTasksAreCreated);

					if (!$checkIfClientTasksAreCreatedResult) {
						$getClientId = "SELECT * ";
						$getClientId .= "FROM goper_clients ";
						$getClientId .= "WHERE abreviation = '".$trigrammeClient."'";
						$getClientIdResult = $this->container->db->query($getClientId);

						$getClientTasksIds = "SELECT * ";
						$getClientTasksIds .= "FROM goper_tasks_clients ";
						$getClientTasksIds .= "WHERE idClient = '".$getClientIdResult[0]['id']."'";
						$getClientTasksIdsResult = $this->container->db->query($getClientTasksIds);

						foreach ($getClientTasksIdsResult as $clientTask) {
							$getClientTaskDetails = "SELECT * ";
							$getClientTaskDetails .= "FROM goper_tasks ";
							$getClientTaskDetails .= "WHERE id = '".$clientTask['idTask']."'";
							$getClientTaskDetailsResult = $this->container->db->query($getClientTaskDetails);

							// Calculate task's deadline
							$deadline = date( "Y-m-d H:i", strtotime(date("Y-m-d ").$getClientTaskDetailsResult[0]['taskDelay']) );

							// Insert task into db
							$createClientTaskIntoDailyTasks = "INSERT INTO goper_dailytasks ";
							$createClientTaskIntoDailyTasks .= "VALUES (NULL, '".$getClientTaskDetailsResult[0]['id']."', '999-".$trigrammeClient."', '', '', '', '0', '', '" . $deadline ."', '".date('Y-m-d H:i')."', '0')";
							$createClientTaskIntoDailyTasksResult = $this->container->db->query($createClientTaskIntoDailyTasks);
						}
					}
				}

				// Create tasks for trains
				foreach ($tabTrainIds as $lineTabTrainIds) {
					if ($lineTabTrainIds[$indexColIsCancelled] != 'DELETE') {
						$checkIfTrainIdTasksAreCreated = "SELECT id ";
						$checkIfTrainIdTasksAreCreated .= "FROM goper_dailytasks ";
						$checkIfTrainIdTasksAreCreated .= "WHERE idTrain = '".$lineTabTrainIds[$indexColTrainId]."' ";
						$checkIfTrainIdTasksAreCreated .= "AND DATE_FORMAT(dateUpdate, '%Y-%m-%d') = '" . date('Y-m-d') . "'";
						$checkIfTrainIdTasksAreCreatedResult = $this->container->db->query($checkIfTrainIdTasksAreCreated);

						if (!$checkIfTrainIdTasksAreCreatedResult) {
							$getTrainTasksIds = "SELECT * ";
							$getTrainTasksIds .= "FROM goper_tasks_trains ";
							$getTrainTasksIdsResult = $this->container->db->query($getTrainTasksIds);

							foreach ($getTrainTasksIdsResult as $trainTask) {
								$getTrainTaskDetails = "SELECT * ";
								$getTrainTaskDetails .= "FROM goper_tasks ";
								$getTrainTaskDetails .= "WHERE id = '".$trainTask['idTask']."'";
								$getTrainTaskDetailsResult = $this->container->db->query($getTrainTaskDetails);

								// Format date field in order to match the international format
								$internationalDateFormat = DateTime::createFromFormat('d/m/Y H:i', $lineTabTrainIds[$indexColActualStart])->format('Y-m-d H:i');

								// Calculate task's deadline
								$firstCarTaskDelay = substr($getTrainTaskDetailsResult[0]['taskDelay'], 0, 1);
								$taskDelay = substr($getTrainTaskDetailsResult[0]['taskDelay'], 1);
								$deadline = date( "Y-m-d H:i", strtotime($internationalDateFormat . " " . $firstCarTaskDelay . $taskDelay." minutes") );

								// Insert task into db
								$createTrainTaskIntoDailyTasks = "INSERT INTO goper_dailytasks ";
								$createTrainTaskIntoDailyTasks .= "VALUES (NULL, '".$getTrainTaskDetailsResult[0]['id']."', '".$lineTabTrainIds[$indexColTrainId]."', '', '', '', '0', '', '" . $deadline ."', '".date('Y-m-d H:i')."', '0')";
								$createTrainTaskIntoDailyTasksResult = $this->container->db->query($createTrainTaskIntoDailyTasks);
							}

							// Create tasks if train is MD
							$trainIdElements = explode("-", $lineTabTrainIds[$indexColTrainId]);
							$shortTrainId = $trainIdElements[0]."-".$trainIdElements[1]."-".$trainIdElements[2];

							$checkIfTrainIsMD = "SELECT * ";
							$checkIfTrainIsMD .= "FROM goper_trains ";
							$checkIfTrainIsMD .= "WHERE name = '".$shortTrainId."' ";
							$checkIfTrainIsMD .= "AND isMD = 1";
							$checkIfTrainIsMDResult = $this->container->db->query($checkIfTrainIsMD);

							if ($checkIfTrainIsMDResult) {
								$getTrainTasksIds = "SELECT * ";
								$getTrainTasksIds .= "FROM goper_tasks_md ";
								$getTrainTasksIdsResult = $this->container->db->query($getTrainTasksIds);

								foreach ($getTrainTasksIdsResult as $trainTask) {
									$getTrainTaskDetails = "SELECT * ";
									$getTrainTaskDetails .= "FROM goper_tasks ";
									$getTrainTaskDetails .= "WHERE id = '".$trainTask['idTask']."'";
									$getTrainTaskDetailsResult = $this->container->db->query($getTrainTaskDetails);

									// Format date field in order to match the international format
									$internationalDateFormat = DateTime::createFromFormat('d/m/Y H:i', $lineTabTrainIds[$indexColActualStart])->format('Y-m-d H:i');

									// Calculate task's deadline
									$firstCarTaskDelay = substr($getTrainTaskDetailsResult[0]['taskDelay'], 0, 1);
									$taskDelay = substr($getTrainTaskDetailsResult[0]['taskDelay'], 1);
									$deadline = date( "Y-m-d H:i", strtotime($internationalDateFormat . " " . $firstCarTaskDelay . $taskDelay." minutes") );

									// Insert task into db
									$createTrainTaskIntoDailyTasks = "INSERT INTO goper_dailytasks ";
									$createTrainTaskIntoDailyTasks .= "VALUES (NULL, '".$getTrainTaskDetailsResult[0]['id']."', '".$lineTabTrainIds[$indexColTrainId]."', '', '', '', '0', '', '" . $deadline ."', '".date('Y-m-d H:i')."', '0')";
									$createTrainTaskIntoDailyTasksResult = $this->container->db->query($createTrainTaskIntoDailyTasks);
								}
							}
						}
					} else {
						// Create table of cancellation tasks ids
						$getCancellationTasksIds = "SELECT * ";
						$getCancellationTasksIds .= "FROM goper_tasks_cancellation ";
						$getCancellationTasksIdsResult = $this->container->db->query($getCancellationTasksIds);

						// Format the results so that it matches SQL syntax
						$tabCancellationTasksIds = "(";
						foreach ($getCancellationTasksIdsResult as $cancellationTaskId) {
							$tabCancellationTasksIds .= $cancellationTaskId['idTask'].",";
						}
						$tabCancellationTasksIds = substr($tabCancellationTasksIds, 0, strlen($tabCancellationTasksIds)-1);
						$tabCancellationTasksIds .= ")";

						$checkIfTrainIdTasksAreCreated = "SELECT id ";
						$checkIfTrainIdTasksAreCreated .= "FROM goper_dailytasks ";
						$checkIfTrainIdTasksAreCreated .= "WHERE idTrain = '".$lineTabTrainIds[$indexColTrainId]."' ";
						$checkIfTrainIdTasksAreCreated .= "AND DATE_FORMAT(dateUpdate, '%Y-%m-%d') = '".date('Y-m-d')."'";
						$checkIfTrainIdTasksAreCreatedResult = $this->container->db->query($checkIfTrainIdTasksAreCreated);

						if ($checkIfTrainIdTasksAreCreatedResult) {
							$cancelTask = "UPDATE goper_dailytasks ";
							$cancelTask .= "SET cancelled = 1 ";
							$cancelTask .= "WHERE idTrain = '".$lineTabTrainIds[$indexColTrainId]."' ";
							$cancelTask .= "AND DATE_FORMAT(dateUpdate, '%Y-%m-%d') = '".date('Y-m-d')."'";
							$cancelTask .= "AND idTask NOT IN ".$tabCancellationTasksIds;
							$cancelTaskResult = $this->container->db->query($cancelTask);
						}

						$checkIfCancellationTasksAreCreated = "SELECT id ";
						$checkIfCancellationTasksAreCreated .= "FROM goper_dailytasks ";
						$checkIfCancellationTasksAreCreated .= "WHERE idTrain = '".$lineTabTrainIds[$indexColTrainId]."' ";
						$checkIfCancellationTasksAreCreated .= "AND DATE_FORMAT(dateUpdate, '%Y-%m-%d') = '" . date('Y-m-d') . "' ";
						$checkIfCancellationTasksAreCreated .= "AND idTask IN ".$tabCancellationTasksIds;
						$checkIfCancellationTasksAreCreatedResult = $this->container->db->query($checkIfCancellationTasksAreCreated);

						if (!$checkIfCancellationTasksAreCreatedResult) {
							foreach ($getCancellationTasksIdsResult as $cancellationTask) {
								$getCancellationTaskDetails = "SELECT * ";
								$getCancellationTaskDetails .= "FROM goper_tasks ";
								$getCancellationTaskDetails .= "WHERE id = '".$cancellationTask['idTask']."'";
								$getCancellationTaskDetailsResult = $this->container->db->query($getCancellationTaskDetails);

								// Calculate task's deadline
								$deadline = date( "Y-m-d H:i", strtotime(date("Y-m-d ").$getCancellationTaskDetailsResult[0]['taskDelay']) );

								// Insert task into db
								$createCancellationTaskIntoDailyTasks = "INSERT INTO goper_dailytasks ";
								$createCancellationTaskIntoDailyTasks .= "VALUES (NULL, '".$getCancellationTaskDetailsResult[0]['id']."', '".$lineTabTrainIds[$indexColTrainId]."', '', '', '', '0', '', '" . $deadline ."', '".date('Y-m-d H:i')."', '0')";
								$createCancellationTaskIntoDailyTasksResult = $this->container->db->query($createCancellationTaskIntoDailyTasks);
							}
						}
					} // END if not "delete" else
				}

			// END headers were found
			} else {
				// Headers were not found -> error
				echo "Format de fichier non valide : les headers ne sont pas trouvés.\n";
				$responseImport = "Format de fichier non valide : les headers ne sont pas trouvés.\n";
				return $response->withStatus(415)
        				    		->write(json_encode($responseImport,JSON_NUMERIC_CHECK));
			}
		} else {
			// Empty file -> error
			echo "Format de fichier non valide : fichier vide.\n";
			$responseImport = "Format de fichier non valide : fichier vide.\n";
			return $response->withStatus(415)
      				    		->write(json_encode($responseImport,JSON_NUMERIC_CHECK));
		}

		return $response->withStatus(200)
        				    ->write(json_encode($responseImport,JSON_NUMERIC_CHECK));
	}
}