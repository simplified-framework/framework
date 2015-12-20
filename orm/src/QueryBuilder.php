<?php

namespace Simplified\Orm;
use Simplified\Core\Collection;

class Query {
    private $model;
    private $fields;
    private $tables;
    private $limit = 0;
    private $orderby = null;
    private $conditions = array();

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function select($what) {
        $this->fields = $what;
        return $this;
    }

    public function from($tbl) {
        $this->tables[] = $tbl;
        return $this;
    }

    public function where($field, $condition, $value) {
        $this->conditions[] = array('type' => 'where', 'field' => $field, 'condition' => $condition, 'value' => $value);
        return $this;
    }

    public function whereIn($field, $collection) {
        $this->conditions[] = array('type' => 'where', 'field' => $field, 'condition' => 'in', 'value' => "(".implode(',', $collection).")");
        return $this;
    }

    public function orderBy($field, $asc_desc) {
        $this->orderby = ['field' => $field, 'value' => (strtolower($asc_desc) == "desc") ? "desc" : "asc"];
        return $this;
    }

    public function limit($num) {
        $this->limit = $num;
        return $this;
    }

    public function get() {
        $q = "SELECT " . $this->fields . " FROM " . implode(',', $this->tables);
        if (count($this->conditions) > 0) {
            $where = array();
            foreach ($this->conditions as $cond) {
                if ($cond['type'] == 'where') {
                    $where[] = $cond['field'] . $cond['condition'] . $cond['value'];
                }
            }
            $q .= " WHERE " . implode(' AND ', $where);
        }

        if ($this->orderby)
            $q .= " ORDER BY " . $this->orderby['field'] . " " . $this->orderby['value'];

        if ($this->limit > 0)
            $q .= ' LIMIT ' . intval($this->limit);

        $data = new Collection();
        $result = $this->model->getDriver()->rawQuery($q);
        $className = get_class($this->model);

        $fields = $this->model->getFieldNames();
        foreach ($result as $row) {
            $instance = new $className;
            foreach ($fields as $field) {
                $instance->$field = $row[$field];
            }

            $data[] = $instance;
        }

        return $data;
    }

    public function first() {
        // compile query
        $q = "SELECT " . $this->fields . " FROM " . implode(',', $this->tables);
        if (count($this->conditions) > 0) {
            $where = array();
            foreach ($this->conditions as $cond) {
                if ($cond['type'] == 'where') {
                    $where[] = $cond['field'] . $cond['condition'] . $cond['value'];
                }
            }
            $q .= " WHERE " . implode(' AND ', $where);
        }

        if ($this->orderby)
            $q .= " ORDER BY " . $this->orderby['field'] . " " . $this->orderby['value'];

        $q .= ' LIMIT 1';

        $data = null;
        $result = $this->model->getDriver()->rawQuery($q);
        $className = get_class($this->model);

        $fields = $this->model->getFieldNames();
        if ($result != null && $result->count() == 1) {
            $row = $result[0];
            $instance = new $className;
            foreach ($fields as $field) {
                $instance->$field = $row[$field];
            }

            $data = $instance;
        }

        return $data;
    }
}