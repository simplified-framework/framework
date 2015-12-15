<?php

namespace Simplified\Core;

class ViewRenderer implements ViewRendererInterface {

    public function render($template, $data = array()) {
        print "render $template";
    }
}