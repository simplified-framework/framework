<?php

namespace Simplified\Http;

use Simplified\Core\NullPointerException;

class Response {
    private $headers;
    private $status = 0;
    private $content = null;
    private $date = null;
    private $lastModified = null;

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
        400 => "",
        401 => "",
        402 => "",
        403 => "",
        404 => "",
        405 => "",
        406 => "",
        407 => "",
        408 => "",
        409 => "",
        410 => "",
        411 => "",
        412 => "",
        413 => "",
        414 => "",
        415 => "",
        416 => "",
        417 => "",
        418 => "",
        420 => "",
        421 => "",
        422 => "",
        423 => "",
        424 => "",
        425 => "",
        426 => "",
        428 => "",
        429 => "",
        431 => "",
        444 => "",
        449 => "",
        451 => "",

        // Server errors
        500 => "",
        501 => "",
        502 => "",
        503 => "",
        504 => "",
        505 => "",
        506 => "",
        507 => "",
        508 => "",
        509 => "",
        510 => ""
    ];

    public function __construct($content = '', $status = 200, $headers = array()) {
        $this->setContent($content);
        $this->setStatus($status);
        $this->addHeader("Content-Type", "text/html");
    }

    public function addHeader($name, $value) {
        if ($name == null)
            throw new NullPointerException('name field can not be null');

        if ($value == null)
            throw new NullPointerException('value field can not be null');

        $this->headers[$name] = $value;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function setDate(\DateTime $date) {
        $this->date = $date;
    }

    public function setLastModified(\DateTime $date) {
        $this->lastModified = $date;
    }

    public function sendHeaders() {
        $status = $this->status . " " . $this->statusHeaders[$this->status];
        //print $status; exit;

        header("HTTP/1.1 " . $status, true);
        foreach ($this->headers as $name => $value) {
            header($name.": ".$value, true);
        }
    }

    public function sendContent() {
        if (strlen($this->content) > 0)
            print $this->content;
    }

    public function send() {
        $this->sendHeaders();
        $this->sendContent();
    }
}