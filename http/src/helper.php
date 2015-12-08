<?php

namespace App\Controllers;

use Simplified\Http\Request;

class Controller {
    public function newIndex(Request $req, $arg1, $arg2) {
        print route("meine.");
    }
}