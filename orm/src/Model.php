<?php

namespace Simplified\Orm;
use Simplified\Config\Config;
use Simplified\DBAL\DriverException;
use Simplified\DBAL\ConnectionException;
use ReflectionProperty;

class Model {
	private $driver = null;
    private $attributes = null;
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
		
		if (!isset($config[$connection]['driver'])) {
			throw new ConnectionException('Database driver parameters not set');
		}

        $class  = "Simplified\\DBAL\\Driver\\Connection";
        // No other drivers are currently supported

        if (null == $class || !class_exists($class)) {
            throw new DriverException('Unknown database driver "' . $config[$connection]['driver'].'": please, check your configuration!');
        }

        $this->driver = new $class($config[$connection]);
        $table = $this->getTable();
        var_dump( $this->driver->getDatabaseSchema());
        /*
        $tables = $this->driver->getDatabaseSchema()->toArray();

        if (!in_array($table, $tables)) {
            throw new ModelException('Unknown table ' . $this->getTable() . ' in database schema');
        }
        */
    }
	
	public function getTable() {
        $class = get_called_class();
        $ref = new ReflectionProperty($class, 'table');
		$table = $ref->getValue($this);
        if (null != $table) {
            return $table;
        }
        
		$parts = explode("\\", $class);
		$end = end($parts);
		return strtolower($end);
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
    
    public function getFieldNames() {
        return $this->getDriver()->getFieldNames($this->getTable());
    }
    
    public function getDriver() {
        if ($this->driver == null) {
            $this->init();
        }
        return $this->driver;
    }
	
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
}

