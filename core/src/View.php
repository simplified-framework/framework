<?php

namespace Simplified\Core;

use Simplified\Config\Config;

class View {
    private $renderer;

    public function __construct() {
        $provider = Config::get('providers', 'view', null);
        if ($provider) {
            // use reflection to detect the provider class name
            $refMethod = new \ReflectionMethod($provider, 'provides');
            $rendererClass  = $refMethod->invoke(new $provider);
            if (empty($rendererClass))
                throw new ViewException("Class {$provider} doesn't provides a valid renderer class");
            $this->renderer = new $rendererClass;
        } else {
            $refMethod = new \ReflectionMethod(__NAMESPACE__ . '\\ViewProvider', 'provides');
            $rendererClass  = $refMethod->invoke(new ViewProvider());
            $this->renderer = new $rendererClass;
        }
    }

    public function render($template, $data = array()) {
        return $this->renderer->render($template, $data);
    }
}