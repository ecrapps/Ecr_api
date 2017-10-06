<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class GoperImportController {

	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	function import(Request $request, Response $response, $args){
		$uploadedFiles = $request->getUploadedFiles();
		print_r($uploadedFiles['file']->file);
		$file = fopen($uploadedFiles['file']->file, "r");
		while (($data = fgetcsv($file, 0, ";")) !== FALSE) {
			print_r($data);
		}
	}
}