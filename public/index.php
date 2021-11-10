<?php

/**
 * Main bootstrapping entry-point for the application.
 */

use App\Config;
use App\Request;
use App\Response;
use App\Router;

require __DIR__ . '/../vendor/autoload.php';

define('ROOT', realpath(__DIR__ . '/..'));

(new Config())->load($_ENV);

if (strpos(phpversion(), '7.4.') === false) {
    echo 'PHP 7.4 is required, running PHP ' . phpversion() . '.';
    exit(1);
}

// Only web-bound requests should be handled by the router.
if (!empty($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_METHOD'])) {
    $request = new Request();
    $router = new Router();
    $router->get('/', function() {
        return (new \App\Controllers\HomeController())->index();
    });
    $router->post('/token', function() use ($request) {
        return (new \App\Controllers\TokensController())->grant($request);
    });
    $router->post('/original', function() use ($request) {
        return (new \App\Controllers\OriginalController())->upload($request);
    });
    $router->delete('/original', function() use ($request) {
        return (new \App\Controllers\OriginalController())->delete($request);
    });
    $router->get('/img/{image}', function($params) use ($request) {
        return (new \App\Controllers\ImagesController())->retrieve($params['image'], $request);
    });
    Response::render(
        $router->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'])
    );
}
