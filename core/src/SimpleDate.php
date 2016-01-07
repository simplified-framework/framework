<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 07.01.2016
 * Time: 09:20
 */

namespace Simplified\Core;


class SimpleDate extends Comparable {
    private $value;

    public function __construct($date) {
        $this->setDate($date);
    }

    public function setDate($date) {
        $value = 0;
        if (is_string($date))
            $value = date("Y-m-d", strtotime($date));
        if (is_int($date))
            $value = date("Y-m-d", $date);
        $this->value = strtotime($value);
    }

    public function getYear() {
        return date("Y", $this->value);
    }

    public function getMonth() {
        return date("m", $this->value);
    }

    public function getDay() {
        return date("d", $this->value);
    }

    public function format($pattern) {
        return date($pattern, $this->value);
    }

    public function getTimestamp() {
        return $this->value;
    }

    public function compareTo(Comparable $other) {
        if (!$other instanceof SimpleDate)
            throw new IllegalArgumentException('Comparable must be a SimpleDateTime object');

        if ($this->value == $other->value)
            return 0;

        if ($this->value < $other->value)
            return -1;

        if ($this->value > $other->value)
            return 1;
    }
}
