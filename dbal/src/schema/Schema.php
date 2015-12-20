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
    }

    public function getDatabase() {
        $this->schema = new Collection();
        if ($this->driver->isConnected()) {
            try {
                $stmt = $this->driver->raw('SHOW TABLES');
                if ($stmt != null) {
                    if ($stmt->execute()) {
                        $stmt->setFetchMode(\PDO::FETCH_COLUMN);
                        while ($record = $stmt->fetch()) {
                            $table = new Table($this->driver);
                            $table->name = $record;
                            $this->schema[] = $table;
                        }
                    }
                }
            } catch (\PDOException $e) {
                throw new DriverException("Unable to fetch database schema: " . $e->getMessage());
            }
        }

        var_dump($this->schema);

        return $this->schema;
    }

    public function getTable($name) {

    }
}