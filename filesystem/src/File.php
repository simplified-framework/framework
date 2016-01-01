<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 30.12.2015
 * Time: 11:30
 */

namespace Simplified\FileSystem;

class File extends \SplFileInfo implements FinderItem {
    public function __construct($path) {
        if (PHP_OS == "WINNT") {
            if (strpos($path, ":") == 1) {
                $path = substr($path, 2, strlen($path));
            }
        }
        $path = str_replace("\\","/", $path);
        parent::__construct($path);
    }

    public function name() {
        return $this->getFilename();
    }

    public function id() {
        return md5($this->getPath() . "/" . $this->name() ."@" . __CLASS__);
    }

    public function size() {
        return $this->getSize();
    }

    public function timestamp() {
        return $this->getMTime();
    }
}