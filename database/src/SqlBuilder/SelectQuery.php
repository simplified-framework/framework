<?php

namespace Simplified\Database\SqlBuilder;

class SelectQuery extends CommonQuery implements \Countable {

	private $fromTable, $fromAlias;

	function __construct(Builder $builder, $from) {
		$clauses = array(
			'SELECT' => ', ',
			'FROM' => null,
			'JOIN' => array($this, 'getClauseJoin'),
			'WHERE' => ' AND ',
			'GROUP BY' => ',',
			'HAVING' => ' AND ',
			'ORDER BY' => ', ',
			'LIMIT' => null,
			'OFFSET' => null,
			"\n--" => "\n--",
		);
		parent::__construct($builder, $clauses);

		# initialize statements
		$fromParts = explode(' ', $from);
		$this->fromTable = reset($fromParts);
		$this->fromAlias = end($fromParts);

		$this->statements['FROM'] = $from;
		$this->statements['SELECT'][] = $this->fromAlias . '.*';
		$this->joins[] = $this->fromAlias;
	}

	public function getFromTable() {
		return $this->fromTable;
	}

	public function getFromAlias() {
		return $this->fromAlias;
	}

	public function fetchColumn($columnNumber = 0) {
		if ($s = $this->execute()) {
			return $s->fetchColumn($columnNumber);
		}
		return false;
	}

	public function fetch($column = '') {
		$return = $this->execute();
		if ($return === false) {
			return false;
		}
		$return = $return->fetch();
		if ($return && $column != '') {
			if (is_object($return)) {
				return $return->{$column};
			} else {
				return $return[$column];
			}
		}
		return $return;
	}

	public function fetchPairs($key, $value, $object = false) {
		if ($s = $this->select(null)->select("$key, $value")->asObject($object)->execute()) {
			return $s->fetchAll(PDO::FETCH_KEY_PAIR);
		}
		return false;
	}

	public function fetchAll($index = '', $selectOnly = '') {
		if ($selectOnly) {
			$this->select(null)->select($index . ', ' . $selectOnly);
		}
		if ($index) {
			$data = array();
			foreach ($this as $row) {
				if (is_object($row)) {
					$data[$row->{$index}] = $row;
				} else {
					$data[$row[$index]] = $row;
				}
			}
			return $data;
		} else {
			if ($s = $this->execute()) {
				return $s->fetchAll();
			}
			return false;
		}
	}

	public function count() {
		$fpdo = clone $this;
		return (int) $fpdo->select(null)->select('COUNT(*)')->fetchColumn();
	}
}
