<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 25.12.2015
 * Time: 09:13
 */

namespace Simplified\Database\SqlBuilder;

class Aggregate {
    private $field;
    private $type;

    public static function __callStatic($name, $arguments) {
        return new self($arguments[0], $name);
    }

    public function __toString() {
        return strtoupper($this->type) . "(" . $this->field . ")";
    }

    private function __construct($field, $type) {
        $this->field = $field;
        $this->type = $type;
    }
}