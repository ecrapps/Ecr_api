<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class GoperController {

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
			$responseLogin->user->userId = $checkLoginResult[0]['id'];
			$responseLogin->user->userName = $checkLoginResult[0]['name'];
		}
		else
			$responseLogin->loginSucceed = false;
		return $response->withStatus(200)
        				->write(json_encode($responseLogin,JSON_NUMERIC_CHECK));
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	//								TrainIds							//
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	public function getTrainIds(Request $request, Response $response, $args){
		$getTrainIds = "SELECT id, name, isMD ";
		$getTrainIds .= "FROM goper_trains ORDER BY name";
		$getTrainIdsResult = $this->container->db->query($getTrainIds);
		return $response->withStatus(200)
        				->write(json_encode($getTrainIdsResult,JSON_NUMERIC_CHECK));
	}

	public function createTrainId(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$createTrainId = "INSERT INTO goper_trains (name, isMD) ";
		$createTrainId .= "VALUES (:trainIdName, :trainIdMd) ";
		$createTrainIdResult = $this->container->db->query($createTrainId, $datas);
		return $response->withStatus(200)
        				->write(json_encode($createTrainIdResult,JSON_NUMERIC_CHECK));
	}

	public function updateTrainId(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$updateTrainId = "UPDATE goper_trains ";
		$updateTrainId .= "SET name = :trainIdName, ";
		$updateTrainId .= "isMD = :trainIdMd ";
		$updateTrainId .= "WHERE id = :idTrainId ";
		$updateTrainIdResult = $this->container->db->query($updateTrainId, $datas);
		return $response->withStatus(200)
        				->write(json_encode($updateTrainIdResult,JSON_NUMERIC_CHECK));
	}

	public function deleteTrainId(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteTrainId = "DELETE FROM goper_trains WHERE id = :idTrainId ";
		$deleteTrainIdResult = $this->container->db->query($deleteTrainId, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteTrainIdResult,JSON_NUMERIC_CHECK));
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	//								Tasks								//
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
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
		$getTaskComments .= "C.author as idAuthor, ";
		$getTaskComments .= "U.name as author, ";
		$getTaskComments .= "C.content, ";
		$getTaskComments .= "C.date ";
		$getTaskComments .= "FROM goper_comments as C ";
		$getTaskComments .= "LEFT JOIN users as U ON C.author = U.id ";
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

	public function associateTrainTask(Request $request, Response $response, $args){
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
		$getTrainsTasks = "SELECT ug.idTrain as id, g.name, g.status ";
		$getTrainsTasks .= "FROM goper_tasks_trains as ug ";
		$getTrainsTasks .= "INNER JOIN goper_trains as g ON g.id=ug.idTrain ";
		$getTrainsTasks .= "WHERE ug.idTask = :idTask ";
		$getTrainsTasksResult = $this->container->db->query($getTrainsTasks, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getTrainsTasksResult,JSON_NUMERIC_CHECK));
	}

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

	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	//							Daily Tasks								//
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	
	public function getDailyTasks(Request $request, Response $response, $args){
		$getDailyTasks = "SELECT D.id, ";
		$getDailyTasks .= "D.idTask, ";
		$getDailyTasks .= "TK.name as taskname, ";
		$getDailyTasks .= "D.trainId as trainId, ";
		$getDailyTasks .= "D.idClient, ";
		$getDailyTasks .= "D.idUserChecked, ";
		$getDailyTasks .= "D.dateChecked, ";
		$getDailyTasks .= "D.checked, ";
		$getDailyTasks .= "D.deadline, ";
		$getDailyTasks .= "D.dateUpdate, ";
		$getDailyTasks .= "D.cancelled ";
		$getDailyTasks .= "FROM goper_dailytasks as D ";
		$getDailyTasks .= "LEFT JOIN goper_tasks as TK ON D.idTask = TK.id ";
		$getDailyTasks .= "LEFT JOIN goper_trains as TN ON D.idTrain = TN.id ";
		$getDailyTasks .= "WHERE NOT (D.DEADLINE < NOW() AND checked = 1) ";
		$getDailyTasks .= "AND D.cancelled <> 1 ";
		$getDailyTasks .= "ORDER BY D.deadline ASC";
		$getDailyTasksResult = $this->container->db->query($getDailyTasks);

		for ($i=0 ; $i<sizeof($getDailyTasksResult) ; $i++) {
			// Add comments to each element
			$getTaskComments = "SELECT C.id, ";
			$getTaskComments .= "C.idTask, ";
			$getTaskComments .= "C.author as idAuthor, ";
			$getTaskComments .= "U.name as author, ";
			$getTaskComments .= "C.content, ";
			$getTaskComments .= "C.date ";
			$getTaskComments .= "FROM goper_comments as C ";
			$getTaskComments .= "LEFT JOIN users as U ON C.author = U.id ";
			$getTaskComments .= "WHERE C.idTask = '".$getDailyTasksResult[$i]['id']."' ";
			$getTaskComments .= "ORDER BY C.date DESC";
			$getTaskCommentsResult = $this->container->db->query($getTaskComments);

			$getDailyTasksResult[$i]['comments'] = [];

			if (sizeof($getTaskCommentsResult) > 0) {
				$getDailyTasksResult[$i]['comments'] = $getTaskCommentsResult;
			}

			// Add client information to each element
			$getTaskClient = "SELECT C.id, ";
			$getTaskClient .= "C.abreviation, ";
			$getTaskClient .= "C.name ";
			$getTaskClient .= "FROM goper_clients as C ";
			$getTaskClient .= "WHERE C.id = '".$getDailyTasksResult[$i]['idClient']."'";
			$getTaskClientResult = $this->container->db->query($getTaskClient);

			$getDailyTasksResult[$i]['client'] = [];

			if (sizeof($getTaskClientResult) > 0) {
				$getDailyTasksResult[$i]['client'] = $getTaskClientResult[0];
			}
		}

		return $response->withStatus(200)
        				->write(json_encode($getDailyTasksResult,JSON_NUMERIC_CHECK));
	}

	public function getHistoryDailyTasks(Request $request, Response $response, $args){
		$getDailyTasks = "SELECT D.id, ";
		$getDailyTasks .= "D.idTask, ";
		$getDailyTasks .= "TK.name as taskname, ";
		$getDailyTasks .= "D.trainId as trainId, ";
		$getDailyTasks .= "D.idClient, ";
		$getDailyTasks .= "D.idUserChecked, ";
		$getDailyTasks .= "D.dateChecked, ";
		$getDailyTasks .= "D.checked, ";
		$getDailyTasks .= "D.deadline, ";
		$getDailyTasks .= "D.dateUpdate, ";
		$getDailyTasks .= "D.cancelled ";
		$getDailyTasks .= "FROM goper_dailytasks as D ";
		$getDailyTasks .= "LEFT JOIN goper_tasks as TK ON D.idTask = TK.id ";
		$getDailyTasks .= "LEFT JOIN goper_trains as TN ON D.idTrain = TN.id ";
		/*$getDailyTasks .= "WHERE NOT (D.DEADLINE < NOW() AND checked = 1) ";
		$getDailyTasks .= "AND D.cancelled <> 1 ";*/
		$getDailyTasks .= "ORDER BY D.deadline ASC";
		$getDailyTasksResult = $this->container->db->query($getDailyTasks);

		for ($i=0 ; $i<sizeof($getDailyTasksResult) ; $i++) {
			// Add comments to each element
			$getTaskComments = "SELECT C.id, ";
			$getTaskComments .= "C.idTask, ";
			$getTaskComments .= "C.author as idAuthor, ";
			$getTaskComments .= "U.name as author, ";
			$getTaskComments .= "C.content, ";
			$getTaskComments .= "C.date ";
			$getTaskComments .= "FROM goper_comments as C ";
			$getTaskComments .= "LEFT JOIN users as U ON C.author = U.id ";
			$getTaskComments .= "WHERE C.idTask = '".$getDailyTasksResult[$i]['id']."' ";
			$getTaskComments .= "ORDER BY C.date DESC";
			$getTaskCommentsResult = $this->container->db->query($getTaskComments);

			$getDailyTasksResult[$i]['comments'] = [];

			if (sizeof($getTaskCommentsResult) > 0) {
				$getDailyTasksResult[$i]['comments'] = $getTaskCommentsResult;
			}

			// Add client information to each element
			$getTaskClient = "SELECT C.id, ";
			$getTaskClient .= "C.abreviation, ";
			$getTaskClient .= "C.name ";
			$getTaskClient .= "FROM goper_clients as C ";
			$getTaskClient .= "WHERE C.id = '".$getDailyTasksResult[$i]['idClient']."'";
			$getTaskClientResult = $this->container->db->query($getTaskClient);

			$getDailyTasksResult[$i]['client'] = [];

			if (sizeof($getTaskClientResult) > 0) {
				$getDailyTasksResult[$i]['client'] = $getTaskClientResult[0];
			}
		}

		return $response->withStatus(200)
        				->write(json_encode($getDailyTasksResult,JSON_NUMERIC_CHECK));
	}

	public function updateTaskCheck(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$updateTaskCheck = "UPDATE goper_dailytasks ";
		$updateTaskCheck .= "SET checked = :taskIsChecked ";
		$updateTaskCheck .= "WHERE id = :taskId";
		$updateTaskCheckResult = $this->container->db->query($updateTaskCheck, $datas);
		return $response->withStatus(200)
        				->write(json_encode($updateTaskCheckResult,JSON_NUMERIC_CHECK));
	}

	public function saveNewComment(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$saveNewComment = "INSERT INTO goper_comments ";
		$saveNewComment .= "VALUES (NULL, :idTask, :author, :content, NOW())";
		$saveNewCommentResult = $this->container->db->query($saveNewComment, $datas);
		return $response->withStatus(200)
        				->write(json_encode($saveNewCommentResult,JSON_NUMERIC_CHECK));
	}

}