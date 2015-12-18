<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 18.12.2015
 * Time: 19:31
 */

namespace Simplified\Session;


class SessionHandler implements \SessionHandlerInterface {
    private $sessionpath;
    public function __construct() {
        $this->sessionpath = session_save_path();
        if (!file_exists($this->sessionpath)) {
            mkdir($this->sessionpath, 0775, true);
        }
    }

    public function read($session_id) {
        if (file_exists($this->sessionpath . DIRECTORY_SEPARATOR . $session_id))
            return file_get_contents($this->sessionpath . DIRECTORY_SEPARATOR . $session_id);

        return "";
    }

    public function write($session_id, $data) {
        $file = $this->sessionpath . DIRECTORY_SEPARATOR . $session_id;
        $fp = fopen($file, "w");
        fwrite($fp, $data);
        fclose($fp);

        return true;
    }

    public function open($session_path, $session_id) {
        return true;
    }

    public function close() {
        return true;
    }

    public function gc($max_life_time) {
        return true;
    }

    public function destroy($session_id) {
        if (file_exists($this->sessionpath . DIRECTORY_SEPARATOR . $session_id))
            @unlink($this->sessionpath . DIRECTORY_SEPARATOR . $session_id);
        return true;
    }
}