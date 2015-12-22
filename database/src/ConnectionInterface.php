<?php

namespace Simplified\Database;

interface ConnectionInterface {
    // connection data
    public function getUsername();
    public function getPassword();
    public function getDatabase();
    public function getDriverName();
    public function connect();
    public function isConnected();
    public function close();

    // schema
    public function getDatabaseSchema();

    // basic table actions
    public function select($query, array $bindings = array());
    public function insert($query, array $bindings = array());
    public function update($query, array $bindings = array());
    public function delete($query, array $bindings = array());
}