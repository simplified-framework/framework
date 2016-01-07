<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 07.01.2016
 * Time: 13:53
 */

namespace Simplified\Core;


class Point extends Comparable {
    private $x;
    private $y;

    public function __construct($x, $y) {
        $this->setX($x);
        $this->setY($y);
    }

    public function x() {
        return $this->x;
    }

    public function y() {
        return $this->y;
    }

    public function setX($x) {
        if (!is_numeric($x))
            throw new IllegalArgumentException('Invalid x coordinate');

        $this->x = $x;
    }

    public function setY($y) {
        if (!is_numeric($y))
            throw new IllegalArgumentException('Invalid y coordinate');
        $this->y = $y;
    }

    public function compareTo(Comparable $other) {
        if ($this->x == $other->x && $this->y == $other->y)
            return true;

        return false;
    }
}
