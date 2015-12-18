<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 18.12.2015
 * Time: 19:26
 */

namespace Simplified\Session;


use Simplified\Core\Provider;

class SessionProvider implements Provider{
    public function provides() {
        return '\\Simplified\\Session\\SessionHandler';
    }

    public function boot() {}
}