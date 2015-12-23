<?php

namespace Simplified\Database;

use Simplified\Config\Config;
use Simplified\Core\IllegalArgumentException;
use Simplified\Database\SqlBuilder\Builder;
use ReflectionProperty;
use Simplified\Database\SqlBuilder\Structure;

class Model {
    private $attributes = array();

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

    public function getConnection() {
        $model_class = get_called_class();
        $ref = new ReflectionProperty($model_class, 'connection');
        $connection = $ref->getValue($this);
        if (null != $connection) {
            return $connection;
        }

        return 'default';
    }

    public static function all() {
        $model_class = get_called_class();
        $instance = new $model_class();
        $table_name = $instance->getTable();

        $builder = $instance->getBuilder($instance);
        // TODO check return value from PDO
        return $builder->select($table_name)->asObject($model_class)->execute()->fetchAll();
    }

    public static function find($id) {
        if (!is_numeric($id))
            throw new IllegalArgumentException("Argument must be numeric");

        $model_class = get_called_class();
        $instance = new $model_class();
        $table_name = $instance->getTable();

        $builder = $instance->getBuilder($instance);
        // TODO check return value from PDO
        return $builder->select($table_name)->where($instance->getPrimaryKey(), array($id))->asObject($model_class)->execute()->fetch();
    }

    public static function where ($field, $condition, $value) {
        $model_class = get_called_class();
        $instance = new $model_class();
        $table_name = $instance->getTable();

        $builder = $instance->getBuilder($instance);
        // TODO check return value from PDO
        // TODO check clause against SQL injection!
        return $builder->select($table_name)->where("$field $condition $value")->asObject($model_class)->execute()->fetchAll();
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

    private function getBuilder(Model $model) {
        $config = Config::get('database', $model->getConnection());
        if (empty($config))
            throw new ConnectionException('Unknown database connection: ' . $model->getConnection());
        $builder = new Builder(new Connection($config), new Structure($model->getPrimaryKey()));
        return $builder;
    }
}

