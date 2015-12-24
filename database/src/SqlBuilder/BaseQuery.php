<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Core\IllegalArgumentException;
use Simplified\Database\ModelException;

class BaseQuery {
    private $andWhere = array();
    private $statement;

    // TODO implement more args
    public function where() {
        switch (func_num_args()) {
            case 1:
                // support raw where clauses: where("field = value")
                // support Closure where clauses: where(function($query){})
                $this->andWhere[] = func_get_arg(0);
                break;
            case 2:
                // support short hand where EQUAL clause: where("field", "value")
                // support short hand where IN clause: where("field", array())
                $field = func_get_arg(0);
                $params = func_get_arg(1);
                if (!is_string($field))
                    throw new IllegalArgumentException("First argument must be string");

                if (is_string($params) || is_numeric($params)) {
                    $escaped = is_string($params) ? "'" . $params . "'" : $params;
                    $this->andWhere[] = "$field = $escaped";
                }
                else
                if (is_array($params)) {
                    $this->andWhere[] = "$field IN (" . implode(",", $params) . ")";
                }
                break;
            case 3:
                $operator = func_get_arg(1);
                $field = func_get_arg(0);
                $params = func_get_arg(2);

                if (!WhereOperator::isValid($operator))
                    throw new IllegalArgumentException("Operator '$operator' must on of '<,>,=,!=,NOT,IN,NOT IN,LIKE'");

                if (!is_string($field) || !is_string($operator))
                    throw new IllegalArgumentException("First and second argument must be string");

                if (is_array($params) && ($operator != WhereOperator::IN && $operator != WhereOperator::NOT_IN))
                    throw new IllegalArgumentException("Second argument can't be used to compare table field and array");

                if (($operator == WhereOperator::IN || $operator == WhereOperator::NOT_IN) && !is_array($params))
                    throw new IllegalArgumentException("Third argument must be array");

                if (is_string($params) || is_numeric($params)) {
                    $escaped = is_string($params) ? "'" . $params . "'" : $params;
                    $this->andWhere[] = "$field $operator $escaped";
                }
                else
                if (is_array($params)) {
                    $this->andWhere[] = "$field $operator (" . implode(",", $params) . ")";
                }
                break;
            case 0:
                throw new IllegalArgumentException("Where clause needs at least one argument");
        }

        return $this;
    }

    public function whereIn($field, array $params) {
        $query = clone $this;
        return $query->where($field, "IN", $params);
    }

    public function whereNotIn($field, array $params) {
        $query = clone $this;
        return $query->where($field, "NOT IN", $params);
    }

    public function whereBetween($field, array $params) {
        if (count($params) != 2)
            throw new IllegalArgumentException("Illegal data in array");

        if (!is_string($field))
            throw new IllegalArgumentException("First argument must be string");

        $this->andWhere[] = "$field BETWEEN " . $params[0] . " AND " . $params[1];
        return $this;
    }

    public function whereNotBetween($field, array $params) {
        if (count($params) != 2)
            throw new IllegalArgumentException("Illegal data in array");

        if (!is_string($field))
            throw new IllegalArgumentException("First argument must be string");

        $this->andWhere[] = "$field NOT BETWEEN " . $params[0] . " AND " . $params[1];
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