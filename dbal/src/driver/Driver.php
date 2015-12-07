<?php

namespace Simplified\DBAL\Driver;

interface Driver {
    // basic methods
    public function getUsername();
    public function getPassword();
    public function getDatabase();
    public function getTables();
    public function getName();
    
    // connection related
    public function connect();
    public function close();
    public function isConnected();
    
    // options
    public function setOptions($opts);
    public function getOptions();
    
    // raw sql query
    public function rawQuery($sql);
    
    // get field names from table
    public function getFieldNames($table);
}