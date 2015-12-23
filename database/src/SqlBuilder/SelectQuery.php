<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Core\IllegalArgumentException;

class SelectQuery extends BaseQuery {
    private $statement;
    public function __construct($from) {
        if (!is_string($from) || is_null($from))
            throw new IllegalArgumentException("No table name specified");

        parent::__construct();
        $this->statement = new Statement(Statement::SELECT, $from);
    }

    public function get() {
        $stmt = $this->statement->compile();
        print $stmt;
        return null;
    }
}