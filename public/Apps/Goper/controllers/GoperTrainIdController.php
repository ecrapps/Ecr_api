<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class GoperTrainIdController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}

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
}