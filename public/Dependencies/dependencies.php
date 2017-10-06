<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$container = $app->getContainer();

	$container['pdo'] = function ($c){
		$db = $c['settings']['db'];
	    $pdo = new PDO("mysql:host=". $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
	    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	    return $pdo;
	};

	$container['db'] = function ($container){
		return new Database($container->pdo);
	};


	class Database {

		private $pdo;

		public function __construct(PDO $pdo){
			$this->pdo = $pdo;
		}

		public function bindValuesRequest($request, $params) {
			if ($params) {
				foreach ($params->params as $key => $value){
					$request->bindValue(':'.$key, $value);
				}
			}			
			return $request;
		}

		public function query($sql, $params = false){
			$req = $this->pdo->prepare($sql);
			$result = $this->bindValuesRequest($req, $params);
			$result->execute();
			if($result->columnCount() > 0)
				return $result->fetchAll();
			else
				return true;
		}
	}