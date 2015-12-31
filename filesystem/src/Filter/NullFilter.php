<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 30.12.2015
 * Time: 19:35
 */

namespace Simplified\FileSystem\Filter;

use Simplified\FileSystem\FinderFilter;

class NullFilter extends FinderFilter {
    public function __construct() {
        parent::__construct(null);
    }

    public function filter(\SplFileInfo $info) {
        return false;
    }
}