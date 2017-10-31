<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	class IsSessionAliveMiddleware {

		private $container;

	    public function __construct($container) {
	        $this->container = $container;
	    }

		public function __invoke(Request $request, Response $response, $next){
			$session = $this->container->session;
			$sessionExist = $session->exists('EcrSession');
			if($sessionExist){
			 	$response = $next($request, $response);
			}else{
				$response->write("Ciao amigo");
			}
			return $response;
		}
	}