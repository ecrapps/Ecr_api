<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;


	//Goper App group route
	$app->group('/Goper', function () use ($app) {
	    //Check Login 
		$app->post('/checkLogin', '\GoperController:checkLogin');

		//Get TrainIds 
		$app->get('/getTrainIds', '\GoperController:getTrainIds');
		//Create TrainId 
		$app->post('/createTrainId', '\GoperController:createTrainId');
		//Update TrainId 
		$app->post('/updateTrainId', '\GoperController:updateTrainId');
		//Delete TrainId 
		$app->post('/deleteTrainId', '\GoperController:deleteTrainId');

		//Get Tasks 
		$app->get('/getTasks', '\GoperController:getTasks');
		//Create Task 
		$app->post('/createTask', '\GoperController:createTask');
		//Update Task 
		$app->post('/updateTask', '\GoperController:updateTask');
		//Delete Task 
		$app->post('/deleteTask', '\GoperController:deleteTask');
		// Get Task type
		$app->get('/getTaskTypes', '\GoperController:getTaskTypes');

		$app->post('/associateClientTask', '\GoperController:associateClientTask');
		$app->post('/deleteClientTask', '\GoperController:deleteClientTask');
		$app->get('/getClientsInTask', '\GoperController:getClientsInTask');

		$app->post('/associateTrainTask', '\GoperController:associateTrainTask');
		$app->post('/deleteTrainTask', '\GoperController:deleteTrainTask');
		$app->get('/getTrainsInTask', '\GoperController:getTrainsInTask');

		$app->post('/associateCommonTask', '\GoperController:associateCommonTask');
		$app->post('/deleteCommonTask', '\GoperController:deleteCommonTask');

		$app->post('/associateCancellationTask', '\GoperController:associateCancellationTask');
		$app->post('/deleteCancellationTask', '\GoperController:deleteCancellationTask');

		$app->post('/associateMdTask', '\GoperController:associateMdTask');
		$app->post('/deleteMdTask', '\GoperController:deleteMdTask');

		// Import
		$app->post('/import', '\GoperImportController:import');

	});//->add(new IsSessionAliveMiddleware());