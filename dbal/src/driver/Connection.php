<?php

namespace Simplified\DBAL\Driver;
use Simplified\Core\NullPointerException;
use Simplified\DBAL\ConnectionException;

class Connection implements ConnectionInterface {
    private $_conn;
    private $_params = array();
    
    public function __construct(array $params = array()) {
        $this->_params = $params;
        $this->_conn = null;
        $this->connect();
    }
    
    public function connect() {
        $dsn = $this->getDriverName() . ":host=".$this->getHost().";dbname=".$this->getDatabase();
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
}