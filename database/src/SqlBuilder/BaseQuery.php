<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Core\IllegalArgumentException;

class BaseQuery {
    private $andWhere = array();
    public function __construct() {
    }

    public function where() {
        switch (func_num_args()) {
            case 1:
                $this->andWhere[] = func_get_arg(0);
                break;
            case 2:
                break;
            case 3:
                break;
            case 0:
                throw new IllegalArgumentException("Where clause needs at least one argument");
        }
    }
}