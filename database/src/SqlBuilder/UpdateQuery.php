<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 24.12.2015
 * Time: 13:52
 */

namespace Simplified\Database\SqlBuilder;
use Simplified\Database\Connection;

class UpdateQuery extends CommonQuery {
    public function __construct($table, Connection $connection) {
        parent::__construct($connection);
        $this->table = $table;
    }

    public function set(array $data) {
        $this->values = $data;
    }

    public function getQuery() {
        $set = array();
        foreach ($this->values as $key => $value) {
            $set[] = $key . "=" . $value;
        }
        $args = implode(", ", $set);
        $query = "UPDATE " . $this->table . " SET " . $args;
        return $query . " " . parent::getQuery();
    }
}