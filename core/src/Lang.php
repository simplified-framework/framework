<?php

namespace Simplified\Core;
use Simplified\Config\PHPFileLoader;

class Lang {
    private static $i18n;
	private function __construct() {
        $loader = new PHPFileLoader();
        $params = $loader->load(CONFIG_PATH . 'language.php', array());

        $language = isset($params['language']) ? $params['language'] : null;
        if ($language == null) {
            $language = isset($params['default']) ? $params['default'] : null;
        }

        if ($language == null)
            throw new LanguageException('No language variable configured.');

        self::$i18n = I18N_PATH . $language . DIRECTORY_SEPARATOR;
	}
	
	// $key schema = file.lang_variable
	// $values can contain replaces in syntax :name
	public static function get($key, array $values = array()) {
        if (self::$i18n == null) {
            new self();
        }

        if (strstr($key, ".") === FALSE)
            throw new LanguageException('No namespace in translation value found.');

        $parts = explode(".", $key);
        $lang_filepath = self::$i18n . $parts[0] . ".php";

        if (!file_exists($lang_filepath)) {
        	throw new LanguageException('Unable to open translation file at ' . $lang_filepath);
        }

		// load language file
		$loader = new PHPFileLoader();
        $translations = $loader->load($lang_filepath, array());
        
        if (!isset($translations[$parts[1]])) {
        	throw new LanguageException('Unable to find translation for ' . $parts[1]);
        }
        
        $translatedText = $translations[$parts[1]];
        
		// TODO replace placeholders
	}
}