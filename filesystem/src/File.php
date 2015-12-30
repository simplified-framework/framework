<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 30.12.2015
 * Time: 11:30
 */

namespace Simplified\FileSystem;


class File extends \SplFileInfo {
    public function __construct($path) {
        if (PHP_OS == "WINNT") {
            if (strpos($path, ":") == 1) {
                $path = substr($path, 2, strlen($path));
            }
        }
        $path = str_replace("\\","/", $path);
        parent::__construct($path);
    }
}