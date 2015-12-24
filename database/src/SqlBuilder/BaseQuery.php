<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Database\Connection;

abstract class BaseQuery {
    private $connection;
    protected $type;
    protected $table;
    protected $fields;
    protected $values;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function connection() {
        return $this->connection;
    }

    public function execute() {}

    abstract public function getQuery();
}