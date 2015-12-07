<?php

namespace Simplified\Config;

class PHPFileLoader {
    public function load($filename, $default = null) {
        if (file_exists($filename)) {
            $data = include $filename;
            if (!is_array($data))
            	return $default;
            
            return $data;
            
        }
        
        return $default;
    }
}

