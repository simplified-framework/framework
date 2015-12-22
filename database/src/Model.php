<?php

namespace Simplified\Database;

use Simplified\Core\IllegalArgumentException;
use Simplified\Database\SqlBuilder\Builder;
use ReflectionProperty;

class Model {
	private $builder = null;
    private $attributes = null;

    // override connection name
	static  $connection;

    // override primary key
    static  $primaryKey;

    // override table name
    static  $table;

	public function __construct($attributes = null) {
        if (is_array($attributes))
            $this->attributes = $attributes;
    }
	
	public function getTable() {
        $model_class = get_called_class();
        $ref = new ReflectionProperty($model_class, 'table');
        $table_name = $ref->getValue($this);
        if (!$table_name) {
            $table_name = strtolower(basename($model_class));
        }

        return $table_name;
	}

    public function getPrimaryKey() {
        $model_class = get_called_class();
        $ref = new ReflectionProperty($model_class, 'primaryKey');
        $key = $ref->getValue($this);
        if (null != $key) {
            return $key;
        }

        return 'id';
    }

    public static function all() {
        $model_class = get_called_class();
        $instance = new $model_class();
        $table_name = $instance->getTable();

        // TODO check connection for model instance
        $driver = new Builder();

        // TODO check return value from PDO
        return $driver->select($table_name)->asObject($model_class)->execute()->fetchAll();
    }

    public static function find($id) {
        if (!is_numeric($id))
            throw new IllegalArgumentException("Argument must be numeric");

        $model_class = get_called_class();
        $instance = new $model_class();
        $table_name = $instance->getTable();

        // TODO check connection for model instance
        $driver = new Builder();

        // TODO check return value from PDO
        return $driver->select($table_name)->where($instance->getPrimaryKey(), array($id))->asObject($model_class)->execute()->fetch();
    }

    public static function where ($field, $condition, $value) {
        $class = get_called_class();
        return $class::all()->where($field, $condition, $value);
    }
/*
    public function hasMany($modelClass, $foreignKey = null) {
        $trace = debug_backtrace();
        $caller = $trace[1];

        $attr = $caller['function'];
        if ($this->getProperty($attr))
            return $this->getProperty($attr);

        if (class_exists($modelClass)) {
            $pk = $this->getPrimaryKey();
            $id_value = $this->$pk;

            $instance = new $modelClass();
            $fk = $foreignKey ? $foreignKey : $this->getTable() . "_id";
            $rel_table = $instance->getTable();

            $data = $modelClass::where($rel_table . "." . $fk, '=', $id_value)->get();
            $this->$attr = $data;
            return $data;
        }
    }

    public function hasOne($modelClass, $foreignKey = null) {
        $trace = debug_backtrace();
        $caller = $trace[1];

        $attr = $caller['function'];
        if ($this->getProperty($attr))
            return $this->getProperty($attr);

        if (class_exists($modelClass)) {
            $pk = $this->getPrimaryKey();
            $id_value = $this->$pk;

            $instance = new $modelClass();
            $fk = $foreignKey ? $foreignKey : $this->getTable() . "_id";
            $rel_table = $instance->getTable();

            $data = $modelClass::where($rel_table . "." . $fk, '=', $id_value)->limit(1)->get();
            $this->$attr = $data;
            return $data;
        }
    }

    public function delete() {
        // TODO who can delete this record?
    }

    public function save() {
        // TODO who can save this record?
    }
    */

    public function __get($name) {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        if (isset($this->$name))
            return $this->$name;
    }

    public function __set($name, $value) {
        if (!isset($this->$name)) {
            $this->attributes[$name] = $value;
        } else {
            $this->$name = $value;
        }
    }
}

