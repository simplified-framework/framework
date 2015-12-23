<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 23.12.2015
 * Time: 18:37
 */

namespace Simplified\Database\SqlBuilder;


use Simplified\Database\Connection;

class SelectQuery extends BaseQuery {
    private $from;
    public function __construct($from, Connection $connection) {
        parent::__construct($connection);
        $this->from = $from;
    }

    public function execute() {
        $sql = "SELECT " . $this->from . ".* FROM " . $this->from;
        print $sql;
    }
}