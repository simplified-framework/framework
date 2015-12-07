<?php

namespace Simplified\DBAL\Driver;
use Simplified\DBAL\ConnectionException;
use Simplified\DBAL\ModelException;
use Simplified\DBAL\NullPointerException;
use Simplified\DBAL\DriverException;
use Simplified\Core\Collection;

class MySqliDriver implements Driver {
    private $_conn;
    private $_params = array();
    
    public function __construct($params) {        
        $this->_params = is_array($params) ? $params : array();        
        $this->_conn = null;
        $this->connect();
    }
    
    public function getName() {
        return "mysqli";
    }
    
    public function setOptions($opts) {
        if (!is_array($opts) || empty($opts)) {
            throw new DriverException("Unable to set empty options");
        }
        
        $this->options = $opts;
        foreach ($this->options as $key => $val) {
            $this->_conn->options((int)$key, $val);
        }
    }
    
    public function getOptions() {
        return $this->options;
    }
    
    public function connect() {
        $this->_conn = new \mysqli($this->getHost(), $this->getUsername(), $this->getPassword(),
            $this->getDatabase(), $this->getPort());
        
        if ($this->_conn == null ) {
            throw new NullPointerException('mysqli::__construct(host, user, password, database, port) returned null');
        }
        
        if ($this->_conn->connect_error ) {
            throw new ConnectionException($this->_conn->connect_error);
        }
        
        return $this->isConnected();
    }
    public function close() {
        if ($this->isConnected()) {
            $this->_conn->close();
            unset($this->_conn);
        }
    }

    public function isConnected() {
        return $this->_conn == null || $this->_conn->connect_error ? false : true;
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
    
    public function rawQuery($sql) {
        $result = $this->isConnected() ? $this->_conn->query($sql) : null;
        if ($this->_conn->error != null) {
            throw new ModelException("SQL Error: " . $sql/*$this->_conn->error*/);
        }
        $data = new Collection();

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = ($row);
            }
        }

        return $data;
    }

    public function getTables() {
        $tables = new Collection();
        if ($this->isConnected()){
            $rows = $this->rawQuery("SHOW TABLES");
            foreach ($rows as $row)
                $tables[] = ($row['Tables_in_' . $this->_params['database']]);
        }

        return $tables;
    }
    
    public function getFieldNames($table) {
        $names = new Collection();
        $rows = $this->rawQuery("DESC " . $table);
        foreach ($rows as $row)
            $names[] = $row['Field'];

        return $names;
    }
}