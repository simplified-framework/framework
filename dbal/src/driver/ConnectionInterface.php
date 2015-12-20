<?php

namespace Simplified\DBAL\Driver;

interface ConnectionInterface {
    public function getUsername();
    public function getPassword();
    public function getDatabase();
    public function getDriverName();
    public function connect();
    public function close();
    public function isConnected();
}