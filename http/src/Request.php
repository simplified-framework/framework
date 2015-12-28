<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 28.12.2015
 * Time: 19:07
 */

namespace Simplified\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface {
    private $method;
    private $uri;
    private $querystring;
    private $segments;
    private $clientAddress;
    private $isajax;
    private $headers;
    private $protocolVersion;
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

    public function getProtocolVersion() {
    }

    public function withProtocolVersion($version) {
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function hasHeader($name) {
    }

    public function getHeader($name) {
    }

    public function getHeaderLine($name) {
    }

    public function withHeader($name, $value) {
    }

    public function withAddedHeader($name, $value) {
    }

    public function withoutHeader($name) {
    }

    public function getBody() {
    }

    public function withBody(StreamInterface $body) {
    }

    public function getRequestTarget() {
    }

    public function withRequestTarget($requestTarget) {
    }

    public function getMethod() {
        return $this->method;
    }

    public function withMethod($method) {
    }

    public function getUri() {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false) {
    }

    private function __construct() {
        $this->uri = Uri::fromString($_SERVER['REQUEST_URI']);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->querystring = $_SERVER['QUERY_STRING'];
        $this->clientAddress = $_SERVER['REMOTE_ADDR'];
        $this->protocolVersion = str_replace("HTTP/","",$_SERVER['SERVER_PROTOCOL']);

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