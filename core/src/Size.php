<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 07.01.2016
 * Time: 13:53
 */

namespace Simplified\Core;


class Size extends Comparable {
    private $w;
    private $h;

    public function __construct($width, $height) {
        $this->setWidth($width);
        $this->setHeight($height);
    }

    public function width() {
        return $this->w;
    }

    public function height() {
        return $this->h;
    }

    public function setWidth($width) {
        if (!is_numeric($width))
            throw new IllegalArgumentException('Invalid width size');

        $this->w = $width;
    }

    public function setHeight($height) {
        if (!is_numeric($height))
            throw new IllegalArgumentException('Invalid height size');
        $this->h = $height;
    }

    public function compareTo(Comparable $other) {
        if ($this->w == $other->w && $this->h == $other->h)
            return true;

        return false;
    }
}
