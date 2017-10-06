<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	class IsSessionAliveMiddleware {

		public function __invoke(Request $request, Response $response, $next){
			// return $response;
		}
	}