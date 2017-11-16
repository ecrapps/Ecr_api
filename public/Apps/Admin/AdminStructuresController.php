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
		// Delete all structure-user associations from users_structures
		$deleteStructureUsers = "DELETE FROM users_structures WHERE idStructure = :idStructure ";
		$deleteStructureUsersResult = $this->container->db->query($deleteStructureUsers, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteStructureResult,JSON_NUMERIC_CHECK));
	}

	public function getStructuresInUser(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getStructuresUsers = "SELECT us.idStructure, s.name ";
		$getStructuresUsers .= "FROM users_structures as us ";
		$getStructuresUsers .= "INNER JOIN structures as s ON s.id=us.idStructure ";
		$getStructuresUsers .= "WHERE us.idUser = :idUser ";
		$getStructuresUsersResult = $this->container->db->query($getStructuresUsers, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getStructuresUsersResult,JSON_NUMERIC_CHECK));
	}
}