<?php

namespace Simplified\Core;
use Simplified\Config\PHPFileLoader;

class Lang {
    private static $language;
    private static $fallback;
    
	private function __construct() {
        $loader = new PHPFileLoader();
        $params = $loader->load(CONFIG_PATH . 'language.php', array());

        $language = isset($params['language']) ? $params['language'] : null;
        if ($language == null) {
            $language = isset($params['default']) ? $params['default'] : null;
        }
        
        $fallback = isset($params['default']) ? $params['default'] : null;

        if ($language == null)
            throw new LanguageException('No language variable configured.');
        
        if ($fallback == null)
            throw new LanguageException('No default language variable configured.');

        self::$language = I18N_PATH . $language . DIRECTORY_SEPARATOR;
        self::$fallback = I18N_PATH . $fallback . DIRECTORY_SEPARATOR;
	}
	
	// $key schema = file.lang_variable
	// $values can contain replaces in syntax :name
	public static function get($key, array $values = array()) {
        if (self::$language == null) {
            new self();
        }

        if (strstr($key, ".") === FALSE)
            throw new LanguageException('No namespace in translation value found.');

        $parts = explode(".", $key);
        $filepath = self::$language . $parts[0] . ".php";

        if (!file_exists($filepath)) {
        	$filepath = self::$fallback . $parts[0] . ".php";
        	if (!file_exists($filepath)) {
        		throw new LanguageException('Unable to open default translation file at ' . $filepath);
        	}
        }

		// load language file
		$loader = new PHPFileLoader();
        $translations = $loader->load($filepath, array());
        
        if (!isset($translations[$parts[1]])) {
        	throw new LanguageException('Unable to find translation for ' . $parts[1]);
        }
        
        $translatedText = $translations[$parts[1]];
        
		// TODO replace placeholders
		
        return $translatedText;
	}
}