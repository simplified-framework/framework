<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 20.12.2015
 * Time: 15:55
 */

namespace Simplified\Database\Schema;

use Simplified\Core\Collection;
use Simplified\Database\Connection;

class Table {
    private $name;
    private $fields;

    public function __construct($name, Connection $driver) {
        $this->name = $name;
        $this->fields = new Collection();
        if ($driver->isConnected()) {
            try {
                $stmt = $driver->raw('DESC ' . $name);
                if ($stmt != null) {
                    if ($stmt->execute()) {
                        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'Simplified\\Database\\Schema\\TableField');
                        while ($record = $stmt->fetch()) {
                            $this->fields[$record->field] = $record;
                        }
                    }
                }
            } catch (\PDOException $e) {
                throw new DriverException("Unable to fetch table schema: " . $e->getMessage());
            }
        }
    }

    public function fields() {
        return $this->fields;
    }

    public function fieldNames() {
        return array_keys($this->fields()->toArray());
    }

    public function name() {
        return $this->name;
    }
}