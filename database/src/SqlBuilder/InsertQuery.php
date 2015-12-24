<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 24.12.2015
 * Time: 13:53
 */

namespace Simplified\Database\SqlBuilder;


class InsertQuery extends BaseQuery {
    public function __construct($table, Connection $connection) {
        parent::__construct($connection);
        $this->table = $table;
    }

    public function set(array $data) {
        $this->values = $data;
        return $this;
    }

    public function getQuery() {
        $fields = implode(",",array_keys($this->values));
        $values = implode(",",array_values($this->values));
        return "INSERT INTO " . $this->table . " (" . $fields . ") VALUES (" . $values . ")";
    }

    public function execute() {
        $q = $this->getQuery();
        $stmt = $this->connection()->raw($q);
        return $stmt ? $this->connection()->lastInsertId() : 0;
    }
}