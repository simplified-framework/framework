<?php

namespace Simplified\Database\SqlBuilder;

abstract class BaseQuery implements \IteratorAggregate {
	private $builder;
	private $result;
	private $time;
	private $object = false;
	protected $clauses = array();
	protected $statements = array(), $parameters = array();

	protected function __construct(Builder $builder, $clauses) {
		$this->builder = $builder;
		$this->clauses = $clauses;
		$this->initClauses();
	}

	private function initClauses() {
		foreach ($this->clauses as $clause => $value) {
			if ($value) {
				$this->statements[$clause] = array();
				$this->parameters[$clause] = array();
			} else {
				$this->statements[$clause] = null;
				$this->parameters[$clause] = null;
			}
		}
	}

	protected function addStatement($clause, $statement, $parameters = array()) {
		if ($statement === null) {
			return $this->resetClause($clause);
		}
		# $statement !== null
		if ($this->clauses[$clause]) {
			if (is_array($statement)) {
				$this->statements[$clause] = array_merge($this->statements[$clause], $statement);
			} else {
				$this->statements[$clause][] = $statement;
			}
			$this->parameters[$clause] = array_merge($this->parameters[$clause], $parameters);
		} else {
			$this->statements[$clause] = $statement;
			$this->parameters[$clause] = $parameters;
		}
		return $this;
	}

	protected function resetClause($clause) {
		$this->statements[$clause] = null;
		$this->parameters[$clause] = array();
		if (isset($this->clauses[$clause]) && $this->clauses[$clause]) {
			$this->statements[$clause] = array();
		}
		return $this;
	}

	public function getIterator() {
		return $this->execute();
	}

	public function execute() {
		$query = $this->buildQuery();
		$parameters = $this->buildParameters();

		$result = $this->builder->getDriver()->prepare($query);

		// At this point, $result is a PDOStatement instance, or false.
		// PDO::prepare() does not reliably return errors. Some database drivers
		// do not support prepared statements, and PHP emulates them.  Postgres
		// does support prepared statements, but PHP does not call Postgres's
		// prepare function until we call PDOStatement::execute() (below).
		// If PDO::prepare() worked properly, this is where we would check
		// for prepare errors, such as invalid SQL.

		if ($this->object !== false) {
			if (class_exists($this->object)) {
				$result->setFetchMode(\PDO::FETCH_CLASS, $this->object);
			} else {
				$result->setFetchMode(\PDO::FETCH_OBJ);
			}
		} elseif ($this->builder->getDriver()->getAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE) == \PDO::FETCH_BOTH) {
			$result->setFetchMode(\PDO::FETCH_ASSOC);
		}

		$time = microtime(true);
		if ($result && $result->execute($parameters)) {
			$this->time = microtime(true) - $time;
		} else {
			$result = false;
		}

		$this->result = $result;
		$this->debugger();

		return $result;
	}

	private function debugger() {
		if ($this->builder->debug) {
			if (!is_callable($this->builder->debug)) {
				$backtrace = '';
				$query = $this->getQuery();
				$parameters = $this->getParameters();
				$debug = '';
				if ($parameters) {
					$debug = "# parameters: " . implode(", ", array_map(array($this, 'quote'), $parameters)) . "\n";
				}
				$debug .= $query;
				$pattern = '(^' . preg_quote(dirname(__FILE__)) . '(\\.php$|[/\\\\]))'; // can be static
				foreach (debug_backtrace() as $backtrace) {
					if (isset($backtrace["file"]) && !preg_match($pattern, $backtrace["file"])) {
						// stop on first file outside FluentPDO source codes
						break;
					}
				}
				$time = sprintf('%0.3f', $this->time * 1000) . ' ms';
				$rows = ($this->result) ? $this->result->rowCount() : 0;
				fwrite(STDERR, "# $backtrace[file]:$backtrace[line] ($time; rows = $rows)\n$debug\n\n");
			} else {
				call_user_func($this->builder->debug, $this);
			}
		}
	}

	protected function getDriver() {
		return $this->builder->getDriver();
	}

	protected function getStructure() {
		return $this->builder->getStructure();
	}

	public function getResult() {
		return $this->result;
	}

	public function getTime() {
		return $this->time;
	}

	public function getParameters() {
		return $this->buildParameters();
	}

	public function getQuery($formated = true) {
		$query = $this->buildQuery();
		if ($formated) $query = Utils::formatQuery($query);
		return $query;
	}

	protected function buildQuery() {
		$query = '';
		foreach ($this->clauses as $clause => $separator) {
			if ($this->clauseNotEmpty($clause)) {
				if (is_string($separator)) {
					$query .= " $clause " . implode($separator, $this->statements[$clause]);
				} elseif ($separator === null) {
					$query .= " $clause " . $this->statements[$clause];
				} elseif (is_callable($separator)) {
					$query .= call_user_func($separator);
				} else {
					throw new Exception("Clause '$clause' is incorrectly set to '$separator'.");
				}
			}
		}
		return trim($query);
	}

	private function clauseNotEmpty($clause) {
		if ($this->clauses[$clause]) {
			return (boolean) count($this->statements[$clause]);
		} else {
			return (boolean) $this->statements[$clause];
		}
	}

	private function buildParameters() {
		$parameters = array();
		foreach ($this->parameters as $clauses) {
			if (is_array($clauses)) {
				foreach ($clauses as $value) {
					if (is_array($value) && is_string(key($value)) && substr(key($value), 0, 1) == ':') {
						// this is named params e.g. (':name' => 'Mark')
						$parameters = array_merge($parameters, $value);
					} else {
						$parameters[] = $value;
					}
				}
			} else {
				if ($clauses) $parameters[] = $clauses;
			}
		}
		return $parameters;
	}

	protected function quote($value) {
		if (!isset($value)) {
			return "NULL";
		}
		if (is_array($value)) { // (a, b) IN ((1, 2), (3, 4))
			return "(" . implode(", ", array_map(array($this, 'quote'), $value)) . ")";
		}
		$value = $this->formatValue($value);
		if (is_float($value)) {
			return sprintf("%F", $value); // otherwise depends on setlocale()
		}
		if ($value === false) {
			return "0";
		}
		if (is_int($value) || $value instanceof Literal) { // number or SQL code - for example "NOW()"
			return (string) $value;
		}
		return $this->builder->getDriver()->quote($value);
	}

	private function formatValue($val) {
		if ($val instanceof DateTime) {
			return $val->format("Y-m-d H:i:s"); //! may be driver specific
		}
		return $val;
	}

	public function asObject($object = true) {
		$this->object = $object;
		return $this;
	}

}

