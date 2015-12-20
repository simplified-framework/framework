<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 20.12.2015
 * Time: 15:09
 */

namespace Simplified\DBAL\Driver;


class Table {
    private $attributes = array();

    public function __set($name, $value) {
        $this->attributes[strtolower($name)] = $value;
    }

    public function __get($name) {
        return isset($this->attributes[strtolower($name)]) ? $this->attributes[strtolower($name)] : null;
    }
}