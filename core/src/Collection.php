<?php

namespace Simplified\Core;


class Collection implements \Countable, \ArrayAccess, \Iterator, Arrayable {

    public function __construct($items = array()) {
        $this->items = array();
        $this->pointer = 0;
        if ($items != null && is_array($items)) {
            foreach ($items as $item)
                $this->add($item);
        }
    }

    public function next() {
        $this->pointer++;
    }

    public function current() {
        return $this->items[$this -> pointer];
    }

    public function rewind() {
        $this->pointer = 0;
    }

    public function valid() {
        return isset($this->items[$this -> pointer]);
    }

    public function key() {
        return $this->pointer;
    }

    public function add($arg1, $arg2) {
        if (func_num_args() == 1) {
            $key  = $this->count();
            $arg2 = $arg1;
        }
        else
            $key = $arg1;

        $this->items[$key] = $arg2;
    }

    public function count() {
        return count ($this->items);
    }

    public function toArray() {
        return is_array($this->items) ? $this->items : array();
    }

    public function offsetExists($offset) {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    public function offsetSet($offset ,$value) {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->items[$offset]);
    }
}