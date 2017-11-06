<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	//Goper App group route
	$app->group('/Goper', function () use ($app) {
	    //Check Login 
		$app->post('/checkLogin', '\GoperTasksController:checkLogin');

		//Get TrainIds 
		$app->get('/getTrainIds', '\GoperTrainIdController:getTrainIds');
		//Create TrainId 
		$app->post('/createTrainId', '\GoperTrainIdController:createTrainId');
		//Update TrainId 
		$app->post('/updateTrainId', '\GoperTrainIdController:updateTrainId');
		//Delete TrainId 
		$app->post('/deleteTrainId', '\GoperTrainIdController:deleteTrainId');

		//Get Tasks 
		$app->get('/getTasks', '\GoperTasksController:getTasks');
		//Get Task
		$app->get('/getTask', '\GoperTasksController:getTask');
		//Create Task 
		$app->post('/createTask', '\GoperTasksController:createTask');
		//Update Task 
		$app->post('/updateTask', '\GoperTasksController:updateTask');
		//Delete Task 
		$app->post('/deleteTask', '\GoperTasksController:deleteTask');
		// Get Task type
		$app->get('/getTaskTypes', '\GoperTasksController:getTaskTypes');

		$app->post('/associateClientTask', '\GoperTasksController:associateClientTask');
		$app->post('/deleteClientTask', '\GoperTasksController:deleteClientTask');
		$app->get('/getClientsInTask', '\GoperTasksController:getClientsInTask');

		$app->post('/associateTrainTask', '\GoperTasksController:associateTrainTask');
		$app->post('/deleteTrainTask', '\GoperTasksController:deleteTrainTask');
		$app->get('/getTrainsInTask', '\GoperTasksController:getTrainsInTask');

		$app->post('/associateCommonTask', '\GoperTasksController:associateCommonTask');
		$app->post('/deleteCommonTask', '\GoperTasksController:deleteCommonTask');

		$app->post('/associateCancellationTask', '\GoperTasksController:associateCancellationTask');
		$app->post('/deleteCancellationTask', '\GoperTasksController:deleteCancellationTask');

		$app->post('/associateMdTask', '\GoperTasksController:associateMdTask');
		$app->post('/deleteMdTask', '\GoperTasksController:deleteMdTask');

		//Get Daily Tasks 
		$app->get('/getDailyTasks', '\GoperDailyTasksController:getDailyTasks');
		$app->get('/getHistoryDailyTasks', '\GoperDailyTasksController:getHistoryDailyTasks');
		// Update checked/unchecked Task 
		$app->post('/updateTaskCheck', '\GoperDailyTasksController:updateTaskCheck');

		$app->post('/saveNewComment', '\GoperDailyTasksController:saveNewComment');

		// Import
		$app->post('/import', '\GoperImportController:import');

	}); //->add(new IsSessionAliveMiddleware($container));