<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 10.01.2016
 * Time: 11:38
 */

namespace Simplified\Core;

class Collection implements Arrayable, ContainerInterface,
    \ArrayAccess, \Countable, \Iterator {

    private $container = array();
    private $position;

    public function __construct(array $items = array()) {
        $this->container = $items;
        $this->rewind();
    }

    public function toArray() {
        return $this->container;
    }

    public function get($key) {
        return $this->container[$key];
    }

    public function has($key) {
        return $this->offsetExists($key);
    }

    public function add($key, $value) {
        $this->container[$key] = $value;
    }

    public function offsetExists($offset) {
        return ($offset >= 0 && $offset < $this->count());
    }

    public function offsetGet($offset) {
        if ($offset > $this->count())
            throw new \InvalidArgumentException("offset $offset is greater than collection({$this->count()})");

        if (is_numeric($offset)) {
            $keys = array_keys($this->container);
            $k = $keys[$offset];
        } else {
            $k = $offset;
        }

        return $this->container[$k];
    }

    public function offsetSet($offset, $value) {
        $this->container[$offset] = $value;
    }

    public function offsetUnset($offset) {
        if ($this->offsetExists($offset)) {
            unset($this->container[$offset]);
        }
    }

    public function count() {
        return count($this->container);
    }

    public function keys() {
        return array_keys($this->container);
    }

    public function values() {
        return array_values($this->container);
    }

    public function all() {
        return $this->container;
    }

    public function current() {
        $item = $this->valid() ?
            $this->offsetGet($this->position) : false;

        return $item;
    }

    public function next() {
        $this->position++;
    }

    public function key() {
        return key($this->container);
    }

    public function valid() {
        return $this->offsetExists($this->position);
    }

    public function rewind() {
        $this->position = 0;
    }
}