<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;


	//Admin App group route
	$app->group('/Admin', function () use ($app) {
	    //Check Login 
		$app->post('/checkLogin', '\AdminUsersController:checkLogin');

		//Get Groups 
		$app->get('/getGroups', '\AdminGroupsController:getGroups');
		//Create Group 
		$app->post('/createGroup', '\AdminGroupsController:createGroup');
		//Update Group 
		$app->post('/updateGroup', '\AdminGroupsController:updateGroup');
		//Delete Group 
		$app->post('/deleteGroup', '\AdminGroupsController:deleteGroup');
		//Associate Group and User
		$app->post('/associateGroupUser', '\AdminGroupsController:associateGroupUser');
		//Delete Group User
		$app->post('/deleteGroupUser', '\AdminGroupsController:deleteGroupUser');
		//Get Groups in User
		$app->get('/getGroupsInUser', '\AdminGroupsController:getGroupsInUser');
		//Associate Group and User
		$app->post('/associateGroupApp', '\AdminGroupsController:associateGroupApp');
		//Delete Group User
		$app->post('/deleteGroupApp', '\AdminGroupsController:deleteGroupApp');
		//Get Groups in App
		$app->get('/getGroupsInApp', '\AdminGroupsController:getGroupsInApp');

		//Get Features 
		$app->get('/getFeatures', '\AdminFeaturesController:getFeatures');
		//Get Features by apps
		$app->get('/getFeaturesByApps', '\AdminFeaturesController:getFeaturesByApps');
		//Create Feature 
		$app->post('/createFeature', '\AdminFeaturesController:createFeature');
		//Update Feature 
		$app->post('/updateFeature', '\AdminFeaturesController:updateFeature');
		//Delete Feature 
		$app->post('/deleteFeature', '\AdminFeaturesController:deleteFeature');
		//Get Features in group
		$app->get('/getFeaturesInGroup', '\AdminFeaturesController:getFeaturesInGroup');
		//Associate Feature and Group
		$app->post('/associateFeatureGroup', '\AdminFeaturesController:associateFeatureGroup');
		//Delete Feature Group
		$app->post('/deleteFeatureGroup', '\AdminFeaturesController:deleteFeatureGroup');

		//Get Apps 
		$app->get('/getApps', '\AdminAppsController:getApps');
		//Create App 
		$app->post('/createApp', '\AdminAppsController:createApp');
		//Update App 
		$app->post('/updateApp', '\AdminAppsController:updateApp');
		//Delete App 
		$app->post('/deleteApp', '\AdminAppsController:deleteApp');
		//Get Apps in User
		$app->get('/getAppsInUser', '\AdminAppsController:getAppsInUser');

		//Get Users 
		$app->get('/getUsers', '\AdminUsersController:getUsers');
		//Create User 
		$app->post('/createUser', '\AdminUsersController:createUser');
		//Update User 
		$app->post('/updateUser', '\AdminUsersController:updateUser');
		//Delete User 
		$app->post('/deleteUser', '\AdminUsersController:deleteUser');
		//Get Users in App
		$app->get('/getUsersInApp', '\AdminUsersController:getUsersInApp');
		//Associate User and App
		$app->post('/associateUserApp', '\AdminUsersController:associateUserApp');
		//Delete User App
		$app->post('/deleteUserApp', '\AdminUsersController:deleteUserApp');
		//Get Users in Structure
		$app->get('/getUsersInStructure', '\AdminUsersController:getUsersInStructure');
		//Associate User and Structure
		$app->post('/associateUserStructure', '\AdminUsersController:associateUserStructure');
		//Delete User Structure
		$app->post('/deleteUserStructure', '\AdminUsersController:deleteUserStructure');
		//Get User Features
		$app->get('/getUserFeatures', '\AdminUsersController:getUserFeatures');
		//Get Users in Client
		$app->get('/getUsersInClient', '\AdminUsersController:getUsersInClient');
		//Associate User and Client
		$app->post('/associateUserClient', '\AdminUsersController:associateUserClient');
		//Delete User Client
		$app->post('/deleteUserClient', '\AdminUsersController:deleteUserClient');

		//Get Structures 
		$app->get('/getStructures', '\AdminStructuresController:getStructures');
		//Create Structure 
		$app->post('/createStructure', '\AdminStructuresController:createStructure');
		//Update Structure 
		$app->post('/updateStructure', '\AdminStructuresController:updateStructure');
		//Delete Structure 
		$app->post('/deleteStructure', '\AdminStructuresController:deleteStructure');
		//Associate Structure and Client
		$app->post('/associateStructureClient', '\AdminStructuresController:associateStructureClient');
		//Get Structures in Client
		$app->get('/getStructuresInClient', '\AdminStructuresController:getStructuresInClient');
		//Associate Structure and User
		$app->post('/associateStructureUser', '\AdminStructuresController:associateStructureUser');
		//Get Structures in User
		$app->get('/getStructuresInUser', '\AdminStructuresController:getStructuresInUser');
		//Delete Structure Client
		$app->post('/deleteStructureClient', '\AdminStructuresController:deleteStructureClient');
		//Delete Structure User
		$app->post('/deleteStructureUser', '\AdminStructuresController:deleteStructureUser');

		//Get Clients 
		$app->get('/getClients', '\AdminClientsController:getClients');
		//Create Client 
		$app->post('/createClient', '\AdminClientsController:createClient');
		//Update Client 
		$app->post('/updateClient', '\AdminClientsController:updateClient');
		//Delete Client 
		$app->post('/deleteClient', '\AdminClientsController:deleteClient');
		//Get Clients in Structure
		$app->get('/getClientsInStructure', '\AdminClientsController:getClientsInStructure');
		//Associate Structure and User
		$app->post('/associateClientStructure', '\AdminClientsController:associateClientStructure');
		//Delete Structure User
		$app->post('/deleteClientStructure', '\AdminClientsController:deleteClientStructure');


	});//->add(new IsSessionAliveMiddleware());