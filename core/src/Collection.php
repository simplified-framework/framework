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
        $this->offsetGet($key);
    }

    public function has($key) {
        return $this->offsetExists($key);
    }

    public function add($key, $value) {
        $this->offsetSet($key, $value);
    }

    public function offsetExists($offset) {
        $keys = $this->keys();
        return isset($keys[$offset]);
    }

    public function offsetGet($offset) {
        if ($this->offsetExists($offset)) {
            $key = $this->keys()[$offset];
            return $this->container[$key];
        } else {
            return false;
        }
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
        return $this->offsetGet($this->position);
    }

    public function next() {
        $this->position++;
    }

    public function key() {
        return $this->position;
    }

    public function valid() {
        return $this->offsetExists($this->position);
    }

    public function rewind() {
        $this->position = 0;
    }
}