<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Core\IllegalArgumentException;
use Simplified\Database\ModelException;

class BaseQuery {
    private $andWhere = array();
    private $statement;

    public function __construct() {
    }

    // TODO implement more args
    public function where() {
        switch (func_num_args()) {
            case 1:
                // support raw where clauses: where("field = value")
                $this->andWhere[] = func_get_arg(0);
                break;
            case 2:
                // support short hand where clause: where("field" = "value")
                break;
            case 3:
                // support where clause with operator: where("field", "<,>,=,!=,NOT,IN", "value")
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

        if ($this->statement->type() == Statement::INSERT && count($this->andWhere) > 0)
            throw new ModelException("INSERT statements can't have a WHERE clause");

        $query  = $this->statement->compile();
        if (count($this->andWhere) > 0)
            $query .= " WHERE " . implode(" AND ", $this->andWhere);
        return $query;
    }
}