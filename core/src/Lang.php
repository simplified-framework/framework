<?php

namespace Simplified\Core;
use Simplified\Config\Config;
use Simplified\Config\PHPFileLoader;

/* TODO implement fallback when nothing is found in current language, maybe its in the default language */

class Lang {
    private static $language;
    private static $fallback;
    private static $cache = array();
    
	private function __construct() {
        $language = Config::get('language.language');
        $fallback = Config::get('language.default');

        if ($language == null || $fallback == null)
            throw new LanguageException('No language variable configured.');

        self::$language = I18N_PATH . $language . DIRECTORY_SEPARATOR;
        self::$fallback = I18N_PATH . $fallback . DIRECTORY_SEPARATOR;
	}
	
	// $key schema = file.lang_variable
	// $values can contain replaces in syntax :name
	public static function get($key, array $values = array()) {
        if (self::$language == null) {
            new self();
        }

        if (strstr($key, ".") === false)
            throw new LanguageException('No namespace in translation value found.');

        $parts = explode(".", $key);
        $filepath = self::$language . $parts[0] . ".php";

        if (!file_exists($filepath)) {
        	$filepath = self::$fallback . $parts[0] . ".php";
        	if (!file_exists($filepath)) {
        		throw new LanguageException('Unable to open default translation file at ' . $filepath);
        	}
        }
        $file_md5 = md5($filepath);

        if (isset(self::$cache[$file_md5])) {
            // load language cache
            $translations = self::$cache[$file_md5];
        }
        else {
            // load language file
            $loader = new PHPFileLoader();
            $translations = $loader->load($filepath, array());
            self::$cache[$file_md5] = $translations;
        }

        if (!isset($translations[$parts[1]])) {
        	throw new LanguageException('Unable to find translation for ' . $parts[1]);
        }
        
        $translatedText = $translations[$parts[1]];
        
		// replace placeholders
        if ($values != null) {
            foreach ($values as $key => $value) {
                $translatedText = str_replace(":$key", $value, $translatedText);
            }
        }
		
        return $translatedText;
	}
}