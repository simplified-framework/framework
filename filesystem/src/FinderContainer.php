<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 30.12.2015
 * Time: 17:34
 */

namespace Simplified\FileSystem;

use Simplified\Core\ContainerInterface;

interface FinderContainer extends ContainerInterface {
    public function files();
    public function directories();
}