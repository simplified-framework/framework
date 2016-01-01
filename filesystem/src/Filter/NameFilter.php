<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 30.12.2015
 * Time: 19:20
 */

namespace Simplified\FileSystem\Filter;

use Simplified\FileSystem\File;
use Simplified\FileSystem\FinderFilter;

class NameFilter extends FinderFilter {
    public function filter(\SplFileInfo $file) {
        if ( !is_string($this->condition()) || is_null($this->condition()) )
            throw new FilterException("Condition must be a string");

        $pattern = str_replace('*', '[a-zA-Z0-9\s\w\-\_\.]+', $this->condition());
        $pattern = str_replace('.', '\\.', $pattern);
        preg_match('/'.$pattern.'/', $file->name(), $matches);

        if (isset($matches[0]) && $matches[0] == $file->name()) {
            return true;
        }

        return false;
    }
}