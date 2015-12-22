<?php

namespace Simplified\Database\SqlBuilder;

class Literal {
	protected $value = '';

	function __construct($value) {
		$this->value = $value;
	}

	function __toString() {
		return $this->value;
	}
}

