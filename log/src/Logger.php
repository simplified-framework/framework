<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 19.12.2015
 * Time: 09:27
 */

namespace Simplified\Log;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger {
    private static $logdir;

    public function log($level, $message, array $context = array()) {
        if ($this->ensureLogDir()) {
            $fp = fopen(self::$logdir . DIRECTORY_SEPARATOR . $level . ".txt", "a");
            if ($fp) {
                $line = (new \DateTime())->format('Y-m-d H:i:s');
                $line .= " " . trim($message) . "\n";
                fwrite($fp, $line);
                fclose($fp);
            }
        }
    }

    private function ensureLogDir() {
        self::$logdir = STORAGE_PATH . DIRECTORY_SEPARATOR . 'log';
        if (!file_exists(self::$logdir)) {
            mkdir(self::$logdir, 0775, true);
        }

        return is_writable(self::$logdir);
    }
}