<?php 

namespace App\Support\Api;

use Illuminate\Routing\RouteCollection; 

class ApiManager
{

	/*
    |--------------------------------------------------------------------------
    | Api Manager
    |--------------------------------------------------------------------------
    |
    | Responsible for all api management. Avoids placing api logic directly into the controller.
    |
    */

	public function __construct()
	{
		
		$this->routes = collect(app()->routes->getRoutes())->filter(function ($route) {

			return $route->action['prefix'] === 'api' && $route->uri !== 'api';

		})->reduce(function ($reduced, $route) {

			$reduced[$route->action['as']] = [
				'uri' => $route->uri,
				'name' => $route->action['as'],
				'methods' => $route->methods
			];

			return $reduced;

		}, []);

	}

	public function getEndpoints()
	{
		return $this->routes;

	}
}