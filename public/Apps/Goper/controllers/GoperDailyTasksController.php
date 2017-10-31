<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class GoperDailyTasksController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}
	
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
			$getTaskComments .= "C.idAuthor, ";
			$getTaskComments .= "U.name as author, ";
			$getTaskComments .= "C.content, ";
			$getTaskComments .= "C.date ";
			$getTaskComments .= "FROM goper_comments as C ";
			$getTaskComments .= "LEFT JOIN users as U ON C.idAuthor = U.id ";
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
		$getDailyTasks .= "WHERE D.cancelled <> 1 ";
		$getDailyTasks .= "ORDER BY D.deadline ASC";
		$getDailyTasksResult = $this->container->db->query($getDailyTasks);

		for ($i=0 ; $i<sizeof($getDailyTasksResult) ; $i++) {
			// Add comments to each element
			$getTaskComments = "SELECT C.id, ";
			$getTaskComments .= "C.idTask, ";
			$getTaskComments .= "C.idAuthor, ";
			$getTaskComments .= "U.name as author, ";
			$getTaskComments .= "C.content, ";
			$getTaskComments .= "C.date ";
			$getTaskComments .= "FROM goper_comments as C ";
			$getTaskComments .= "LEFT JOIN users as U ON C.idAuthor = U.id ";
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
		$saveNewComment .= "VALUES (NULL, :idTask, :idAuthor, :content, NOW())";
		$saveNewCommentResult = $this->container->db->query($saveNewComment, $datas);
		return $response->withStatus(200)
        				->write(json_encode($saveNewCommentResult,JSON_NUMERIC_CHECK));
	}
}