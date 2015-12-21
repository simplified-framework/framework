<?php

namespace Simplified\Orm;

use Simplified\Config\Config;
use Simplified\DBAL\Connection;
use Simplified\DBAL\ConnectionException;
use Simplified\DBAL\ModelException;
use ReflectionProperty;

class Model {
	private $driver = null;
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
        $this->init();
    }
    
    public function __destruct() {
        if ($this->driver != null)
            $this->driver->close();
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

        $this->driver = new Connection($config[$connection]);
        $this->metadata = $this->driver->getDatabaseSchema()->table($table_name);

        if (!$this->metadata) {
            throw new ModelException('Unknown table ' . $this->getTable()->name() . ' in database schema');
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

    /*
	public static function all() {
        $class = get_called_class();
        $instance = new $class();
        $query = new Query($instance);
        return $query->select("*")->from($instance->getTable())->get();
	}
    
    public static function find($id) {
        $class = get_called_class();        
        $instance = new $class();
        $pk = $instance->getTable() . "." . $instance->getPrimaryKey();
        $query = new Query($instance);
        return $query->select("*")->from($instance->getTable())->where($pk, "=", intval($id))->get();
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

