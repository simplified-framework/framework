<?php

namespace Simplified\Http;
use Simplified\Core\Collection;
use Simplified\Core\IllegalArgumentException;

class Route {
    private static $routes;
    private static $instance;

    public static function get($uri, $arg2) {
        return self::registerRoute('get', $uri, $arg2);
    }

    public static function put($uri, $arg2) {
        return self::registerRoute('put', $uri, $arg2);
    }

    public static function post($uri, $arg2) {
        return self::registerRoute('post', $uri, $arg2);
    }

    public static function delete($uri, $arg2) {
        return self::registerRoute('delete', $uri, $arg2);
    }

    public function conditions(array $condition) {
        $keys = array_keys(self::$routes->toArray());
        $route = self::$routes[end($keys)];
        $route['conditions'] = $condition;

        self::$routes[end($keys)] = $route;
    }

    public static function getCollection() {
        if (self::$instance == null)
            self::$instance = new self();

        if (self::$routes == null)
            self::$routes = new Collection();

        return self::$routes;
    }

    private static function registerRoute($type, $uri, $arg2) {
        if (self::$instance == null)
            self::$instance = new self();

        if (self::$routes == null)
            self::$routes = new Collection();

        $controller = null;
        $closure = null;
        $routename = md5(microtime());

        if (is_array($arg2)) {
            if (isset($arg2['uses'])) {
                $controller = $arg2['uses'];
            }

            if (isset($arg2['as'])) {
                $routename = $arg2['as'];
            }
        }
        else
            if (is_string($arg2)) {
                $controller = $arg2;
            }
            else
                if (gettype($arg2) == 'object' && $arg2 instanceof \Closure) {
                    $closure = $arg2;
                }

        if ($controller == null && !$arg2 instanceof \Closure)
            throw new IllegalArgumentException("Unable to set controller for route $uri.");

        if (!strstr($controller, "@") && !$arg2 instanceof \Closure)
            throw new IllegalArgumentException("Unable to set controller for route $uri: no controller method set.");

        $route = array(
            'conditions' => array(),
            'name' => $routename,
            'path' => $uri,
            'method' => strtoupper($type),
            'controller' => $controller,
            'closure' => $closure
        );
        self::$routes->add($routename, $route);

        return self::$instance;
    }

    private function __construct() {
        self::$routes = new Collection();
    }
}