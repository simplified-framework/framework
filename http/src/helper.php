<?php

/* http helper functions */

use Simplified\Http\Route;
use Simplified\Http\ResourceNotFoundException;

function route($name) {
	$routes = Route::getCollection();
	if (isset($routes[$name])) {
		$item = $routes[$name]['path'];
		if (func_num_args() == 2) {
			$params = func_get_arg(1);
            $keys = array_keys($params);

            foreach ($keys as $key) {
                $item = str_replace("{".$key."}", $params[$key], $item);
            }
		}
        return $item;
	}

    var_dump($routes);
	throw new ResourceNotFoundException("No route named $name found. " . $routes->count());
}