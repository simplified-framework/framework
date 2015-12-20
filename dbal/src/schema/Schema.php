<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 20.12.2015
 * Time: 16:03
 */

namespace Simplified\DBAL\Schema;

use Simplified\Core\Collection;
use Simplified\DBAL\Connection;
use Simplified\DBAL\DriverException;

class Schema {
    private $driver;
    private $schema;
    public function __construct(Connection $driver) {
        $this->driver = $driver;
        $this->loadSchema();
    }

    private function loadSchema() {
        $this->schema = new Collection();
        if ($this->driver->isConnected()) {
            try {
                $stmt = $this->driver->raw('SHOW TABLES');
                if ($stmt != null) {
                    if ($stmt->execute()) {
                        $stmt->setFetchMode(\PDO::FETCH_COLUMN, 0);
                        while ($name = $stmt->fetch()) {
                            $table = new Table($name, $this->driver);
                            $this->schema[$name] = $table;
                        }
                    }
                }
            } catch (\PDOException $e) {
                throw new DriverException("Unable to fetch database schema: " . $e->getMessage());
            }
        }

        return $this->schema;
    }

    public function table($name) {
        if (isset($this->schema[$name])) {
            return $this->schema[$name];
        }

        return null;
    }

    public function tables() {
        return $this->schema;
    }

    public function tableNames() {
        return array_keys($this->tables()->toArray());
    }
}