<?php

	require '../vendor/autoload.php';

	session_start();
	
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	//Db configuration
	require __DIR__ . '/Settings/settings.php';
	$app = new \Slim\App(["settings" => $config]);

	//Instanciate middlewares
	require __DIR__ . '/Apps/Garile/middleware.php';

	//Setting conten type header
	$app->add(function ($req, $res, $next) {
	    $response = $next($req, $res);
	    return $response
	            ->withHeader('Access-Control-Allow-Origin', '*')
	            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
	            ->withHeader('Content-Type', 'application/json')
	            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	});

	//Adding dependencies
	require __DIR__ . '/Dependencies/dependencies.php';

	//Garile app
	require __DIR__ . '/Apps/Garile/ticketsController.php';
	require __DIR__ . '/Apps/Garile/routes.php';

	//Admin app
	require __DIR__ . '/Apps/Admin/AdminUsersController.php';
	require __DIR__ . '/Apps/Admin/AdminGroupsController.php';
	require __DIR__ . '/Apps/Admin/AdminStructuresController.php';
	require __DIR__ . '/Apps/Admin/AdminAppsController.php';
	require __DIR__ . '/Apps/Admin/AdminFeaturesController.php';
	require __DIR__ . '/Apps/Admin/AdminClientsController.php';
	require __DIR__ . '/Apps/Admin/Routes.php';

	//Goper app
	require __DIR__ . '/Apps/Goper/GoperController.php';
	require __DIR__ . '/Apps/Goper/GoperImportController.php';
	require __DIR__ . '/Apps/Goper/Routes.php';

	$app->run();