<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 15.12.2015
 * Time: 13:42
 */

namespace Simplified\Core;


interface Provider {

    // class name to instantiate
    public function provides();

    // This function is called after class instantiation
    public function boot();
}