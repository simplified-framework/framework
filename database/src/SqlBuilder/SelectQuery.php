<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Core\IllegalArgumentException;
use Simplified\Database\Connection;

class SelectQuery extends CommonQuery {
    private $modelClassName;
    public function __construct($from, $modelClassName, Connection $conn) {
        if (!is_string($from) || is_null($from))
            throw new IllegalArgumentException("No table name specified");

        parent::__construct($conn);
        $this->type = "SELECT";
        $this->fields = $from.".*";
        $this->table = $from;
        $this->modelClassName = $modelClassName;
    }

    public function select($fields) {
        $this->fields = $fields;
        return $this;
    }

    public function getQuery() {
        $fields = is_array($this->fields) ? implode(",", $this->fields) : $this->fields;
        $query = "SELECT $fields FROM " . $this->table;
        return $query . " " . parent::getQuery();
    }

    public function get() {
        $query = $this->getQuery();
        $stmt = $this->connection()->raw($query);
        if ($stmt && $stmt->rowCount() > 0) {
            $stmt->setFetchMode(\PDO::FETCH_CLASS,$this->modelClassName);
            $result = $stmt->fetch();
            return $result;
        }
        return false;
    }

    public function count() {
        $clone = clone $this;
        $query = $clone->select("COUNT(*)")->getQuery();
        return $this->connection()->raw($query)->fetch();
    }

    public function min($field) {

    }

    public function max($field) {

    }

    public function avg($field) {

    }

    public function sum($field) {

    }
}