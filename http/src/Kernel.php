<?php

namespace Simplified\Http;

use Simplified\Core\IllegalArgumentException;
use Simplified\Debug\Debug;

// TODO dont simply cho return values, instead check response type

// handle debug
Debug::handleDebug();

// load configured routes
require CONFIG_PATH . 'routes.php';

class Kernel {
    public function handleRequest() {
    	ob_start ();

        // load declared routes
        $routes = RouteCollection::instance()->toArray();

        if ($routes == null || count($routes) == 0)
            throw new \ErrorException('Unable to load routes from configuration directory.');

        $req = Request::createFromGlobals();
        $path = $req->path();
        $current_route = null;
        $matches = array();

        foreach ($routes as $route) {
            $route_path = $route->path;
            // current route is equal to configured route
            if ($route_path === $path) {
                if ($route->method != $req->method())
                    throw new MethodNotAllowedException("Method " . $req->method() . " not allowed.");

                $current_route = $route;
                break;
            }

            // find route with regex
            $matches = array();
            $pattern = str_replace("/", "\\/", $route_path);
            if ( count($route->conditions) > 0 ) {
                foreach ($route->conditions as $key => $val) {
                    $pattern = str_replace("{".$key."}", "($val)", $pattern);
                }
            } else {
                $pattern = str_replace('{[a-zA-Z]+\}', "([a-zA-Z]+)", $pattern);
            }

            // compile pattern
            if (0 === preg_match('/'.$pattern.'/', $path, $matches)) {
                throw new ResourceNotFoundException('Route not found: ' . $path . " (Regex returned 0 for pattern '$pattern' with route $path)");
            }

            // compile pattern
            if (FALSE === preg_match('/'.$pattern.'/', $path, $matches)) {
                throw new ResourceNotFoundException('Route not found: ' . $path . " (Regex compiler error)");
            }

            // current route can be translated to regex pattern
            if (count($matches) >= 2 && !empty($matches[1])) {
                if ($route->method != $req->method())
                    throw new MethodNotAllowedException("Method " . $req->method() . " not allowed.");

                array_shift($matches); // remove first element
                $current_route = $route;
                break;
            }
        }

        // TODO if route was not found, throw 404 exception
        if ($current_route == null)
            throw new ResourceNotFoundException('Route not found: ' . $path);

        // if we use a closure, call them with the request object
        if ($current_route->closure) {
        	$content = null;
            $ref = new \ReflectionFunction ($current_route->closure);
            if ($ref->getNumberOfParameters() > 0) {
                $params = array();
                $first = $ref->getParameters()[0];
                if ($first->getClass() != null && strstr($first->getClass()->getName(), 'Request'))
                    $params[] = $req;

                foreach ($matches as $match) {
                    $params[] = $match;
                }

                $retval = call_user_func_array($current_route->closure, $params);
            }
            else {
                $retval = $current_route->closure();
            }
            
	        $clean_content = ob_get_clean ();
	        if ($clean_content != null) {
                // TODO create Response object
	        	print $clean_content;
	        }
	        if ($retval != null) {
                // TODO check for type string, array or Response object
	        	print $retval;
	        }

            return;
        }

        // we use a controller, so do checks and throw if something is wrong (class or method)
        if (!$current_route->controller) {
            throw new IllegalArgumentException('No controller was set for route ' . $current_route->path);
        }

        $parts = explode("@", $current_route->controller);
        $controller = "App\\Controllers\\" . $parts[0];
        $method = $parts[1];

        if (!class_exists($controller))
            throw new ResourceNotFoundException("Unable to find controller $controller.");

        if (!method_exists($controller, $method))
            throw new ResourceNotFoundException("Unable to call $controller::$method()");

        $ref = new \ReflectionClass ($controller);
        $num_params = $ref->getMethod($method)->getNumberOfParameters();
        $retval = null;
        
        if ($num_params > 0) {
            $ref_method = $ref->getMethod($method);
            $params = array();
            $first = $ref_method->getParameters()[0];
            if ($first->getClass() != null && strstr($first->getClass()->getName(), 'Request'))
                $params[] = $req;
            foreach ($matches as $match) {
                $params[] = $match;
            }

            $retval = call_user_func_array(array(new $controller, $method), $params);
        }
        else {
            $retval = call_user_func(array(new $controller, $method));
        }
        
        $clean_content = ob_get_clean ();
        if ($clean_content != null) {
            // TODO create Response object
        	print $clean_content;
        }
        if ($retval != null) {
            // TODO check for type string, array or Response object
        	print $retval;
        }
    }
}