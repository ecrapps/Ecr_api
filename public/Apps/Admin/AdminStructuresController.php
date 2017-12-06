<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AdminStructuresController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	//							Structures								//
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	public function getStructures(Request $request, Response $response, $args){
		$getStructures = "SELECT id, name, abreviation ";
		$getStructures .= "FROM structures ORDER BY name";
		$getStructuresResult = $this->container->db->query($getStructures);
		return $response->withStatus(200)
        				->write(json_encode($getStructuresResult,JSON_NUMERIC_CHECK));
	}

	public function createStructure(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$createStructure = "INSERT INTO structures (name, abreviation) ";
		$createStructure .= "VALUES (:structureName, :structureAbreviation) ";
		$createStructureResult = $this->container->db->query($createStructure, $datas);
		return $response->withStatus(200)
        				->write(json_encode($createStructureResult,JSON_NUMERIC_CHECK));
	}

	public function updateStructure(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$updateStructure = "UPDATE structures ";
		$updateStructure .= "SET name = :structureName, ";
		$updateStructure .= "abreviation = :structureAbreviation ";
		$updateStructure .= "WHERE id = :idStructure ";
		$updateStructureResult = $this->container->db->query($updateStructure, $datas);
		return $response->withStatus(200)
        				->write(json_encode($updateStructureResult,JSON_NUMERIC_CHECK));
	}

	public function deleteStructure(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteStructure = "DELETE FROM structures WHERE id = :idStructure ";
		$deleteStructureResult = $this->container->db->query($deleteStructure, $datas);
		// Delete all structure-users associations from users_structures
		$deleteStructureUsers = "DELETE FROM users_structures WHERE idStructure = :idStructure ";
		$deleteStructureUsersResult = $this->container->db->query($deleteStructureUsers, $datas);
		// Delete all structure-clients associations from clients_structures
		$deleteStructureClients = "DELETE FROM clients_structures WHERE idStructure = :idStructure ";
		$deleteStructureClientsResult = $this->container->db->query($deleteStructureClients, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteStructureResult,JSON_NUMERIC_CHECK));
	}

	public function associateStructureClient(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$associateStructureClient = "INSERT INTO clients_structures (idStructure, idClient) ";
		$associateStructureClient .= "VALUES (:idStructure, :idClient) ";
		$associateStructureClientResult = $this->container->db->query($associateStructureClient, $datas);
		return $response->withStatus(200)
        				->write(json_encode($associateStructureClientResult,JSON_NUMERIC_CHECK));
	}

	public function deleteStructureClient(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteStructureClient = "DELETE FROM clients_structures ";
		$deleteStructureClient .= "WHERE idStructure = :idStructure AND idClient = :idClient";
		$deleteStructureClientResult = $this->container->db->query($deleteStructureClient, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteStructureClientResult,JSON_NUMERIC_CHECK));
	}

	public function getStructuresInClient(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getStructuresInClient = "SELECT cs.idStructure AS id, s.name ";
		$getStructuresInClient .= "FROM clients_structures as cs ";
		$getStructuresInClient .= "INNER JOIN structures as s ON s.id=cs.idStructure ";
		$getStructuresInClient .= "WHERE cs.idClient = :idClient ";
		$getStructuresInClientResult = $this->container->db->query($getStructuresInClient, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getStructuresInClientResult,JSON_NUMERIC_CHECK));
	}

	public function getStructuresInUser(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getStructuresInUser = "SELECT us.idStructure AS id, s.name ";
		$getStructuresInUser .= "FROM users_structures as us ";
		$getStructuresInUser .= "INNER JOIN structures as s ON s.id=us.idStructure ";
		$getStructuresInUser .= "WHERE us.idUser = :idUser ";
		$getStructuresInUserResult = $this->container->db->query($getStructuresInUser, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getStructuresInUserResult,JSON_NUMERIC_CHECK));
	}

	public function associateStructureUser(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$associateStructureUser = "INSERT INTO users_structures (idStructure, idUser) ";
		$associateStructureUser .= "VALUES (:idStructure, :idUser) ";
		$associateStructureUserResult = $this->container->db->query($associateStructureUser, $datas);
		return $response->withStatus(200)
        				->write(json_encode($associateStructureUserResult,JSON_NUMERIC_CHECK));
	}

	public function deleteStructureUser(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteStructureUser = "DELETE FROM users_structures ";
		$deleteStructureUser .= "WHERE idStructure = :idStructure AND idUser = :idUser";
		$deleteStructureUserResult = $this->container->db->query($deleteStructureUser, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteStructureUserResult,JSON_NUMERIC_CHECK));
	}
}