<?php

/**
 * Main bootstrapping entry-point for the application.
 */

use App\Controllers\HomeController;
use App\Router;

require __DIR__ . '/../vendor/autoload.php';

define('ROOT', dirname(__DIR__ . '/..'));

if (!empty($_SERVER['REQUEST_URI'])) {
    $router = (new Router());
    $router->get('/', function() {
        return (new HomeController())->index();
    });
    echo $router->handle($_SERVER['REQUEST_URI']);
}
