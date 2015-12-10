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
        $filepath = self::$i18n . $parts[0] . ".php";

        if (!file_exists($filepath))
            throw new LanguageException('Unable to open translation file at ' . $filepath);

		// TODO load language file

		// TODO replace placeholders
	}
}