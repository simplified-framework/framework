<?php

namespace Simplified\Database\SqlBuilder;

use Simplified\Config\Config;
use Simplified\Database\Connection;
use Simplified\Database\ConnectionException;

class Builder {

	private $driver, $structure;
	public $debug;

	function __construct(Connection $connection = null, Structure $structure = null) {
		if ($connection == null) {
			$conf = Config::get('database', 'default');
			if (empty($conf))
				throw new ConnectionException('No default database configuration found');

			$connection = new Connection($conf);
		}
		$this->driver = $connection;
		if (!$structure) {
			$structure = new Structure;
		}
		$this->structure = $structure;
	}

	public function select($table, $primaryKey = null) {
		$query = new SelectQuery($this, $table);
		if ($primaryKey) {
			$tableTable = $query->getFromTable();
			$tableAlias = $query->getFromAlias();
			$primaryKeyName = $this->structure->getPrimaryKey($tableTable);
			$query = $query->where("$tableAlias.$primaryKeyName", $primaryKey);
		}
		return $query;
	}

	public function insert($table, $values = array()) {
		$query = new InsertQuery($this, $table, $values);
		return $query;
	}

	public function update($table, $set = array(), $primaryKey = null) {
		$query = new UpdateQuery($this, $table);
		$query->set($set);
		if ($primaryKey) {
			$primaryKeyName = $this->getStructure()->getPrimaryKey($table);
			$query = $query->where($primaryKeyName, $primaryKey);
		}
		return $query;
	}

	public function delete($table, $primaryKey = null) {
		$query = new DeleteQuery($this, $table);
		if ($primaryKey) {
			$primaryKeyName = $this->getStructure()->getPrimaryKey($table);
			$query = $query->where($primaryKeyName, $primaryKey);
		}
		return $query;
	}

	public function deleteFrom($table, $primaryKey = null) {
		$args = func_get_args();
		return call_user_func_array(array($this, 'delete'), $args);
	}

	public function getDriver() {
		return $this->driver;
	}

	public function getStructure() {
		return $this->structure;
	}
}
