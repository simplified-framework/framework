<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 30.12.2015
 * Time: 14:51
 */

namespace Simplified\Asset;


class VersionStrategy {
    private $version;
    private $format;

    public function __construct($version, $format = null) {
        $this->version = $version;
        $this->format = $format ?: '%s?%s';
    }

    public function version() {
        return $this->version;
    }

    public function format() {
        return $this->format;
    }

    public function applyVersion($path) {
        $versionized = sprintf($this->format, ltrim($path, '/'), $this->version($path));
        if ($path && '/' == $path[0]) {
            return '/'.$versionized;
        }
        return $versionized;
    }
}