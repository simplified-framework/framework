<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 18.12.2015
 * Time: 19:31
 */

namespace Simplified\Session;


class SessionHandler implements \SessionHandlerInterface {
    public function read($session_id) {

    }

    public function write($session_id, $data) {

    }

    public function open($session_path, $session_id) {

    }

    public function close() {
        return true;
    }

    public function gc($max_life_time) {

    }

    public function destroy($session_id) {

    }
}