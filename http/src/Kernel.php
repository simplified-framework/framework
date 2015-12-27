<?php

namespace Simplified\Http;
use Simplified\Config\Config;
use Simplified\Core\IllegalArgumentException;
use Simplified\Debug\Debug;
use Simplified\Session\SessionException;

define ("BASE_PATH",   dirname(dirname($_SERVER['SCRIPT_FILENAME'])) . DIRECTORY_SEPARATOR);
define ("VENDOR_PATH", BASE_PATH . "vendor" . DIRECTORY_SEPARATOR);
define ("PUBLIC_PATH", BASE_PATH . "public" . DIRECTORY_SEPARATOR);
define ("APP_PATH", BASE_PATH . "app" . DIRECTORY_SEPARATOR);
define ("STORAGE_PATH", APP_PATH . "storage" . DIRECTORY_SEPARATOR);
define ("I18N_PATH", APP_PATH . "i18n" . DIRECTORY_SEPARATOR);
define ("RESOURCES_PATH", APP_PATH . "resources" . DIRECTORY_SEPARATOR);
define ("RESOURCES_VENDOR_PATH", RESOURCES_PATH . "vendor" . DIRECTORY_SEPARATOR);
define ("CONFIG_PATH", APP_PATH . "config" . DIRECTORY_SEPARATOR);

// handle debug
Debug::handleDebug();

// load configured routes
require CONFIG_PATH . 'routes.php';

class Kernel {
    public function handleRequest() {
    	ob_start ();

        $this->startSession();

        $routes = RouteCollection::instance();
        if ($routes == null || $routes->count() == 0)
            throw new \ErrorException('Unable to load routes from configuration directory.');

        $request = Request::createFromGlobals();
        $path = $request->path();
        $current_route = null;
        $matches = array();

        $urlMatcher = new UrlMatcher($routes, $request);
        switch ($urlMatcher->matches($path)) {
            case UrlMatcher::UNKNOWN_RESOURCE:
                throw new ResourceNotFoundException('Route not found: ' . $path);
                return;

            case UrlMatcher::METHOD_MISMATCH:
                throw new MethodNotAllowedException("Method not allowed");
                return;

            case UrlMatcher::MATCH_FOUND:
                $current_route = $urlMatcher->getMatchedRoute();
                $matches = $urlMatcher->getMatches();
                break;
        }

        // if we use a closure, call them with the request object
        if ($current_route->closure) {
        	$content = null;
            $params = array();
            $ref = new \ReflectionFunction ($current_route->closure);
            if ($ref->getNumberOfParameters() > 0) {
                $first = $ref->getParameters()[0];
                if ($first->getClass() != null && strstr($first->getClass()->getName(), 'Request'))
                    $params[] = $request;

                foreach ($matches as $match) {
                    $params[] = $match;
                }
            }

            $retval = call_user_func_array($current_route->closure, $params);
            $this->handleContent($retval);
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
        $params = array();

        if ($num_params > 0) {
            $ref_method = $ref->getMethod($method);
            $first = $ref_method->getParameters()[0];
            if ($first->getClass() != null && strstr($first->getClass()->getName(), 'Request'))
                $params[] = $request;
            foreach ($matches as $match) {
                $params[] = $match;
            }
        }
        $retval = call_user_func_array(array(new $controller, $method), $params);

        $this->handleContent($retval);
    }

    private function startSession() {
        if (headers_sent())
            throw new SessionException('Unable to start session handling, headers already sent.');

        $provider = Config::get('providers', 'session');
        if ($provider) {
            if (!class_exists($provider))
                throw new IllegalArgumentException('Unable to set session provider to ' . $provider);

            $handlerClass = (new $provider())->provides();
            $handler = new $handlerClass();
            session_set_save_handler($handler, true);
        }
        session_start();
    }

    private function handleContent($content) {
        $clean_content = ob_get_clean ();
        if ($clean_content != null) {
            (new Response($clean_content))->send();
        }

        if ($content != null) {
            if (is_string($content)) {
                (new Response($content))->send();
            }
            else {
                if ($content instanceof Response) {
                    $content->send();
                } else {
                    if (is_array($content)) {
                        (new Response(json_encode($content), 200, array('Content-Type' => 'application/json')))->send();
                    }
                }
            }
        }
    }
}