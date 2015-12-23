<?php

namespace Simplified\Database\SqlBuilder;

class SelectQuery extends BaseQuery {
    private $from;
    public function __construct($from) {
        parent::__construct();
        $this->from = $from;
    }

    public function execute() {
        $sql = "SELECT " . $this->from . ".* FROM " . $this->from;
        print $sql;
        return null;
    }
}