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

    public function __construct(Connection $driver) {
        $this->fields = new Collection();
        $this->driver = $driver;
    }

    public function __set($field, $value) {
        if (isset($this->fields[$field]))
            return;

        if (isset($this->$field))
            return;

        $this->fields[$field] = $value;
    }

    public function __get($field) {
        if (isset($this->$field))
            return $this->$field;

        return $this->fields[$field];
    }
}