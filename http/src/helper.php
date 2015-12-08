<?php

/* http helper functions */

use Simplified\Http\Route;

function route($name) {
	$routes = Route::getCollection();
	if (isset($routes[$name])) {
		return $routes[$name]['path'];
	}
	
	throw new \Exception("No route named $name found");
}