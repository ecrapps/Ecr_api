<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AdminGroupsController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	//								Groups								//
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	public function getGroups(Request $request, Response $response, $args){
		$getGroups = "SELECT id, name ";
		$getGroups .= "FROM groups ORDER BY name";
		$getGroupsResult = $this->container->db->query($getGroups);
		return $response->withStatus(200)
        				->write(json_encode($getGroupsResult,JSON_NUMERIC_CHECK));
	}

	public function createGroup(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$createGroup = "INSERT INTO groups (name) VALUES (:groupName) ";
		$createGroupResult = $this->container->db->query($createGroup, $datas);
		return $response->withStatus(200)
        				->write(json_encode($createGroupResult,JSON_NUMERIC_CHECK));
	}

	public function updateGroup(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$updateGroup = "UPDATE groups SET name = :groupName WHERE id = :idGroup ";
		$updateGroupResult = $this->container->db->query($updateGroup, $datas);
		return $response->withStatus(200)
        				->write(json_encode($updateGroupResult,JSON_NUMERIC_CHECK));
	}

	public function deleteGroup(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteGroup = "DELETE FROM groups WHERE id = :idGroup ";
		$deleteGroupResult = $this->container->db->query($deleteGroup, $datas);
		// Delete all group-features associations from groups_features
		$deleteGroupFeatures = "DELETE FROM groups_features WHERE idGroup = :idGroup ";
		$deleteGroupFeaturesResult = $this->container->db->query($deleteGroupFeatures, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteGroupResult,JSON_NUMERIC_CHECK));
	}

	public function associateGroupUser(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$associateGroupUser = "INSERT INTO users_groups (idGroup, idUser) ";
		$associateGroupUser .= "VALUES (:idGroup, :idUser) ";
		$associateGroupUserResult = $this->container->db->query($associateGroupUser, $datas);
		return $response->withStatus(200)
        				->write(json_encode($associateGroupUserResult,JSON_NUMERIC_CHECK));
	}

	public function deleteGroupUser(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteGroupUser = "DELETE FROM users_groups ";
		$deleteGroupUser .= "WHERE idGroup = :idGroup AND idUser = :idUser";
		$deleteGroupUserResult = $this->container->db->query($deleteGroupUser, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteGroupUserResult,JSON_NUMERIC_CHECK));
	}

	public function getGroupsInUser(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getGroupsUsers = "SELECT ug.idGroup, g.name ";
		$getGroupsUsers .= "FROM users_groups as ug ";
		$getGroupsUsers .= "INNER JOIN groups as g ON g.id=ug.idGroup ";
		$getGroupsUsers .= "WHERE ug.idUser = :idUser ";
		$getGroupsUsersResult = $this->container->db->query($getGroupsUsers, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getGroupsUsersResult,JSON_NUMERIC_CHECK));
	}
}