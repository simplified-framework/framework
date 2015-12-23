<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Database\Connection;

class Builder {
    private $connection;
    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function select($table) {
        $query = new SelectQuery($table);
        return $query;
    }
}