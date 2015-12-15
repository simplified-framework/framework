<?php

namespace Simplified\Core;

use Simplified\Config\PHPFileLoader;

class View {
    private $renderer;

    public function __construct() {
        $loader = new PHPFileLoader();
        $providers = $loader->load(CONFIG_PATH . 'providers.php', array());
        if (is_array($providers) && isset($providers['view'])) {
            // use reflection to detect the provider class name
            $refMethod = new \ReflectionMethod($providers['view'], 'provides');
            $rendererClass  = $refMethod->invoke(new $providers['view']);
            if (empty($rendererClass))
                throw new ViewException("Class {$providers['view']} doesn't provides a valid renderer class");
            $this->renderer = new $rendererClass;
        } else {
            $refMethod = new \ReflectionMethod(__NAMESPACE__ . '\\ViewProvider', 'provides');
            $rendererClass  = $refMethod->invoke(new ViewProvider());
            $this->renderer = new $rendererClass;
        }
    }

    public function render($template, $data = array()) {
        $this->renderer->render($template, $data);
    }
}