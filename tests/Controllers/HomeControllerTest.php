<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    public function testHomepageReturnsAppName()
    {
        // Arrange
        $controller = new \App\Controllers\HomeController();

        // Act
        $response = $controller->index();

        // Assert
        $this->assertEquals('Storlex/API', $response);
    }
}
