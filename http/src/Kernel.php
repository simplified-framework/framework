<?php

namespace Simplified\Http;

use Simplified\Core\Collection;
use Simplified\Core\IllegalArgumentException;
use Simplified\Debug\Debug;

// handle debug
Debug::handleDebug();

// load configured routes
require CONFIG_PATH . 'routes.php';

class Kernel {
    private $routes;
    public function __construct() {
        // load declared routes
        $routes = Route::getCollection();

        if ($routes->count() == 0)
            throw new \ErrorException('Unable to load routes from configuration directory.');

        $this->routes = $routes;
    }

    public function handleRequest() {
        $req = Request::createFromGlobals();
        $path = $req->path();
        $current_route = null;

        foreach ($this->routes->toArray() as $key => $route) {
            $route_path = $route['path'];

            // current route is equal to configured route
            if ($route_path == $path) {
                if ($route['method'] != $req->method())
                    throw new MethodNotAllowedException("Method " . $req->method() . " not allowed.");

                $current_route = $route;
                break;
            }

            // find route with regex
            $matches = array();
            // check if route has a condition to match
            // if so, loop through conditions and replace theme here
            // else, use standard pattern
            $pattern = str_replace("/", "\\/", $route_path);
            if ( count($route['conditions']) > 0 ) {
                foreach ($route['conditions'] as $key => $val) {
                    $pattern = str_replace("{".$key."}", "($val)", $pattern);
                }
            } else {
                $pattern = preg_replace('/(\{[a-zA-Z]+\})/', "([a-zA-Z0-9-_]+)", $pattern);
            }

            // compile pattern
            preg_match('/'.$pattern.'$/', $path, $matches);

            // current route can be translated to regex pattern
            if (count($matches) >= 2 && !empty($matches[1])) {
                if ($route['method'] != $req->method())
                    throw new MethodNotAllowedException("Method " . $req->method() . " not allowed.");

                array_shift($matches); // remove first element

                print "<p>pattern $pattern matched:</p>";
                print "<p><pre>";
                var_dump($matches);
                print "</pre></p>";
                $current_route = $route;
                break;
            }
        }

        // TODO if route was not found, throw 404 exception
        if ($current_route == null)
            throw new ResourceNotFoundException('Route not found: ' . $path);

        // if we use a closure, call them with the request object
        if ($current_route['closure']) {
            // TODO catch content
            $ref = new \ReflectionFunction ($current_route['closure']);
            if ($ref->getNumberOfParameters() > 0) {
                $current_route['closure'] ($req);
            }
            else {
                $current_route['closure'] ();
            }

            return;
        }

        // we use a controller, so do checks and throw if something is wrong (class or method)
        if (empty($current_route['controller']))
            throw new IllegalArgumentException('No closure or controller was set for route ' . $current_route['path']);

        $parts = explode("@", $current_route['controller']);
        $controller = "App\\Controllers\\" . $parts[0];
        $method = $parts[1];

        if (!class_exists($controller))
            throw new ResourceNotFoundException("Unable to find controller $controller.");

        if (!method_exists($controller, $method))
            throw new ResourceNotFoundException("Unable to call $controller::$method()");

        // TODO catch content
        call_user_func(array(new $controller, $method));
    }
}