<?php

namespace Simplified\DBAL;
use Simplified\Core\IllegalArgumentException;
use Simplified\Core\NullPointerException;
use Simplified\DBAL\Schema\Schema;

class Connection implements ConnectionInterface {
    private $_conn;
    private $_params = array();
    private $schema;
    
    public function __construct(array $params = array()) {
        $this->_params = $params;
        $this->_conn = null;
        if ($this->connect() ){
            $this->schema = new Schema($this);
        }
    }

    public function getDatabaseSchema() {
        return $this->schema;
    }

    public function connect() {
        $dsn = $this->getDriverName() . ":host=".$this->getHost().";dbname=".$this->getDatabase().';charset=utf8';
        try {
            $this->_conn = new \PDO($dsn, $this->getUsername(), $this->getPassword(),
                array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_PERSISTENT => true));
        } catch (\PDOException $e) {
            throw  new ConnectionException($e->getMessage() . ", " . $dsn);
        }

        if ($this->_conn == null ) {
            throw new NullPointerException('\PDO::__construct('.$dsn.') returned null');
        }

        return $this->isConnected();
    }
    public function close() {
        if ($this->isConnected()) {
            $this->_conn = null;
            unset($this->_conn);
        }
    }

    public function isConnected() {
        return $this->_conn == null ? false : true;
    }
    
    public function getHost() {
        return isset($this->_params['host']) ? $this->_params['host'] : "";
    }
    
    public function getPort() {
        return isset($this->_params['port']) ? intval($this->_params['port']) : 0;
    }
    
    public function getUsername() {
        return isset($this->_params['user']) ? $this->_params['user'] : "";
    }
    
    public function getPassword() {
        return isset($this->_params['password']) ? $this->_params['password'] : "";
    }
    
    public function getDatabase() {
        return isset($this->_params['database']) ? $this->_params['database'] : "";
    }

    public function getDriverName() {
        return isset($this->_params['driver']) ? $this->_params['driver'] : "";
    }

    public function raw($query) {
        $stmt = null;
        if ($this->isConnected()) {
            $stmt = $this->_conn->query($query);
        }
        return $stmt;
    }

    public function select($query, array $bindings = array()) {
        if (stristr($query, "select ") !== 0)
            throw new IllegalArgumentException("Query isnt a valid select statement (" . $query . ")");

        if ($this->isConnected()) {

        }
    }

    public function insert($query, array $bindings = array()) {
        if (stristr($query, "insert into") !== 0)
            throw new IllegalArgumentException("Query isn't a valid insert statement (" . $query . ")");

        if ($this->isConnected()) {

        }
    }

    public function update($query, array $bindings = array()) {
        if (stristr($query, "update ") !== 0)
            throw new IllegalArgumentException("Query isn't a valid update statement (" . $query . ")");

        if ($this->isConnected()) {

        }
    }

    public function delete($query, array $bindings = array()) {
        if (stristr($query, "delete from ") !== 0)
            throw new IllegalArgumentException("Query isn't a valid delete statement (" . $query . ")");

        if ($this->isConnected()) {

        }
    }
}