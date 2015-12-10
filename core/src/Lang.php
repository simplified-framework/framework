<?php

class Lang {
	private function __construct() {
		// load config
		
		// set fallback language
	}
	
	// $key schema = file.lang_variable
	// $valus can contain replaces in syntax :name
	public static function get(string $key, array $values = array()) {
		// load language file
		// find name in language file array
		// replace placeholders
	}
}