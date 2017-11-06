<?php

	/*$app->add(new \Slim\Middleware\Session([
	  'lifetime' => '2 minutes',
	  'autorefresh' => false
	]));*/

	$container = $app->getContainer();

	// Register globally to app
	$container['session'] = function ($c) {
	  return new \SlimSession\Helper;
	};