<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 30.12.2015
 * Time: 16:32
 */

namespace Simplified\FileSystem;

class Finder implements \Iterator {
    const TYPE_ALL  = 1;
    const TYPE_FILE = 2;
    const TYPE_DIRECTORY  = 3;

    private $position;
    private $container;
    private $content;
    private $type;

    public function __construct() {
        $this->type = Finder::TYPE_ALL;
        $this->position = 0;
    }

    public function files() {
        $this->type = Finder::TYPE_FILE;
        return $this;
    }

    public function directories() {
        $this->type = Finder::TYPE_DIRECTORY;
        return $this;
    }

    public function name() {
        return $this;
    }

    public function date(\DateTime $date) {
        return $this;
    }

    public function size($size) {
        return $this;
    }

    public function in(FinderContainer $container) {
        $this->container = $container;
        return $this;
    }

    public function current() {
        return $this->content->get($this->position);
    }

    public function next() {
        ++$this->position;
    }

    public function key() {
        return $this->position;
    }

    public function valid() {
        return $this->content->has($this->position);
    }

    public function rewind() {
        $this->content = null;
        switch ($this->type) {
            case Finder::TYPE_ALL:
                $this->content = $this->container->all();
                break;
            case Finder::TYPE_DIRECTORY:
                $this->content = $this->container->directories();
                break;
            case Finder::TYPE_FILE:
                $this->content = $this->container->files();
                break;
        }
        $this->position = 0;
    }
}