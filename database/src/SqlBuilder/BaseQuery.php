<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 23.12.2015
 * Time: 18:41
 */

namespace Simplified\Database\SqlBuilder;


use Simplified\Database\Connection;

class BaseQuery {
    private $connection;
    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }
}