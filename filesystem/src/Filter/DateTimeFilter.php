<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 30.12.2015
 * Time: 19:35
 */

namespace Simplified\FileSystem\Filter;

use Carbon\Carbon;
use Simplified\FileSystem\FinderFilter;

class DateTimeFilter extends FinderFilter {
    public function filter(\SplFileInfo $file) {
        if ( !is_string($this->condition()) || is_null($this->condition()))
            throw new FilterException("Condition must be a string");

        $filetime = $file->getMTime();

        $patterns   = array();
        $patterns[] = '(\<\=)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2} [\d]{2,2}:[\d]{2,2}:[\d]{2,2})'; // <= YYYY-mm-dd H:i:s
        $patterns[] = '(\<\=)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2} [\d]{2,2}:[\d]{2,2})'; // <= YYYY-mm-dd H:i
        $patterns[] = '(\<\=)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2})'; // <= YYYY-mm-dd
        $patterns[] = '(\<\=)[\s]+?([\d]{4,4}-[\d]{2,2})'; // <= YYYY-mm
        $patterns[] = '(\<\=)[\s]+?([\d]{4,4})'; // <= YYYY

        $patterns[] = '(\>\=)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2} [\d]{2,2}:[\d]{2,2}:[\d]{2,2})'; // >= YYYY-mm-dd H:i:s
        $patterns[] = '(\>\=)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2} [\d]{2,2}:[\d]{2,2})'; // >= YYYY-mm-dd H:i
        $patterns[] = '(\>\=)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2})'; // >= YYYY-mm-dd
        $patterns[] = '(\>\=)[\s]+?([\d]{4,4}-[\d]{2,2})'; // >= YYYY-mm
        $patterns[] = '(\>\=)[\s]+?([\d]{4,4})'; // >= YYYY

        $patterns[] = '(\<)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2} [\d]{2,2}:[\d]{2,2}:[\d]{2,2})'; // < YYYY-mm-dd H:i:s
        $patterns[] = '(\<)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2} [\d]{2,2}:[\d]{2,2})'; // < YYYY-mm-dd H:i
        $patterns[] = '(\<)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2})'; // < YYYY-mm-dd
        $patterns[] = '(\<)[\s]+?([\d]{4,4}-[\d]{2,2})'; // < YYYY-mm
        $patterns[] = '(\<)[\s]+?([\d]{4,4})'; // < YYYY

        $patterns[] = '(\>)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2} [\d]{2,2}:[\d]{2,2}:[\d]{2,2})'; // > YYYY-mm-dd H:i:s
        $patterns[] = '(\>)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2} [\d]{2,2}:[\d]{2,2})'; // > YYYY-mm-dd H:i
        $patterns[] = '(\>)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2})'; // > YYYY-mm-dd
        $patterns[] = '(\>)[\s]+?([\d]{4,4}-[\d]{2,2})'; // > YYYY-mm
        $patterns[] = '(\>)[\s]+?([\d]{4,4})'; //> YYYY

        $patterns[] = '(\=)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2} [\d]{2,2}:[\d]{2,2}:[\d]{2,2})'; // = YYYY-mm-dd H:i:s
        $patterns[] = '(\=)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2} [\d]{2,2}:[\d]{2,2})'; //= YYYY-mm-dd H:i
        $patterns[] = '(\=)[\s]+?([\d]{4,4}-[\d]{2,2}-[\d]{2,2})'; // = YYYY-mm-dd
        $patterns[] = '(\=)[\s]+?([\d]{4,4}-[\d]{2,2})'; // = YYYY-mm
        $patterns[] = '(\=)[\s]+?([\d]{4,4})'; // = YYYY

        foreach ($patterns as $pattern) {
            preg_match_all('/'.$pattern.'/s', $this->condition(), $matches2);
            if (!empty($matches2[0])) {
                $op  = $matches2[1][0];
                $val = strtotime($matches2[2][0]);

                // add month, day and time
                if (preg_match('/[\d]{4,4}/', $matches2[2][0], $m)) {
                    if (isset($m[0]) && $m[0] == $matches2[2][0]) {
                        $date = $matches2[2][0] . "-01-01 00:00:00";
                        $val  = strtotime($date);
                    }
                }

                switch ($op) {
                    case "<=":
                        return (intval($filetime) <= intval($val));
                        break;

                    case ">=":
                        return (intval($filetime) >= intval($val));
                        break;

                    case "<":
                        return (intval($filetime) < intval($val));
                        break;

                    case ">":
                        return (intval($filetime) > intval($val));
                        break;

                    case "=":
                        return (intval($filetime) == intval($val));
                        break;
                }
            }
        }

        return false;
    }
}