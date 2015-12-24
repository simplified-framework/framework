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
                // support Closure where clauses: where(function($query){})
                $this->andWhere[] = func_get_arg(0);
                break;
            case 2:
                // support short hand where clause: where("field" = "value")
                // support short hand whereIN clause: where("field", "IN",  array())
                break;
            case 3:
                // support where clause with operator: where("field", "<,>,=,!=,NOT,IN,NOT IN", "value")
                // validate function args
                if (WhereOperator::isValid(func_get_arg(1)))
                    throw new IllegalArgumentException("Operator must on of '<,>,=,!=,NOT,IN,NOT IN'");

                if (!is_string(func_get_arg(0)) || !is_string(func_get_arg(1)))
                    throw new IllegalArgumentException("First and second argument must be string");

                if (is_array(func_get_arg(2)) && (func_get_arg(1) != WhereOperator::IN || func_get_arg(1) != WhereOperator::NOT_IN))
                    throw new IllegalArgumentException("Second argument can't be used to compare table field and array");

                if ((func_get_arg(1) == WhereOperator::IN || func_get_arg(1) == WhereOperator::NOT_IN) && !is_array(func_get_arg(2)))
                    throw new IllegalArgumentException("Third argument must be array");


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