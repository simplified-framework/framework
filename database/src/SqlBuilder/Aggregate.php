<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 25.12.2015
 * Time: 09:13
 */

namespace Simplified\Database\SqlBuilder;

class Aggregate {
    private static $field;
    private static $type;

    public static function __callStatic($name, $arguments) {
        self::$type = $name;
        self::$field = $arguments[0];
    }

    public function __toString() {
        return strtoupper(self::$type) . "(" . self::$field . ")";
    }
}