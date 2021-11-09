<?php

namespace Tests;

use App\Response;
use App\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    /** @test */
    public function itMatchesRootPath()
    {
        // Arrange
        $callback = function () {
            return 'Hello World';
        };

        // Act
        $router = new Router();
        $router->get('/', $callback);

        // Assert
        $this->assertEquals($callback(), $router->match('/')());
    }

    /** @test */
    public function itMatchesPathWithParameters()
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
        $this->assertEquals($callbackUserShow(1), $router->match('/users/1')(1));
        $this->assertEquals($callbackImageShow(2), $router->match('/image/john')(2));
        $this->assertEquals(null, $router->match('/unmatched/'));
    }

    /** @test */
    public function itMatchesPostAndGetRoutes()
    {
        // Arrange
        $callbackPost = function () {
            return 'Hello World';
        };

        $callbackGet = function () {
            return 'Hi there';
        };

        // Act
        $router = new Router();
        $router->get('/something', $callbackGet);
        $router->post('/something', $callbackPost);

        // Assert
        $this->assertEquals($callbackGet(), $router->match('/something', 'GET')());
        $this->assertEquals($callbackPost(), $router->match('/something', 'POST')());
    }

    /** @test */
    public function itHandlesUrlsContainingQueryStringsAndHash()
    {
        // Arrange
        $callback = function () {
            return new Response(200, 'I am the response');
        };
        $router = new Router();
        $router->get('/thisone', $callback);

        // Act
        $response = $router->handle('GET', '/thisone?query=string#hash');

        // Assert
        $this->assertNotNull($response);
        $this->assertEquals($callback()->getStatusCode(), $response->getStatusCode());
        $this->assertEquals($callback()->getBody(), $response->getBody());
    }

    /** @test */
    public function itHandlesAMalformedUrlByRespondingBadRequest()
    {
        // Arrange
        $router = new Router();

        // Act
        $response = $router->handle('GET', '//////////////////');

        // Assert
        $this->assertNotNull($response);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /** @test */
    public function itOnlyHandlesAsciiUrls()
    {
        // Arrange
        $callback = function () {
            return new Response(200, 'I am content');
        };

        // Act
        $router = new Router();
        $router->get('/sõmethiñg', $callback);

        // Assert
        $this->assertEquals(404, $router->handle('GET', '/sõmethiñg')->getStatusCode());
        $this->assertEquals(404, $router->handle('GET', '/something')->getStatusCode());
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
        $response = $router->handle('GET', '/');

        // Assert
        $this->assertEquals('I am content', $response->getBody());
    }

    /** @test */
    public function itReturns404IfAskedForAnUndefinedMethod()
    {
        // Arrange
        $router = new Router();
        $callback = function () {
            return new Response(200, 'I am content');
        };
        $router->get('/', $callback);

        // Act
        $response = $router->handle('POST', '/');

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
    }
}
