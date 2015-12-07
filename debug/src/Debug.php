<?php

namespace Simplified\Debug;
use Simplified\Core\PHPFileLoader;

class Debug {
    public static function handleDebug() {
        // load debug config
        // enable disable debug by config values
        ini_set('display_errors', 'off');
        error_reporting(E_ALL);

        // set errorHandler
        // set exception handler
        // set shutdown handler
        set_error_handler(array(new ErrorHandler(), 'handleError'), E_ALL);
        set_exception_handler(array(new ErrorHandler(), 'handleException'));
        register_shutdown_function(array(new ErrorHandler(), 'handleShutdown'));
    }
}