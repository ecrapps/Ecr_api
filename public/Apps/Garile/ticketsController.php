<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class TicketsController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	public function createTicket(Request $request, Response $response, $args){
		$parsedBody = $request->getParsedBody();
		$idAuthor = $_SESSION['VRAK']['id'];

		$createTicket = "INSERT INTO tickets (idAuthor, idSubject, status, creationDate) ";
		$createTicket .= "VALUES (:idAuthor, :idSubject, 0, NOW())";
		$datasCreateTicket->params->idAuthor = $idAuthor;
		$datasCreateTicket->params->idSubject = $parsedBody['idSubject'];
		$createTicketsResult = $this->container->db->query($createTicket, $datasCreateTicket);

		$sendMessage = "INSERT INTO tickets_messages (idTicket, idAuthor, content) ";
		$sendMessage .= "VALUES ((SELECT MAX(id) FROM tickets), :idAuthor, :content)";
		$datasSendMessage->params->idAuthor = $idAuthor;
		$datasSendMessage->params->content = $parsedBody['content'];
		$createTicketsResult = $this->container->db->query($sendMessage, $datasSendMessage);

		if($createTicketsResult && $createTicketsResult){
			$resultCreateTicket = "Create ticket succeed.";
		}else{
			$resultCreateTicket = "Error when trying to create ticket";
		}
		return $response->withStatus(200)
        				->write(json_encode($resultCreateTicket, JSON_NUMERIC_CHECK));
	}

	public function getTickets(Request $request, Response $response, $args){
		$getTickets = "SELECT ";
		$getTickets .= "t.id, ";
		$getTickets .= "uA.prenom as preAuthor, ";
		$getTickets .= "uA.nom as nomAuthor, ";
		$getTickets .= "uR.id as idResp, ";
		$getTickets .= "uR.prenom as preResp, ";
		$getTickets .= "uR.nom as nomResp, ";
		$getTickets .= "s.id as idSubject, ";
		$getTickets .= "s.subject as subject, ";
		$getTickets .= "t.status as status, ";
		$getTickets .= "t.creationDate as creationDate, ";
		$getTickets .= "t.updateDate as updateDate, ";
		$getTickets .= "CASE ";
		$getTickets .= "WHEN gu.idGroup=1 THEN 'admin' ";
		$getTickets .= "WHEN uR.id = :idAuthor THEN 'responsanle' ";
		$getTickets .= "WHEN uA.id = :idAuthor THEN 'user' ";
		$getTickets .= "ELSE '' ";
		$getTickets .= "END as userStatus ";
		$getTickets .= "FROM tickets as t ";
		$getTickets .= "LEFT JOIN users as uA ON uA.id=t.idAuthor  ";
		$getTickets .= "LEFT JOIN users as uR ON uR.id=t.idResp  ";
		$getTickets .= "LEFT JOIN tickets_subjects as s ON s.id=t.idSubject ";
		$getTickets .= "LEFT JOIN group_users as gu ON gu.idUser=:idAuthor AND gu.idGroup=1 ";
		$getTickets .= "WHERE uA.id=:idAuthor ";
		$getTickets .= "OR uR.id=:idAuthor ";
		$getTickets .= "OR (gu.idUser=:idAuthor AND gu.idGroup=1) ";
		$idAuthor = $_SESSION['VRAK']['id'];
		$datas->params->idAuthor = $idAuthor;
		$getTicketsResult = $this->container->db->query($getTickets, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getTicketsResult, JSON_NUMERIC_CHECK));
	}

	public function getMessages(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$getMessages = "SELECT ";
		$getMessages .= "m.id, ";
		$getMessages .= "uA.prenom as authorPre, ";
		$getMessages .= "uA.nom as authorNom, ";
		$getMessages .= "m.content as content, ";
		$getMessages .= "m.creationDate as creationDate ";
		$getMessages .= "FROM tickets_messages as m ";
		$getMessages .= "LEFT JOIN users as uA ON uA.id=m.idAuthor  ";
		$getMessages .= "WHERE idTicket=:idTicket ";
		$req = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getMessagesResult = $this->container->db->query($getMessages, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getMessagesResult, JSON_NUMERIC_CHECK));
	}

	public function sendMessage(Request $request, Response $response, $args){
		$parsedBody = $request->getParsedBody();
		$sendMessage = "INSERT INTO tickets_messages (idTicket, idAuthor, content) ";
		$sendMessage .= "VALUES (:idTicket, :idAuthor, :content)";
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($parsedBody), FALSE);
		$sendMessagesResult = $this->container->db->query($sendMessage, $datas);
		if($sendMessagesResult){
			$result = "New message inserted with success !";
		}else{
			$result = "Error when inserted new message !";
		}
		return $response->withStatus(200)
        				->write(json_encode($result, JSON_NUMERIC_CHECK));
	}

	public function getUsers(Request $request, Response $response, $args){
		$getUsers = "SELECT ";
		$getUsers .= "id, ";
		$getUsers .= "prenom, ";
		$getUsers .= "nom ";
		$getUsers .= "FROM users ";
		$getUsers .= "WHERE isOut=0 ";
		$getUsers .= "ORDER BY prenom, nom ";
		$getUsersResult = $this->container->db->query($getUsers);
		return $response->withStatus(200)
        				->write(json_encode($getUsersResult, JSON_NUMERIC_CHECK));
	}

	public function getSubjects(Request $request, Response $response, $args){
		$getSubjects = "SELECT ";
		$getSubjects .= "id, ";
		$getSubjects .= "subject ";
		$getSubjects .= "FROM tickets_subjects ";
		$getSubjects .= "ORDER BY subject ";
		$getSubjectsResult = $this->container->db->query($getSubjects);
		return $response->withStatus(200)
        				->write(json_encode($getSubjectsResult, JSON_NUMERIC_CHECK));
	}

	public function deleteTicket(Request $request, Response $response, $args){
		$parsedBody = $request->getParsedBody();
		$deleteTicket = "DELETE FROM tickets WHERE id=:idTicket";
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($parsedBody), FALSE);
		$deleteTicketResult = $this->container->db->query($deleteTicket, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteTicketResult, JSON_NUMERIC_CHECK));
	}

	public function updateStatus(Request $request, Response $response, $args){
		$parsedBody = $request->getParsedBody();
		$updateStatus = "UPDATE tickets SET status=:status WHERE id=:idTicket ";
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($parsedBody), FALSE);
		$updateStatusResult = $this->container->db->query($updateStatus, $datas);
		return $response->withStatus(200)
        				->write(json_encode($updateStatusResult, JSON_NUMERIC_CHECK));
	}

	public function getUser(Request $request, Response $response, $args){
		$getUser = "SELECT ";
		$getUser .= "id, ";
		$getUser .= "prenom, ";
		$getUser .= "nom ";
		$getUser .= "FROM users ";
		$getUser .= "WHERE id=:idUser ";
		$idUser = $_SESSION['VRAK']['id'];
		$datas = new stdClass();
		$datas->params->idUser = $idUser;
		$getUserResult = $this->container->db->query($getUser, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getUserResult, JSON_NUMERIC_CHECK));
	}
}