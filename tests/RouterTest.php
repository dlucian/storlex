<?php

namespace Tests;

use App\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testRouterServesHomeController()
    {
        $router = new Router();
        $router->get('/', 'HomeController@index');
        $this->assertEquals('HomeController@index', $router->match('/'));
    }
}
