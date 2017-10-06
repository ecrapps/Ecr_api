<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;


	//Garile App group route
	$app->group('/Garile', function () use ($app) {
	    //Get Tickets 
		$app->get('/getTickets', '\TicketsController:getTickets');

		//Get Messages
		$app->get('/getMessages', '\TicketsController:getMessages');

		//Send new message
		$app->post('/sendMessage', '\TicketsController:sendMessage');

		//Get subjects
		$app->get('/getSubjects', '\TicketsController:getSubjects');

		//Delete ticket
		$app->delete('/deleteTicket', '\TicketsController:deleteTicket');

		//Create ticket
		$app->post('/createTicket', '\TicketsController:createTicket');

		//Update ticket status
		$app->post('/updateStatus', '\TicketsController:updateStatus');

		//Get user infos
		$app->get('/getUser', '\TicketsController:getUser');

		//Get users infos
		$app->get('/getUsers', '\TicketsController:getUsers');

	});//->add(new IsSessionAliveMiddleware());