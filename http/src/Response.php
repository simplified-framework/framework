<?php

namespace Simplified\Http;

use Simplified\Core\NullPointerException;

class Response {
    private $headers;
    private $status = "HTTP/1.1 401 Authorization Failed";
    private $content = null;
    private $date = null;
    private $lastModified = null;

    public function __construct() {
        $this->setStatus("HTTP/1.1 200 Ok");
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
        header($this->status, true);
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