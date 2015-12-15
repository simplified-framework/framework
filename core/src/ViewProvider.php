<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 15.12.2015
 * Time: 13:50
 */

namespace Simplified\Core;


class ViewProvider implements Provider {

    public function boot() {
    }

    public function provides() {
        return 'Simplified\\Core\\ViewRenderer';
    }
}