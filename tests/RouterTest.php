<?php

namespace Tests;

use App\Response;
use App\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testRouterMatchesRootPath()
    {
        // Arrange
        $callback = function () {
            return 'Hello World';
        };

        // Act
        $router = new Router();
        $router->get('/', $callback);

        // Assert
        $this->assertEquals($callback, $router->match('/'));
    }

    public function testRouterMatchesPathWithParameters()
    {
        // Arrange
        $callbackUserShow = function ($id) {
            return 'User: ' . $id;
        };
        $callbackImageShow = function ($id) {
            return 'Image: ' . $id;
        };
        $callbackUnmatched = function () {
            return 'Unmatched';
        };

        // Act
        $router = new Router();
        $router->get('/users/{id}', $callbackUserShow);
        $router->get('/image/{slug}', $callbackImageShow);
        $router->get('/unmatched/{id}', $callbackUnmatched);

        // Assert
        $this->assertEquals($callbackUserShow, $router->match('/users/1'));
        $this->assertEquals($callbackImageShow, $router->match('/image/john'));
        $this->assertEquals(null, $router->match('/unmatched/'));
    }

    public function testRouterOnlyMatchesAsciiPaths()
    {
        // Arrange
        $callback = function () {
            return 'Hello World';
        };

        // Act
        $router = new Router();
        $router->get('/sõmethiñg', $callback);

        // Assert
        $this->assertEquals(null, $router->match('/sõmethiñg'));
        $this->assertEquals(null, $router->match('/something'));
    }

    /** @test */
    public function itCanHandleAnIncomingRequest()
    {
        // Arrange
        $callback = function () {
            return new Response(200, 'I am content');
        };

        $router = new Router();
        $router->get('/', $callback);

        // Act
        $response = $router->handle('/');

        $this->assertEquals('I am content', $response->getBody());
    }
}
