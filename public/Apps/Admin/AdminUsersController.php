<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AdminUsersController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	public function checkLogin(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$datas->params->password = hash('sha256', $datas->params->password);
		$checkLogin = "SELECT id ";
		$checkLogin .= "FROM users ";
		$checkLogin .= "WHERE name = :login ";
		$checkLogin .= "AND password = :password ";
		$checkLoginResult = $this->container->db->query($checkLogin, $datas);
		$responseLogin = new stdClass();
		if ($checkLoginResult) {
			$responseLogin->loginSucceed = true;
			$responseLogin->idUser = $checkLoginResult[0]['id'];
		}
		else
			$responseLogin->loginSucceed = false;
		return $response->withStatus(200)
        				->write(json_encode($responseLogin,JSON_NUMERIC_CHECK));
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	//								Users								//
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	public function getUsers(Request $request, Response $response, $args){
		$getUsers = "SELECT id, name, username, email, status ";
		$getUsers .= "FROM users ORDER BY name ";
		$getUsersResult = $this->container->db->query($getUsers);
		return $response->withStatus(200)
        				->write(json_encode($getUsersResult,JSON_NUMERIC_CHECK));
	}

	public function createUser(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$createUser = "INSERT INTO users (name, username, email, status) ";
		$createUser .= "VALUES (:name, :username, :email, :status) ";
		$createUserResult = $this->container->db->query($createUser, $datas);
		return $response->withStatus(200)
        				->write(json_encode($createUserResult,JSON_NUMERIC_CHECK));
	}

	public function updateUser(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$updateUser = "UPDATE users ";
		$updateUser .= "SET name = :name, ";
		$updateUser .= "username = :username, ";
		$updateUser .= "email = :email, ";
		$updateUser .= "status = :status ";
		$updateUser .= "WHERE id = :id ";
		$updateUserResult = $this->container->db->query($updateUser, $datas);
		return $response->withStatus(200)
        				->write(json_encode($updateUserResult,JSON_NUMERIC_CHECK));
	}

	public function deleteUser(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteUser = "DELETE FROM users WHERE id = :idUser ";
		$deleteUserResult = $this->container->db->query($deleteUser, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteUserResult,JSON_NUMERIC_CHECK));
	}

	public function associateUserApp(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$associateUserApp = "INSERT INTO users_apps (idUser, idApp) ";
		$associateUserApp .= "VALUES (:idUser, :idApp) ";
		$associateUserAppResult = $this->container->db->query($associateUserApp, $datas);
		return $response->withStatus(200)
        				->write(json_encode($associateUserAppResult,JSON_NUMERIC_CHECK));
	}

	public function deleteUserApp(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteUserApp = "DELETE FROM users_apps ";
		$deleteUserApp .= "WHERE idUser = :idUser AND idApp = :idApp";
		$deleteUserAppResult = $this->container->db->query($deleteUserApp, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteUserAppResult,JSON_NUMERIC_CHECK));
	}

	public function getUsersInApp(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getUsersApps = "SELECT idUser ";
		$getUsersApps .= "FROM users_apps WHERE idApp = :idApp ";
		$getUsersAppsResult = $this->container->db->query($getUsersApps, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getUsersAppsResult,JSON_NUMERIC_CHECK));
	}

	public function associateUserStructure(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$associateUserStructure = "INSERT INTO users_structures (idUser, idStructure) ";
		$associateUserStructure .= "VALUES (:idUser, :idStructure) ";
		$associateUserStructureResult = $this->container->db->query($associateUserStructure, $datas);
		return $response->withStatus(200)
        				->write(json_encode($associateUserStructureResult,JSON_NUMERIC_CHECK));
	}

	public function deleteUserStructure(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteUserStructure = "DELETE FROM users_structures ";
		$deleteUserStructure .= "WHERE idUser = :idUser AND idStructure = :idStructure";
		$deleteUserStructureResult = $this->container->db->query($deleteUserStructure, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteUserStructureResult,JSON_NUMERIC_CHECK));
	}

	public function getUsersInStructure(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getUsersStructures = "SELECT idUser ";
		$getUsersStructures .= "FROM users_structures WHERE idStructure = :idStructure ";
		$getUsersStructuresResult = $this->container->db->query($getUsersStructures, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getUsersStructuresResult,JSON_NUMERIC_CHECK));
	}

	public function getUserFeatures(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getFeaturesByApps = "SELECT DISTINCT a.id as idApp, ";
		$getFeaturesByApps .= "a.name as nameApp, ";
		$getFeaturesByApps .= "f.id as idFeature, ";
		$getFeaturesByApps .= "f.name as nameFeature ";
		$getFeaturesByApps .= "FROM users_apps as ua, ";
		$getFeaturesByApps .= "apps as a, ";
		$getFeaturesByApps .= "features as f, ";
		$getFeaturesByApps .= "groups_features as gf, ";
		$getFeaturesByApps .= "users_groups as ug ";
		$getFeaturesByApps .= "WHERE ua.idUser = :idUser ";
		$getFeaturesByApps .= "AND a.id = ua.idApp ";
		$getFeaturesByApps .= "AND a.id=f.idApp ";
		$getFeaturesByApps .= "AND f.id=gf.idFeature ";
		$getFeaturesByApps .= "AND gf.idGroup=ug.idGroup ";
		$getFeaturesByApps .= "AND ug.idUser = ua.idUser ";
		$getFeaturesByApps .= "ORDER BY a.name, f.name ";
		$getFeaturesByAppsResult = $this->container->db->query($getFeaturesByApps, $datas);
		if ($getFeaturesByAppsResult) {
			$oldIdApp = 0;
			$nbApp = -1;
			$result = null;
			foreach ($getFeaturesByAppsResult as $line) {
				if ($oldIdApp != $line['idApp']) {
					$nbApp++;
					$result[$nbApp] = new stdClass();
					$result[$nbApp]->name = $line['nameApp'];
					$result[$nbApp]->features = [];
					$nbFeature = 0;
					$oldIdApp = $line['idApp'];
				}
				$result[$nbApp]->features[$nbFeature] = new stdClass();
				$result[$nbApp]->features[$nbFeature]->id = $line['idFeature'];
				$result[$nbApp]->features[$nbFeature]->name = $line['nameFeature'];
				$nbFeature++;
			}
		}
		return $response->withStatus(200)
        				->write(json_encode($result,JSON_NUMERIC_CHECK));
	}
	
	public function associateUserClient(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$associateUserClient = "INSERT INTO users_clients (idUser, idClient) ";
		$associateUserClient .= "VALUES (:idUser, :idClient) ";
		$associateUserClientResult = $this->container->db->query($associateUserClient, $datas);
		return $response->withStatus(200)
        				->write(json_encode($associateUserClientResult,JSON_NUMERIC_CHECK));
	}

	public function deleteUserClient(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteUserClient = "DELETE FROM users_clients ";
		$deleteUserClient .= "WHERE idUser = :idUser AND idClient = :idClient";
		$deleteUserClientResult = $this->container->db->query($deleteUserClient, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteUserClientResult,JSON_NUMERIC_CHECK));
	}

	public function getUsersInClient(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getUsersClients = "SELECT idUser ";
		$getUsersClients .= "FROM users_clients WHERE idClient = :idClient ";
		$getUsersClientsResult = $this->container->db->query($getUsersClients, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getUsersClientsResult,JSON_NUMERIC_CHECK));
	}
}