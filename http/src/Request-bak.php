<?php

namespace Simplified\Http;


class RequestBak {
    private $method;
    private $uri;
    private $querystring;
    private $segments;
    private $clientAddress;
    private $isajax;
    private $headers;
    private static $data;
    private static $instance;

    public static function createFromGlobals() {
        if (self::$instance == null)
            self::$instance = new self();

        return self::$instance;
    }

    public static function input($key, $default = null) {
        if (isset(self::$data[$key]))
            return self::$data[$key];

        return $default;
    }

    public function method() {
        return $this->method;
    }

    public function uri() {
        return $this->uri;
    }

    public function path() {
        return str_replace("?".$this->querystring, "", $this->uri);
    }

    public function queryString() {
        return $this->querystring;
    }

    public function segments() {
        return $this->segments;
    }

    public function clientAddress() {
        return $this->clientAddress;
    }

    public function isAjax() {
        return $this->isajax;
    }

    public function headers() {
        return $this->headers;
    }

    private function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->querystring = $_SERVER['QUERY_STRING'];
        $this->clientAddress = $_SERVER['REMOTE_ADDR'];

        $segments_data = explode("/", substr(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $_SERVER['REQUEST_URI'], 1));
        if ($segments_data == null || !is_array($segments_data))
            $segments_data = array();
        $this->segments = $segments_data;

        $this->headers = getallheaders();

        $isAjax = false;
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isAjax = true;
        }
        $this->isajax = $isAjax;

        $data = array();
        $data = array_merge($data, $_GET);
        $data = array_merge($data, $_POST);
        $data = array_merge($data, $_FILES);
        self::$data = $data;
    }
}