<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 16.12.2015
 * Time: 13:05
 */

namespace Simplified\Config;


class Config {
    private static $loader;
    private static $configurations = array();
    public function __construct() {
        self::$loader = new PHPFileLoader();
    }

    public static function get($context, $key, $default = null) {
        if (self::$loader == null)
            new self();

        if (!Config::exists($context))
            return $default;

        if (!isset(self::$configurations[$context][$key])) {
            return $default;
        }

        return self::$configurations[$context][$key];
    }

    public static function getAll($context) {
        if (self::$loader == null)
            new self();

        if (!Config::exists($context))
            return array();

        if (!isset(self::$configurations[$context])) {
            return array();
        }

        return self::$configurations[$context];
    }

    public static function exists($context) {
        if (self::$loader == null)
            new self();

        if (!file_exists(CONFIG_PATH . $context . ".php"))
            return false;

        if (!array_key_exists($context, self::$configurations)) {
            $content = self::$loader->load(CONFIG_PATH . $context . ".php");
            self::$configurations[$context] = $content;
        }

        return isset(self::$configurations[$context]) && is_array(self::$configurations[$context]);
    }
}