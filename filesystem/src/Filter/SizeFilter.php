<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 30.12.2015
 * Time: 19:35
 */

namespace Simplified\FileSystem\Filter;

use Simplified\FileSystem\File;
use Simplified\FileSystem\FinderFilter;

class SizeFilter extends FinderFilter {
    public function filter(\SplFileInfo $file) {
        if ( !is_string($this->condition()) || is_null($this->condition()) )
            throw new FilterException("Condition must be a string");

        $size = $file->getSize();

        $patterns   = array();
        $patterns[] = '(\<\=)[\s]+?([\d]+)'; // <=
        $patterns[] = '(\>\=)[\s]+?([\d]+)'; // >=
        $patterns[] = '(\<)[\s]+?([\d]+)';   // <
        $patterns[] = '(\>)[\s]+?([\d]+)';   // >
        $patterns[] = '(\=)[\s]+?([\d]+)';   // =

        foreach ($patterns as $pattern) {
            preg_match_all('/'.$pattern.'/s', $this->condition(), $matches2);
            if (!empty($matches2[0])) {
                $op  = $matches2[1][0];
                $val = $matches2[2][0];
                switch ($op) {
                    case "<=":
                        return intval($size) <= intval($val);
                        break;

                    case ">=":
                        return intval($size) >= intval($val);
                        break;

                    case "<":
                        return intval($size) < intval($val);
                        break;

                    case ">":
                        return intval($size) > intval($val);
                        break;

                    case "=":
                        return intval($size) == intval($val);
                        break;
                }
            }
        }

        throw new FilterException("Invalid condition '". $this->condition() . "'");

        return false;
    }
}