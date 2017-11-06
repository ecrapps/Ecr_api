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
		// Update checked/unchecked Task 
		$app->post('/updateTaskCheck', '\GoperDailyTasksController:updateTaskCheck');
		// Get Task type
		$app->get('/getTaskTypes', '\GoperTasksController:getTaskTypes');

		$app->post('/saveNewComment', '\GoperDailyTasksController:saveNewComment');

		//Get Daily Tasks 
		$app->get('/getDailyTasks', '\GoperDailyTasksController:getDailyTasks');
		$app->get('/getHistoryDailyTasks', '\GoperDailyTasksController:getHistoryDailyTasks');

		$app->post('/associateClientTask', '\GoperDailyTasksController:associateClientTask');
		$app->post('/deleteClientTask', '\GoperDailyTasksController:deleteClientTask');
		$app->get('/getClientsInTask', '\GoperDailyTasksController:getClientsInTask');

		$app->post('/associateTrainTask', '\GoperDailyTasksController:associateTrainTask');
		$app->post('/deleteTrainTask', '\GoperDailyTasksController:deleteTrainTask');
		$app->get('/getTrainsInTask', '\GoperDailyTasksController:getTrainsInTask');

		$app->post('/associateCommonTask', '\GoperDailyTasksController:associateCommonTask');
		$app->post('/deleteCommonTask', '\GoperDailyTasksController:deleteCommonTask');

		$app->post('/associateCancellationTask', '\GoperDailyTasksController:associateCancellationTask');
		$app->post('/deleteCancellationTask', '\GoperDailyTasksController:deleteCancellationTask');

		$app->post('/associateMdTask', '\GoperDailyTasksController:associateMdTask');
		$app->post('/deleteMdTask', '\GoperDailyTasksController:deleteMdTask');

		// Import
		$app->post('/import', '\GoperImportController:import');

	}); //->add(new IsSessionAliveMiddleware($container));