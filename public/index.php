<?php

/**
 * Main bootstrapping entry-point for the application.
 */

use App\Config;
use App\Controllers\HomeController;
use App\Controllers\TokensController;
use App\Request;
use App\Response;
use App\Router;

require __DIR__ . '/../vendor/autoload.php';

define('ROOT', realpath(__DIR__ . '/..'));

(new Config())->load($_ENV);

// Only web-bound requests should be handled by the router.
if (!empty($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_METHOD'])) {
    $request = new Request();
    $router = new Router();
    $router->get('/', function() {
        return (new HomeController())->index();
    });
    $router->post('/token', function() use ($request) {
        return (new TokensController())->grant($request);
    });
    Response::render(
        $router->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'])
    );
}
