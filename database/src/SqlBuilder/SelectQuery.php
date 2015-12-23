<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 23.12.2015
 * Time: 18:37
 */

namespace Simplified\Database\SqlBuilder;


class SelectQuery extends BaseQuery {
    private $from;
    public function __construct($from, Builder $builder) {
        parent::__construct($builder);
        $this->from = $from;
    }

    public function execute() {
        $sql = "SELECT " . $this->from . ".* FROM " . $this->from;
        print $sql;
    }
}