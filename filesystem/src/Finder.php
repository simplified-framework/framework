<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 30.12.2015
 * Time: 16:32
 */

namespace Simplified\FileSystem;

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
        return $this->filter(
            new NameFilter($expr)
        );
    }

    public function date($expr) {
        return $this->filter(
            new DateTimeFilter($expr)
        );
    }

    public function size($expr) {
        return $this->filter(
            new SizeFilter($expr)
        );
    }

    public function filter(FinderFilter $filter) {
        $filtered = array();
        foreach ($this->items as $key => $item) {
            if ($filter->filter(end($item))) {
                $filtered[] = $item;
            }
        }
        $this->items = $filtered;
        return $this;
    }

    public function sort($mode) {
        if ($mode instanceof \Closure) {
            usort($this->items, $mode);
            return $this;
        }

        switch ($mode) {
            case Finder::SORT_TYPE:
            case 'type':
                return $this->sortByType();

            case Finder::SORT_DATE:
            case 'date':
                return $this->sortByDate();

            case Finder::SORT_NAME:
            case 'name':
                return $this->sortByName();

            case Finder::SORT_SIZE:
            case 'size':
                return $this->sortBySize();

            default:
                throw new FinderException(sprintf("Invalid sort argument %s", $mode));
        }
    }

    public function sortByName() {
        usort($this->items, function($a, $b) {
            $a = end($a)->name();
            $b = end($b)->name();
            return strcmp($a, $b);
        });
        return $this;
    }

    public function sortBySize() {
        usort($this->items, function($a, $b) {
            $a = end($a)->size();
            $b = end($b)->size();
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });
        return $this;
    }

    public function sortByDate() {
        usort($this->items, function($a, $b) {
            $a = end($a)->timestamp();
            $b = end($b)->timestamp();
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });
        return $this;
    }

    public function sortByType() {
        usort($this->items, function($a, $b) {
            $a = end($a)->isDir() ? 'dir' : 'file';
            $b = end($b)->isDir() ? 'dir' : 'file';
            return strcmp($a, $b);
        });
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
}