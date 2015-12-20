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

    public function __construct() {
    }

    public function log($level, $message, array $context = array()) {
        if ($this->ensureLogDir()) {
            $file = self::$logdir . DIRECTORY_SEPARATOR . "messages.txt";
            $lastLine = $this->getLastFileLine($file);
            if (strstr($lastLine, $message) !== false) {
                return;
            }

            switch ($level) {
                case LogLevel::ERROR:
                    $prefix = "[ERROR]";
                    break;
                case LogLevel::ALERT:
                    $prefix = "[ALERT]";
                    break;
                case LogLevel::CRITICAL:
                    $prefix = "[CRITICAL]";
                    break;
                case LogLevel::DEBUG:
                    $prefix = "[DEBUG]";
                    break;
                case LogLevel::EMERGENCY:
                    $prefix = "[EMERG]";
                    break;
                case LogLevel::INFO:
                    $prefix = "[INFO]";
                    break;
                case LogLevel::NOTICE:
                    $prefix = "[NOTICE]";
                    break;
                case LogLevel::WARNING:
                    $prefix = "[WARN]";
                    break;
                default:
                    $prefix = "[UNNOWN]";
                    break;
            }
            if (($fp = fopen($file, "a"))) {
                $line = (new \DateTime())->format('Y-m-d H:i:s');
                $line .= " " . $prefix;
                $line .= " " . trim($message) . PHP_EOL;
                fwrite($fp, $line);
                fclose($fp);
            }
        }
    }

    private function getLastFileLine($file) {
        if (!file_exists($file))
            return "";

        $content = file_get_contents($file);
        $lines = explode(PHP_EOL, $content);
        $last = "";

        for ($i = 0; $i < count($lines); $i++) {
            if ($i == count($lines)-2) {
                $last = $lines[$i];
                break;
            }
        }

        return $last;
    }

    private function ensureLogDir() {
        self::$logdir = STORAGE_PATH . DIRECTORY_SEPARATOR . 'log';
        if (!file_exists(self::$logdir)) {
            mkdir(self::$logdir, 0775, true);
        }

        return is_writable(self::$logdir);
    }
}