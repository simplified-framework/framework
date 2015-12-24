<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Database\Connection;

class BaseQuery {
    protected $type;
    protected $table;
    protected $fields;

    public function __construct(Connection $connection) {
    }

    public function getQuery() {
        $query = "";
        if ($this->type == "SELECT") {
            $query = "SELECT ";
            if ($this->fields) {
                if (is_array($this->fields)) {
                    $query .= implode(",", $this->fields);
                } else {
                    $query .= $this->fields;
                }
            } else {
                throw new SqlSyntaxException("No fields selected");
            }
            $query .= " FROM " . $this->table;
        }

        if (count($this->joins) > 0)
            $query .= " " . implode(" ", $this->joins);

        if (count($this->andWhere) > 0)
            $query .= " WHERE " . implode(" AND ", $this->andWhere);
        return $query;
    }
}