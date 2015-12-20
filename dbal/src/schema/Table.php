<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 20.12.2015
 * Time: 15:55
 */

namespace Simplified\DBAL\Schema;


use Simplified\Core\Collection;
use Simplified\DBAL\Connection;

class Table {
    private $fields;
    private $driver;
    private $name;

    public function __construct(Connection $driver) {
        $this->fields = new Collection();
        $this->driver = $driver;
    }

    public function __call($method, $arg = null) {
        if ($method == "setName") {
            $this->name = $arg;
        }
        if ($method == "getName") {
            return $this->name;
        }
    }
}