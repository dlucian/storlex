<?php

namespace Tests;

use App\Drivers\DriverManager;
use PHPUnit\Framework\TestCase;

/**
 * DriverManagerTest
 */
class DriverManagerTest extends TestCase
{
    /** @test */
    public function itRetrievesAnImageStorageDriver()
    {
        // Act
        $driver = DriverManager::imageStorage('filesystem');

        // Assert
        $this->assertInstanceOf(\App\Drivers\ImageStorage\ImageStorage::class, $driver);
    }

    /** @test */
    public function itThrowsExceptionIfInvalidImageStorageDriverRequested()
    {
        // Arrange
        $this->expectException(\App\Exceptions\InvalidDriverException::class);

        // Act
        DriverManager::imageStorage('foobar');
    }
}
