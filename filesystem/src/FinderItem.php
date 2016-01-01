<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 31.12.2015
 * Time: 16:13
 */

namespace Simplified\FileSystem;


interface FinderItem {
    public function id();
    public function name();
    public function size();
    public function timestamp();
}