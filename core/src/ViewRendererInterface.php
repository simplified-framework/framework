<?php
namespace Simplified\Core;


interface ViewRendererInterface {
    public function render($template, $data = array());
}