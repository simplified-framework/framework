<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 20.12.2015
 * Time: 15:55
 */

namespace Simplified\DBAL\Schema;

class Table {
    private $name;

    public function __construct($name) {
        $this->name = $name;
    }
}