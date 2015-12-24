<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Core\IllegalArgumentException;

class SelectQuery extends BaseQuery {
    public function __construct($from) {
        if (!is_string($from) || is_null($from))
            throw new IllegalArgumentException("No table name specified");

        parent::__construct();
        $this->setStatement(new Statement(Statement::SELECT, $from));
    }

    public function get() {
        $query = $this->getQuery();
        print $query;
        return null;
    }

    public function count() {

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