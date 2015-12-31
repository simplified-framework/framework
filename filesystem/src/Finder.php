<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 30.12.2015
 * Time: 16:32
 */

namespace Simplified\FileSystem;

use Simplified\Core\Collection;
use Simplified\FileSystem\Filter\DateTimeFilter;
use Simplified\FileSystem\Filter\NameFilter;
use Simplified\FileSystem\Filter\SizeFilter;

class Finder implements \Iterator {
    const TYPE_ALL  = 1;
    const TYPE_FILE = 2;
    const TYPE_DIRECTORY  = 3;

    private $position;
    private $container;
    private $content;
    private $type;
    private $filters;
    private $items;

    public function __construct() {
        $this->type = Finder::TYPE_ALL;
        $this->position = 0;
        $this->items = array();
    }

    public function in(FinderContainer $container) {
        $this->container = $container;
        return $this;
    }

    public function files() {
        if (!$this->container)
            throw new FinderException("No container was set");

        $this->type = Finder::TYPE_FILE;
        $items = $this->container->files();
        foreach ($items as $item) {
            $this->items[] = array($item->id() => $item);
        }
        return $this;
    }

    public function directories() {
        if (!$this->container)
            throw new FinderException("No container was set");

        $this->type = Finder::TYPE_DIRECTORY;
        $items = $this->container->directories();
        foreach ($items as $item) {
            $this->items[] = array($item->id() => $item);
        }
        return $this;
    }

    public function name($expr) {
        $filter = new NameFilter($expr);
        $filtered = array();
        foreach ($this->items as $key => $item) {
            if ($filter->filter(end($item))) {
                $filtered[] = $item;
            }
        }
        $this->items = $filtered;

        return $this;
    }

    public function date($expr) {
        $filter = new DateTimeFilter($expr);
        $filtered = array();
        foreach ($this->items as $key => $item) {
            if ($filter->filter(end($item))) {
                $filtered[] = $item;
            }
        }
        $this->items = $filtered;
        return $this;
    }

    public function size($expr) {
        $filter = new SizeFilter($expr);
        $filtered = array();
        foreach ($this->items as $key => $item) {
            if ($filter->filter(end($item))) {
                $filtered[] = $item;
            }
        }
        $this->items = $filtered;
        return $this;
    }

    public function addFilter(FinderFilter $filter) {
        $filtered = array();
        foreach ($this->items as $key => $item) {
            if ($filter->filter(end($item))) {
                $filtered[] = $item;
            }
        }
        $this->items = $filtered;
        return $this;
    }

    public function current() {
        return $this->items[$this->position];
    }

    public function next() {
        ++$this->position;
    }

    public function key() {
        return $this->position;
    }

    public function valid() {
        return isset($this->items[$this->position]);
    }

    public function rewind() {
        $this->position = 0;
    }
}