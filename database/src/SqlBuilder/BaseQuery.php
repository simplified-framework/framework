<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Core\IllegalArgumentException;
use Simplified\Database\ModelException;

class BaseQuery {
    private $andWhere = array();
    private $statement;

    public function __construct() {
    }

    public function where() {
        switch (func_num_args()) {
            case 1:
                $this->andWhere[] = func_get_arg(0); // where("field = 2")
                break;
            case 2:
                break;
            case 3:
                break;
            case 0:
                throw new IllegalArgumentException("Where clause needs at least one argument");
        }

        return $this;
    }

    public function setStatement(Statement $stmt) {
        $this->statement = $stmt;
    }

    public function getQuery() {
        if (empty($this->statement))
            throw new ModelException("Unable to compile statement");

        $query  = $this->statement->compile();
        if (count($this->andWhere) > 0)
            $query .= " WHERE " . implode("AND ", $this->andWhere);
        return $query;
    }
}