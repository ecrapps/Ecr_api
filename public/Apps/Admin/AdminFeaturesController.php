<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AdminFeaturesController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	//								Features							//
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	public function getFeatures(Request $request, Response $response, $args){
		$getFeatures = "SELECT id, name ";
		$getFeatures .= "FROM features ORDER BY name";
		$getFeaturesResult = $this->container->db->query($getFeatures);
		return $response->withStatus(200)
        				->write(json_encode($getFeaturesResult,JSON_NUMERIC_CHECK));
	}

	public function getFeaturesByApps(Request $request, Response $response, $args){
		$getFeaturesByApps = "SELECT a.id as idApp, ";
		$getFeaturesByApps .= "a.name as nameApp, ";
		$getFeaturesByApps .= "f.id as idFeature, ";
		$getFeaturesByApps .= "f.name as nameFeature ";
		$getFeaturesByApps .= "FROM apps as a, features as f ";
		$getFeaturesByApps .= "WHERE a.id=f.idApp ";
		$getFeaturesByApps .= "ORDER BY a.name, f.name ";
		$getFeaturesByAppsResult = $this->container->db->query($getFeaturesByApps);
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

	public function createFeature(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$createFeature = "INSERT INTO features (name, idApp) VALUES (:featureName, :idApp) ";
		$createFeatureResult = $this->container->db->query($createFeature, $datas);
		return $response->withStatus(200)
        				->write(json_encode($createFeatureResult,JSON_NUMERIC_CHECK));
	}

	public function updateFeature(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$updateFeature = "UPDATE features SET name = :featureName WHERE id = :idFeature ";
		$updateFeatureResult = $this->container->db->query($updateFeature, $datas);
		return $response->withStatus(200)
        				->write(json_encode($updateFeatureResult,JSON_NUMERIC_CHECK));
	}

	public function deleteFeature(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteFeature = "DELETE FROM features WHERE id = :idFeature ";
		$deleteFeatureResult = $this->container->db->query($deleteFeature, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteFeatureResult,JSON_NUMERIC_CHECK));
	}

	public function associateFeatureGroup(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$associateFeatureGroup = "INSERT INTO groups_features (idFeature, idGroup) ";
		$associateFeatureGroup .= "VALUES (:idFeature, :idGroup) ";
		$associateFeatureGroupResult = $this->container->db->query($associateFeatureGroup, $datas);
		return $response->withStatus(200)
        				->write(json_encode($associateFeatureGroupResult,JSON_NUMERIC_CHECK));
	}

	public function deleteFeatureGroup(Request $request, Response $response, $args){
		$getParsedBody = $request->getParsedBody();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getParsedBody), FALSE);
		$deleteFeatureGroup = "DELETE FROM groups_features WHERE idFeature = :idFeature AND idGroup = :idGroup";
		$deleteFeatureGroupResult = $this->container->db->query($deleteFeatureGroup, $datas);
		return $response->withStatus(200)
        				->write(json_encode($deleteFeatureGroupResult,JSON_NUMERIC_CHECK));
	}

	public function getFeaturesInGroup(Request $request, Response $response, $args){
		$getQueryParams = $request->getQueryParams();
		$datas = new stdClass();
		$datas->params = json_decode(json_encode($getQueryParams), FALSE);
		$getFeaturesGroups = "SELECT idFeature ";
		$getFeaturesGroups .= "FROM groups_features WHERE idGroup = :idGroup ";
		$getFeaturesGroupsResult = $this->container->db->query($getFeaturesGroups, $datas);
		return $response->withStatus(200)
        				->write(json_encode($getFeaturesGroupsResult,JSON_NUMERIC_CHECK));
	}
}