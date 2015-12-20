<?php

namespace Simplified\DBAL\Driver;

interface ConnectionInterface {
    // connection data
    public function getUsername();
    public function getPassword();
    public function getDatabase();
    public function getDriverName();
    public function connect();
    public function isConnected();
    public function close();

    // basic table actions
    public function select(string $query, array $bindings = array());
    public function insert(string $query, array $bindings = array());
    public function update(string $query, array $bindings = array());
    public function delete(string $query, array $bindings = array());

    // database / table info
    public function getDatabaseSchema();
    public function describeTable(string $table);
    public function getFieldNames(string $table);
}