<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Database\Connection;

abstract class BaseQuery {
    protected $type;
    protected $table;
    protected $fields;
    protected $values;

    public function __construct(Connection $connection) {
    }

    public function execute() {}

    abstract public function getQuery();
}