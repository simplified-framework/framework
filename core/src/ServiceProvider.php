<?php

namespace Simplified\Core;

interface ServiceProvider {
    public function provides();
    public function register();
}