<?php

namespace Simplified\Database\SqlBuilder;

class DeleteQuery extends CommonQuery {

	private $ignore = false;

	public function __construct(Builder $builder, $table) {
		$clauses = array(
			'DELETE FROM' => array($this, 'getClauseDeleteFrom'),
			'DELETE' => array($this, 'getClauseDelete'),
			'FROM' => null,
			'JOIN' => array($this, 'getClauseJoin'),
			'WHERE' => ' AND ',
			'ORDER BY' => ', ',
			'LIMIT' => null,
		);

		parent::__construct($builder, $clauses);

		$this->statements['DELETE FROM'] = $table;
		$this->statements['DELETE'] = $table;
	}

	public function ignore() {
		$this->ignore = true;
		return $this;
	}

	protected function buildQuery() {
		if ($this->statements['FROM']) {
			unset($this->clauses['DELETE FROM']);
		} else {
			unset($this->clauses['DELETE']);
		}
		return parent::buildQuery();
	}

	public function execute() {
		$result = parent::execute();
		if ($result) {
			return $result->rowCount();
		}
		return false;
	}

	protected function getClauseDelete() {
		return 'DELETE' . ($this->ignore ? " IGNORE" : '') . ' ' . $this->statements['DELETE'];
	}

	protected function getClauseDeleteFrom() {
		return 'DELETE' . ($this->ignore ? " IGNORE" : '') . ' FROM ' . $this->statements['DELETE FROM'];
	}
}
