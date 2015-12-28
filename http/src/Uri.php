<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 28.12.2015
 * Time: 19:13
 */

namespace Simplified\Http;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface {
    private $data;
    public static function fromString($str) {
        return new self($str);
    }

    public function getScheme()
    {
        // TODO: Implement getScheme() method.
    }

    public function getAuthority()
    {
        // TODO: Implement getAuthority() method.
    }

    public function getUserInfo()
    {
        // TODO: Implement getUserInfo() method.
    }

    public function getHost()
    {
        // TODO: Implement getHost() method.
    }

    public function getPort()
    {
        // TODO: Implement getPort() method.
    }

    public function getPath()
    {
        return $this->data;
    }

    public function getQuery()
    {
        // TODO: Implement getQuery() method.
    }

    public function getFragment()
    {
        // TODO: Implement getFragment() method.
    }

    public function withScheme($scheme)
    {
        // TODO: Implement withScheme() method.
    }

    public function withUserInfo($user, $password = null)
    {
        // TODO: Implement withUserInfo() method.
    }

    public function withHost($host)
    {
        // TODO: Implement withHost() method.
    }

    public function withPort($port)
    {
        // TODO: Implement withPort() method.
    }

    public function withPath($path)
    {
        // TODO: Implement withPath() method.
    }

    public function withQuery($query)
    {
        // TODO: Implement withQuery() method.
    }

    public function withFragment($fragment)
    {
        // TODO: Implement withFragment() method.
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
    }

    private function __construct($str)
    {
        $this->data = $str;
    }
}