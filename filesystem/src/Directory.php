<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 29.12.2015
 * Time: 19:06
 */

namespace Simplified\FileSystem;

use Simplified\Core\Collection;
use Simplified\Core\IllegalArgumentException;

class Directory extends \SplFileInfo implements FinderContainer, FinderItem {
    private $path;
    private $items;

    public static function create($path, $mode = 777, $recursive = true) {
        if (mkdir($path, $mode, $recursive)) {
            return new self($path);
        }

        throw new IllegalArgumentException("Unable to create directory %s", basename($path));
    }

    public static function currentDir() {
        return new self(getcwd());
    }

    public function __construct($path) {
        parent::__construct($path);
        if (PHP_OS == "WINNT") {
            if (strpos($path, ":") == 1) {
                $path = substr($path, 2, strlen($path));
            }
        }
        $path = str_replace("\\","/", $path);
        $this->items = new Collection();

        $this->path = $path;
        if ($this->exists()) {
            if ($this->isReadable()) {
                $handle = opendir($this->path());
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        $this->items->add($entry);
                    }
                }
                closedir($handle);
            }
        }
    }

    public function isAbsolutePath() {
        if( strpos($this->path(), "/") === 0)
            return true;

        return false;
    }

    public function isRelativePath() {
        return strpos($this->path(), ".") === 0;
    }

    public function isReadable() {
        return is_readable($this->path());
    }

    public function isWritable() {
        return is_writable($this->path());
    }

    public function absolutePath() {
        if ($this->isAbsolutePath())
            return $this->path();

        $name = $this->name();
        $dir = self::currentDir();

        $path = $dir->path() . DIRECTORY_SEPARATOR . $name;
        return $path;
    }

    public function path() {
        return $this->path;
    }

    public function name() {
        return basename($this->path());
    }

    public function files() {
        if ($this->items->count() == 0)
            return new Collection();

        $files = new Collection();
        if ($this->exists()) {
            foreach ($this->items->all() as $entry) {
                $path = $this->absolutePath() . DIRECTORY_SEPARATOR . $entry;
                if (is_file($path)) {
                    $files->add(new File($path));
                }
            }
        }
        return $files;
    }

    public function directories() {
        if ($this->items->count() == 0)
            return new Collection();

        $dirs = new Collection();
        if ($this->exists()) {
            foreach ($this->items->all() as $entry) {
                $path = $this->absolutePath() . DIRECTORY_SEPARATOR . $entry;
                if (is_dir($path)) {
                    $dirs->add(new Directory($path));
                }
            }
        }
        return $dirs;
    }

    public function exists() {
        return is_dir($this->path);
    }

    public function id() {
        return md5($this->path() ."@" . __CLASS__);
    }

    public function get($id) {
        return $this->items->get($id);
    }

    public function has($id) {
        return $this->items->has($id);
    }
}