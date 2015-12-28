<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 27.12.2015
 * Time: 13:54
 */

namespace Simplified\Http;
use Simplified\Core\Collection;

class UrlMatcher {
    // TODO define constants to reflect errors
    const MATCH_FOUND = 1;
    const METHOD_MISMATCH = 2;
    const UNKNOWN_RESOURCE = 3;

    private $routes;
    private $request;
    private $route;
    private $matches = array();

    public function __construct(Collection $routes, Request $request) {
        $this->routes  = $routes;
        $this->request = $request;
        $this->route   = null;
    }

    public function matches($url) {
        foreach ($this->routes->toArray() as $route) {
            $route_path = $route->path;
            if ($route_path === $url) {
                if ($route->method != $this->request->getMethod())
                    return UrlMatcher::METHOD_MISMATCH;

                $this->route = $route;
                break;
            }

            ///////////////////////////////////////////////////////////////////////////

            $matches = array();
            $pattern = str_replace("/", "\\/", $route_path);
            if ( count($route->conditions) > 0 ) {
                foreach ($route->conditions as $key => $val) {
                    $pattern = str_replace("{".$key."}", "($val)", $pattern);
                }
            } else {
                $pattern = preg_replace('/\{[a-zA-Z]+\}/', "([a-zA-Z]+)", $pattern);
            }

            // compile pattern
            if (0 === preg_match('/'.$pattern.'/', $url, $matches)) {
                return UrlMatcher::UNKNOWN_RESOURCE;
            }

            // compile pattern
            if (FALSE === preg_match('/'.$pattern.'/', $url, $matches)) {
                return UrlMatcher::UNKNOWN_RESOURCE;
            }

            // current route can be translated to regex pattern
            if (count($matches) >= 2 && !empty($matches[1])) {
                if ($route->method != $this->request->getMethod())
                    return UrlMatcher::METHOD_MISMATCH;

                array_shift($matches); // remove first element
                $this->route = $route;
                $this->matches = $matches;
                break;
            }
        }

        if ($this->route != null)
            return UrlMatcher::MATCH_FOUND;

        return UrlMatcher::UNKNOWN_RESOURCE;
    }

    public function getMatchedRoute() {
        return $this->route;
    }

    public function getMatches() {
        return $this->matches;
    }
}