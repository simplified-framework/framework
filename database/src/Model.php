<?php

namespace Simplified\Database;

use Simplified\Config\Config;
use Simplified\Database\SqlBuilder\Builder;
use ReflectionProperty;

class Model {
	private $builder = null;
    private $attributes = null;
    private $metadata;
    static  $hasMany;
	static  $connection;
    static  $primaryKey;
    static  $table;
    static  $instance;
	
	public function __construct($attributes = null) {
        if ($attributes)
            $this->attributes = $attributes;
        //$this->init();
    }
    
    public function __destruct() {
        if ($this->builder != null && $this->builder->getDriver() != null)
            $this->builder->getDriver()->close();
    }

    private function init() {
		$config = Config::getAll('database');
		if (empty($config) || count($config) == 0) {
			throw new ConnectionException('No database configuration found');
		}

        $ref = new ReflectionProperty(get_called_class(), 'connection');
        $connection = $ref->getValue($this);
		if (null == $connection) {
			$connection = "default";
		}
		
		if (!isset($config[$connection])) {
			throw new ConnectionException('No database configuration named "' . $connection . '" found');
		}

        $model_class = get_called_class();
        $ref = new ReflectionProperty($model_class, 'table');
        $table_name = $ref->getValue($this);
        if (!$table_name) {
            $table_name = strtolower(basename($model_class));
        }

        $connection = new Connection($config[$connection]);
        $this->builder = new Builder($connection);
        $this->metadata = $connection->getDatabaseSchema()->table($table_name);

        if (!$this->metadata) {
            throw new ModelException('Unknown table for Model '.get_called_class().' in database schema');
        }
    }
	
	public function getTable() {
        return $this->metadata;
	}

    public function getPrimaryKey() {
        $class = get_called_class();
        $ref = new ReflectionProperty($class, 'primaryKey');
        $key = $ref->getValue($this);
        if (null != $key) {
            return $key;
        }

        return 'id';
    }

    public static function all() {
        $model_class = get_called_class();
        $instance = new $model_class();
        //$table = $instance->getTable()->name();

        $model_class = get_called_class();
        $ref = new ReflectionProperty($model_class, 'table');
        $table_name = $ref->getValue($instance);
        if (!$table_name) {
            $table_name = strtolower(basename($model_class));
        }

        $driver = new Builder();
        // TODO check return value from PDO
        return $driver->select($table_name)->asObject($model_class)->execute()->fetchAll();
    }

    public static function find($id) {
        $class = get_called_class();
        $instance = new $class();
        $table = $instance->getTable()->name();

        $driver = new Builder();
        // TODO check return value from PDO
        return $driver->select($table)->where('id', array($id))->asObject($class)->execute()->fetch();
    }

    public static function where ($field, $condition, $value) {
        $class = get_called_class();
        return $class::all()->where($field, $condition, $value);
    }

/*
    public function __get($name) {
        if (in_array($name, $this->getFieldNames()->toArray())) {
            if ($this->getProperty($name))
                return $this->getProperty($name);
        }
        else
            if (method_exists($this, $name) && !($this->getProperty($name))) {
                $data = call_user_func(array($this, $name));
                if (get_class($data) == "Simplified\\Core\\Collection")
                    $data = $data->toArray();

                $this->$name = $data;
                return $data;
            }
            else
                if ($this->getProperty($name)) {
                    return $this->getProperty($name);
                }

        return null;
    }

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

    public function __get($name) {
        if (in_array($name, $this->getFieldNames()->toArray())) {
            if ($this->getProperty($name))
                return $this->getProperty($name);
        }
        else
        if (method_exists($this, $name) && !($this->getProperty($name))) {
            $data = call_user_func(array($this, $name));
            if (get_class($data) == "Simplified\\Core\\Collection")
                $data = $data->toArray();

            $this->$name = $data;
            return $data;
        }
        else
        if ($this->getProperty($name)) {
            return $this->getProperty($name);
        }

        return null;
    }

    public function __set($key, $val) {
        $this->$key = $val;
    }

    public static function where ($field, $condition, $value) {
        $class = get_called_class();
        return $class::all()->where($field, $condition, $value);
    }

    public function delete() {
        // TODO who can delete this record?
    }

    public function save() {
        // TODO who can save this record?
    }
    */
}

