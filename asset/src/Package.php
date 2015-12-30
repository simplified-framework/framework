<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 30.12.2015
 * Time: 14:48
 */

namespace Simplified\Asset;


class Package {
    private $strategy;
    public function __construct(VersionStrategy $strategy) {
        $this->strategy = $strategy;
    }

    public function getUrl($path) {
        if ($this->isAbsoluteUrl($path)) {
            return $path;
        }
        return $this->strategy->applyVersion($path);
    }

    protected function isAbsoluteUrl($url) {
        return false !== strpos($url, '://') || '//' === substr($url, 0, 2);
    }
}