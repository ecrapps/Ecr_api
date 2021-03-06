<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class GoperTasksController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}	

	public function checkLogin(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$datas->params->password = hash('sha256', $datas->params->password);
		$checkLogin = "SELECT id, name ";
		$checkLogin .= "FROM users ";
		$checkLogin .= "WHERE username = :login ";
		$checkLogin .= "AND password = :password ";
		$checkLoginResult = $this->container->db->query($checkLogin, $datas);
		$responseLogin = new stdClass();
		if ($checkLoginResult) {
			$responseLogin->loginSucceed = true;
			$responseLogin->user = new stdClass();
			$responseLogin->user->idUser = $checkLoginResult[0]['id'];
			$responseLogin->user->userName = $checkLoginResult[0]['name'];

			/* TODO :
			 * Enregistrer les données de l'user courant dans $_SESSION
			 * pour ne plus avoir à les passer dans la requête http
			 * Pour cela utiliser le le middleware : sessionMiddleware
			 *
			*/
			// $session = $this->container->session;
			// $session->set('EcrSession', $responseLogin);
		}
		else
			$responseLogin->loginSucceed = false;
		return $response->withStatus(200)
        				->write(json_encode($responseLogin,JSON_NUMERIC_CHECK));
	}

	public function getTasks(Request $request, Response $response, $args){
		$getTasks = "SELECT id, name, taskDelay ";
		$getTasks .= "FROM goper_tasks ORDER BY name";
		$getTasksResult = $this->container->db->query($getTasks);
		return $response->withStatus(200)
        				->write(json_encode($getTasksResult,JSON_NUMERIC_CHECK));
	}

	public function getTask(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getTask = "SELECT D.id, ";
		$getTask .= "D.idTask, ";
		$getTask .= "TK.name, ";
		$getTask .= "D.idTrain as trainId, ";
		$getTask .= "D.idUserChecked, ";
		$getTask .= "D.dateChecked, ";
		$getTask .= "D.checked, ";
		$getTask .= "DATE_FORMAT(D.deadline, '%d/%m/%Y - %H:%i') as deadline, ";
		$getTask .= "D.dateUpdate, ";
		$getTask .= "D.cancelled ";
		$getTask .= "FROM goper_dailytasks as D ";
		$getTask .= "LEFT JOIN goper_tasks as TK ON D.idTask = TK.id ";
		$getTask .= "LEFT JOIN goper_trains as TN ON D.idTrain = TN.id ";
		$getTask .= "WHERE NOT (D.DEADLINE < NOW() AND checked = 1) ";
		$getTask .= "AND D.cancelled <> 1 ";
		$getTask .= "AND D.id = :idTask ";
		$getTask .= "ORDER BY D.deadline ASC";
		$getTaskResult = $this->container->db->query($getTask, $datas);

		// Add comments to the element
		$getTaskComments = "SELECT C.id, ";
		$getTaskComments .= "C.idTask, ";
		$getTaskComments .= "C.idAuthor, ";
		$getTaskComments .= "U.name as author, ";
		$getTaskComments .= "C.content, ";
		$getTaskComments .= "C.date ";
		$getTaskComments .= "FROM goper_comments as C ";
		$getTaskComments .= "LEFT JOIN users as U ON C.idAuthor = U.id ";
		$getTaskComments .= "WHERE C.idTask = '".$getTaskResult[0]['id']."' ";
		$getTaskComments .= "ORDER BY C.date DESC";
		$getTaskCommentsResult = $this->container->db->query($getTaskComments);

		$getTaskResult[0]['comments'] = [];

		if (sizeof($getTaskCommentsResult) > 0) {
			$getTaskResult[0]['comments'] = $getTaskCommentsResult;
		}

		return $response->withStatus(200)
        				->write(json_encode($getTaskResult,JSON_NUMERIC_CHECK));
	}

	public function createTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$createTask = "INSERT INTO goper_tasks (name, taskDelay) ";
		$createTask .= "VALUES (:taskName, :taskDelay) ";
		$createTaskResult = $this->container->db->query($createTask, $datas);
		return $response->withStatus(200)
        				->write(json_encode($createTaskResult,JSON_NUMERIC_CHECK));
	}

	public function updateTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$updateTask = "UPDATE goper_tasks ";
		$updateTask .= "SET name = :taskName, ";
		$updateTask .= "taskDelay = :taskDelay ";
		$updateTask .= "WHERE id = :idTask ";
		$updateTaskResult = $this->container->db->query($updateTask, $datas);
		return $response->withStatus(200)
        				->write(json_encode($updateTaskResult,JSON_NUMERIC_CHECK));
	}

	public function deleteTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteTask = "DELETE FROM goper_tasks WHERE id = :idTask ";
		$deleteTaskResult = $this->container->db->query($deleteTask, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteTaskResult,JSON_NUMERIC_CHECK));
	}

	public function getTaskTypes(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getTaskTypes = "SELECT DISTINCT ";
		$getTaskTypes .= "CASE WHEN gtca.idTask THEN 1 ELSE 0 END as cancellation, ";
		$getTaskTypes .= "CASE WHEN gtm.idTask THEN 1 ELSE 0 END as md, ";
		$getTaskTypes .= "CASE WHEN gtco.idTask THEN 1 ELSE 0 END as common, ";
		$getTaskTypes .= "CASE WHEN gtcl.idTask THEN 1 ELSE 0 END as client, ";
		$getTaskTypes .= "CASE WHEN gtt.idTask THEN 1 ELSE 0 END as train ";
		$getTaskTypes .= "FROM goper_tasks as gt ";
		$getTaskTypes .= "LEFT JOIN goper_tasks_cancellation as gtca ON gt.id = gtca.idTask ";
		$getTaskTypes .= "LEFT JOIN goper_tasks_md as gtm ON gt.id = gtm.idTask ";
		$getTaskTypes .= "LEFT JOIN goper_tasks_common as gtco ON gt.id = gtco.idTask ";
		$getTaskTypes .= "LEFT JOIN goper_tasks_clients as gtcl ON gt.id = gtcl.idTask ";
		$getTaskTypes .= "LEFT JOIN goper_tasks_trains as gtt ON gt.id = gtt.idTask ";
		$getTaskTypes .= "WHERE gt.id = :idTask ";
		$getTaskTypesResult = $this->container->db->query($getTaskTypes, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getTaskTypesResult,JSON_NUMERIC_CHECK));
	}

	/*public function associateTrainTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$associateTrainTask = "INSERT INTO goper_tasks_trains (idTrain, idTask) ";
		$associateTrainTask .= "VALUES (:idTrain, :idTask) ";
		$associateTrainTaskResult = $this->container->db->query($associateTrainTask, $datas);
		return $response->withStatus(200)
        				->write(json_encode($associateTrainTaskResult,JSON_NUMERIC_CHECK));
	}

	public function deleteTrainTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteTrainTask = "DELETE FROM goper_tasks_trains ";
		$deleteTrainTask .= "WHERE idTrain = :idTrain AND idTask = :idTask";
		$deleteTrainTaskResult = $this->container->db->query($deleteTrainTask, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteTrainTaskResult,JSON_NUMERIC_CHECK));
	}

	public function getTrainsInTask(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		//$getTrainsTasks = "SELECT ug.idTrain as id, g.name, g.status ";
		//$getTrainsTasks .= "FROM goper_tasks_trains as ug ";
		//$getTrainsTasks .= "INNER JOIN goper_trains as g ON g.id=ug.idTrain ";
		//$getTrainsTasks .= "WHERE ug.idTask = :idTask ";
		$getTrainsTasks = "SELECT * ";
		$getTrainsTasks .= "FROM goper_trains ";
		$getTrainsTasksResult = $this->container->db->query($getTrainsTasks, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getTrainsTasksResult,JSON_NUMERIC_CHECK));
	}*/

	public function associateClientTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$associateClientTask = "INSERT INTO goper_tasks_clients (idClient, idTask) ";
		$associateClientTask .= "VALUES (:idClient, :idTask) ";
		$associateClientTaskResult = $this->container->db->query($associateClientTask, $datas);
		return $response->withStatus(200)
        				->write(json_encode($associateClientTaskResult,JSON_NUMERIC_CHECK));
	}

	public function deleteClientTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteClientTask = "DELETE FROM goper_tasks_clients ";
		$deleteClientTask .= "WHERE idClient = :idClient AND idTask = :idTask";
		$deleteClientTaskResult = $this->container->db->query($deleteClientTask, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteClientTaskResult,JSON_NUMERIC_CHECK));
	}

	public function getClientsInTask(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getClientsTasks = "SELECT ug.idClient as id, g.name, g.abreviation ";
		$getClientsTasks .= "FROM goper_tasks_clients as ug ";
		$getClientsTasks .= "INNER JOIN goper_clients as g ON g.id=ug.idClient ";
		$getClientsTasks .= "WHERE ug.idTask = :idTask ";
		$getClientsTasksResult = $this->container->db->query($getClientsTasks, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getClientsTasksResult,JSON_NUMERIC_CHECK));
	}

	public function associateCommonTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$selectAssociate = "SELECT idTask FROM goper_tasks_common WHERE idTask=:idTask";
		$selectAssociateResult = $this->container->db->query($selectAssociate, $datas);
		$associateCommonTask = "INSERT INTO goper_tasks_common (idTask) VALUES (:idTask) ";
		if (!isset($selectAssociateResult[0]['idTask']))
			$associateCommonTaskResult = $this->container->db->query($associateCommonTask, $datas);
		else 
			$associateCommonTaskResult = true;
		return $response->withStatus(200)
        				->write(json_encode($selectAssociateResult,JSON_NUMERIC_CHECK));
	}

	public function deleteCommonTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteCommonTask = "DELETE FROM goper_tasks_common ";
		$deleteCommonTask .= "WHERE idTask = :idTask";
		$deleteCommonTaskResult = $this->container->db->query($deleteCommonTask, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteCommonTaskResult,JSON_NUMERIC_CHECK));
	}

	public function associateCancellationTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$selectAssociate = "SELECT idTask FROM goper_tasks_cancellation WHERE idTask=:idTask";
		$selectAssociateResult = $this->container->db->query($selectAssociate, $datas);
		$associateCancellationTask = "INSERT INTO goper_tasks_cancellation (idTask) ";
		$associateCancellationTask .= "VALUES (:idTask) ";
		if (!$selectAssociateResult)
			$associateCancellationTaskResult = $this->container->db->query($associateCancellationTask, $datas);
		else
			$associateCancellationTaskResult = true;
		return $response->withStatus(200)
        				->write(json_encode($associateCancellationTaskResult,JSON_NUMERIC_CHECK));
	}

	public function deleteCancellationTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteCancellationTask = "DELETE FROM goper_tasks_cancellation ";
		$deleteCancellationTask .= "WHERE idTask = :idTask";
		$deleteCancellationTaskResult = $this->container->db->query($deleteCancellationTask, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteCancellationTaskResult,JSON_NUMERIC_CHECK));
	}

	public function associateMdTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$selectAssociate = "SELECT idTask FROM goper_tasks_md WHERE idTask=:idTask";
		$selectAssociateResult = $this->container->db->query($selectAssociate, $datas);
		$associateMdTask = "INSERT INTO goper_tasks_md (idTask) ";
		$associateMdTask .= "VALUES (:idTask) ";
		if (!$selectAssociateResult)
			$associateMdTaskResult = $this->container->db->query($associateMdTask, $datas);
		else
			$associateMdTaskResult = true;
		return $response->withStatus(200)
        				->write(json_encode($associateMdTaskResult,JSON_NUMERIC_CHECK));
	}

	public function deleteMdTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteMdTask = "DELETE FROM goper_tasks_md ";
		$deleteMdTask .= "WHERE idTask = :idTask";
		$deleteMdTaskResult = $this->container->db->query($deleteMdTask, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteMdTaskResult,JSON_NUMERIC_CHECK));
	}

	public function associateTrainTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$selectAssociate = "SELECT idTask FROM goper_tasks_trains WHERE idTask=:idTask";
		$selectAssociateResult = $this->container->db->query($selectAssociate, $datas);
		$associateTrainTask = "INSERT INTO goper_tasks_trains (idTask) ";
		$associateTrainTask .= "VALUES (:idTask) ";
		if (!$selectAssociateResult)
			$associateTrainTaskResult = $this->container->db->query($associateTrainTask, $datas);
		else
			$associateTrainTaskResult = true;
		return $response->withStatus(200)
        				->write(json_encode($associateTrainTaskResult,JSON_NUMERIC_CHECK));
	}

	public function deleteTrainTask(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteTrainTask = "DELETE FROM goper_tasks_trains ";
		$deleteTrainTask .= "WHERE idTask = :idTask";
		$deleteTrainTaskResult = $this->container->db->query($deleteTrainTask, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteTrainTaskResult,JSON_NUMERIC_CHECK));
	}
}