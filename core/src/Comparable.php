<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 07.01.2016
 * Time: 09:12
 */

namespace Simplified\Core;


abstract class Comparable {
    abstract public function compareTo(Comparable $other);
}
