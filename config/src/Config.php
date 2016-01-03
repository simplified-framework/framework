<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 16.12.2015
 * Time: 13:05
 */

namespace Simplified\Config;

use Simplified\Cache\ApcCache;

class Config {
    private static $loader;
    private static $cache;

    public function __construct() {
        self::$loader = new PHPFileLoader();
        self::$cache  = new ApcCache();
    }

    public static function get($key, $default = null) {
        if (self::$loader == null)
            new self();

        if (!self::$cache->has($key)) {
            $parts = explode(".", $key);
            $context = $parts[0];
            if (!file_exists(CONFIG_PATH . $context . ".php"))
                return $default;

            $records = self::$loader->load(CONFIG_PATH . $context . ".php");
            foreach ($records as $k => $v) {
                $record = "$context.$k";
                self::$cache->set($record, $v);
            }
        }

        if (self::$cache->has($key)) {
            return self::$cache->get($key);
        }

        return $default;
    }

    public static function getAll($context) {
        if (self::$loader == null)
            new self();

        if (!file_exists(CONFIG_PATH . $context . ".php"))
            return array();

        $records = self::$loader->load(CONFIG_PATH . $context . ".php");
        return $records;
    }
}