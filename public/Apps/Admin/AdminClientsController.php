<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AdminClientsController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	//								Clients								//
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	public function getClients(Request $request, Response $response, $args){
		$getClients = "SELECT id, name, abreviation ";
		$getClients .= "FROM goper_clients ORDER BY name";
		$getClientsResult = $this->container->db->query($getClients);
		return $response->withStatus(200)
        				->write(json_encode($getClientsResult,JSON_NUMERIC_CHECK));
	}

	public function createClient(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$createClient = "INSERT INTO goper_clients (name, abreviation) ";
		$createClient .= "VALUES (:clientName, :clientAbreviation) ";
		$createClientResult = $this->container->db->query($createClient, $datas);
		return $response->withStatus(200)
        				->write(json_encode($createClientResult,JSON_NUMERIC_CHECK));
	}

	public function updateClient(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$updateClient = "UPDATE goper_clients ";
		$updateClient .= "SET name = :clientName, ";
		$updateClient .= "abreviation = :clientAbreviation ";
		$updateClient .= "WHERE id = :idClient ";
		$updateClientResult = $this->container->db->query($updateClient, $datas);
		return $response->withStatus(200)
        				->write(json_encode($updateClientResult,JSON_NUMERIC_CHECK));
	}

	public function deleteClient(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteClient = "DELETE FROM goper_clients WHERE id = :idClient ";
		$deleteClientResult = $this->container->db->query($deleteClient, $datas);
		// Delete all client-users associations from users_clients
		$deleteClientUsers = "DELETE FROM users_clients WHERE idClient = :idClient ";
		$deleteClientUsersResult = $this->container->db->query($deleteClientUsers, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteClientResult,JSON_NUMERIC_CHECK));
	}

	public function getClientsInUser(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getClientsUsers = "SELECT uc.idClient, c.name ";
		$getClientsUsers .= "FROM users_clients as uc ";
		$getClientsUsers .= "INNER JOIN goper_clients as c ON c.id=uc.idClient ";
		$getClientsUsers .= "WHERE uc.idUser = :idUser ";
		$getClientsUsersResult = $this->container->db->query($getClientsUsers, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getClientsUsersResult,JSON_NUMERIC_CHECK));
	}
}