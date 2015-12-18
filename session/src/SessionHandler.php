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
        // set session save path to app/storage/session
        // TODO load it from session config
        $this->sessionpath = STORAGE_PATH . "session";
        session_save_path($this->sessionpath);

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
        return is_writable($session_path);
    }

    public function close() {
        return true;
    }

    public function gc($max_life_time) {
        // TODO implement garbage cleanup
        return true;
    }

    public function destroy($session_id) {
        if (file_exists($this->sessionpath . DIRECTORY_SEPARATOR . $session_id))
            @unlink($this->sessionpath . DIRECTORY_SEPARATOR . $session_id);
        return true;
    }
}