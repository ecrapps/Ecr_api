<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	class IsSessionAliveMiddleware {

		public function __invoke(Request $request, Response $response, $next){
			$response->write ('<h1>Bienvenue</h1>');
			$response = $next($request, $response);
			$response->write('<h1>Au revoir</h1>');
			return $response;
		}
	}