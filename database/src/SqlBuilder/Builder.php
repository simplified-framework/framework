<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 23.12.2015
 * Time: 18:34
 */

namespace Simplified\Database\SqlBuilder;

use Simplified\Database\Connection;

class Builder {
    private $connection;
    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function select($table) {
        $query = new SelectQuery($table, $this->connection);
        return $query;
    }
}