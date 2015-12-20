<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 20.12.2015
 * Time: 16:03
 */

namespace Simplified\DBAL\Schema;

use Simplified\DBAL\Connection;

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
                        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'Simplified\\DBAL\\Schema\\Table');
                        while ($record = $stmt->fetch()) {
                            $this->schema[] = $record;
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