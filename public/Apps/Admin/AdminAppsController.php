<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AdminAppsController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	//								Apps								//
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	public function getApps(Request $request, Response $response, $args){
		$getApps = "SELECT id, name ";
		$getApps .= "FROM apps ORDER BY name";
		$getAppsResult = $this->container->db->query($getApps);
		return $response->withStatus(200)
        				->write(json_encode($getAppsResult,JSON_NUMERIC_CHECK));
	}

	public function createApp(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$createApp = "INSERT INTO apps (name) VALUES (:appName) ";
		$createAppResult = $this->container->db->query($createApp, $datas);
		return $response->withStatus(200)
        				->write(json_encode($createAppResult,JSON_NUMERIC_CHECK));
	}

	public function updateApp(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$updateApp = "UPDATE apps SET name = :appName WHERE id = :idApp ";
		$updateAppResult = $this->container->db->query($updateApp, $datas);
		return $response->withStatus(200)
        				->write(json_encode($updateAppResult,JSON_NUMERIC_CHECK));
	}

	public function deleteApp(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteApp = "DELETE FROM apps WHERE id = :idApp ";
		$deleteAppResult = $this->container->db->query($deleteApp, $datas);
		// Delete all app-users associations from users_apps
		$deleteAppUsers = "DELETE FROM groups_apps WHERE idApp = :idApp ";
		$deleteAppUsersResult = $this->container->db->query($deleteAppUsers, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteAppResult,JSON_NUMERIC_CHECK));
	}

	public function getAppsInUser(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getAppsUsers = "SELECT ua.idApp, a.name ";
		$getAppsUsers .= "FROM users_apps as ua ";
		$getAppsUsers .= "INNER JOIN apps as a ON a.id=ua.idApp ";
		$getAppsUsers .= "WHERE ua.idUser = :idUser ";
		$getAppsUsersResult = $this->container->db->query($getAppsUsers, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getAppsUsersResult,JSON_NUMERIC_CHECK));
	}

}