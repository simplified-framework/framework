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
    const SORT_NAME = 100;
    const SORT_DATE = 200;
    const SORT_SIZE = 300;
    const SORT_TYPE = 400;

    private $position;
    private $container;
    private $content;
    private $type;
    private $filters;
    private $items;

    public function __construct() {
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

        $items = $this->container->files();
        foreach ($items as $item) {
            $this->items[] = array($item->id() => $item);
        }
        return $this;
    }

    public function directories() {
        if (!$this->container)
            throw new FinderException("No container was set");

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

    public function sortBy($mode) {
        if ($mode instanceof \Closure) {
            usort($this->items, $mode);
            return $this;
        }

        switch ($mode) {
            case Finder::SORT_TYPE:
                usort($this->items, array($this, 'sortByType'));
                break;
            case Finder::SORT_DATE:
                usort($this->items, array($this, 'sortByDate'));
                break;
            case Finder::SORT_NAME:
                usort($this->items, array($this, 'sortByName'));
                break;
            case Finder::SORT_SIZE:
                usort($this->items, array($this, 'sortBySize'));
                break;
            default:
                throw new FinderException("Invalid sort argument");
        }

        return $this;
    }

    public function current() {
        return end($this->items[$this->position]);
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

    public function __call($name, $arguments) {
        if (strtolower($name) == "sortbyname") {
            $a = end($arguments[0]);
            $b = end($arguments[1]);
            return strcmp($a->name(), $b->name());
        }

        if (strtolower($name) == "sortbysize") {
            $a = end($arguments[0])->size();
            $b = end($arguments[1])->size();
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        }

        if (strtolower($name) == "sortbydate") {
            $a = end($arguments[0])->timestamp();
            $b = end($arguments[1])->timestamp();
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        }
    }
}