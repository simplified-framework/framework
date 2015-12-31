<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 30.12.2015
 * Time: 19:18
 */

namespace Simplified\FileSystem;


abstract class FinderFilter {
    private $condition;
    public function __construct($condition) {
        $this->condition = $condition;
    }

    public function condition() {
        return $this->condition;
    }

    abstract public function filter(\SplFileInfo $info);
}