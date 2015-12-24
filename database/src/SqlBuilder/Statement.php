<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Core\IllegalArgumentException;

class Statement {
    const SELECT = 1;
    const INSERT = 2;
    const UPDATE = 3;
    const DELETE = 4;

    private $type;
    private $table;
    private $fields = array();

    public function __construct($type, $table) {
        $this->type = $type;
        $this->table = $table;
    }

    public function addField($field) {
        if ($this->type == Statement::DELETE)
            throw new IllegalArgumentException("DELETE statements can't have fields");

        $this->fields[] = $field;
    }

    public function compile() {
        switch ($this->type) {
            case Statement::SELECT:
                $stmt = "SELECT ";
                if (count($this->fields) == 0) {
                    $stmt .= $this->table.".* ";
                } else {
                    $stmt .= implode(",", $this->fields) . " ";
                }
                $stmt .= "FROM " . $this->table;
                return $stmt;
                break;
        }
    }

    public function type() {
        return $this->type;
    }
}