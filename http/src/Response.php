<?php

namespace Simplified\Http;

use Simplified\Core\NullPointerException;
use DateTime;

class Response {
    private $headers;
    private $status = 0;
    private $content = null;
    private $date = null;
    private $lastModified = null;
    private $protocolVersion = "HTTP/1.1";

    private $statusHeaders = [

        // Information
        100 => "Continue",
        101 => "Switching Protocols",
        102 => "Processing",

        // Success operations
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        207 => "Multi-Status",
        208 => "Already Reported",
        226 => "IM Used",

        // Redirection
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        306 => "Switch Proxy", // not used
        307 => "Temporary Redirect",
        308 => "Permanent Redirect",

        // Client errors
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Time-out",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Request Entity Too Large",
        414 => "Request-URL Too Long",
        415 => "Unsupported Media Type",
        416 => "Requested range not satisfiable",
        417 => "Expectation Failed",
        418 => "I'm a teapot",
        420 => "Policy Not Fulfilled",
        421 => "Misdirected Request",
        422 => "Unprocessable Entity",
        423 => "Locked",
        424 => "Failed Dependency",
        425 => "Unordered Collection",
        426 => "Upgrade Required",
        428 => "Precondition Required",
        429 => "Too Many Requests",
        431 => "Request Header Fields Too Large",
        444 => "No Response",
        449 => "The request should be retried after doing the appropriate action",
        451 => "Unavailable For Legal Reasons",

        // Server errors
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Time-out",
        505 => "HTTP Version not supported",
        506 => "Variant Also Negotiates",
        507 => "Insufficient Storage",
        508 => "Loop Detected",
        509 => "Bandwidth Limit Exceeded",
        510 => "Not Extended"
    ];

    public function __construct($content = '', $status = 200, $headers = array()) {
        $this->setContent($content);
        $this->setStatus($status);
        $this->setLastModified(new DateTime());
        $this->addHeader("Content-Type", "text/html");
        $this->headers = array_merge($this->headers, $headers);
    }

    public function addHeader($name, $value) {
        if ($name == null)
            throw new NullPointerException('name field can not be null');

        if ($value == null)
            throw new NullPointerException('value field can not be null');

        $this->headers[$name] = $value;
    }

    public function setStatus($status) {
        $this->status = intval($status);
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function setDate(DateTime $date) {
        $this->date = $date;
    }

    public function setLastModified(DateTime $date) {
        $this->lastModified = $date;
    }

    public function setProtocolVersion($version) {
        if ($version != "HTTP/1.0" && $version != "HTTP/1.1")
            throw new UnknownProtocolVersionException("Unknown version: $version");

        $this->protocolVersion = $version;
    }

    public function sendHeaders() {
        $status = $this->status . " " . $this->statusHeaders[$this->status];
        header($this->protocolVersion . " " . $status, true);
        header('Last-Modified: ' . $this->lastModified->format(DATE_COOKIE));
        foreach ($this->headers as $name => $value) {
            header($name.": ".$value, true);
        }
    }

    public function sendContent() {
        print $this->content;
    }

    public function send() {
        $this->sendHeaders();
        $this->sendContent();
        exit;
    }
}