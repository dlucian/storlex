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
        $driver = DriverManager::imageStorage('file');

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

    /** @test */
    public function itRetrievesAnImageCacheDriver()
    {
        // Act
        $driver = DriverManager::imageCache('file');

        // Assert
        $this->assertInstanceOf(\App\Drivers\ImageCache\ImageCache::class, $driver);
    }

    /** @test */
    public function itRetrievesAnImageProcessorDriver()
    {
        // Act
        $driver = DriverManager::imageProcessor('gd2');

        // Assert
        $this->assertInstanceOf(\App\Drivers\ImageProcessor\ImageProcessor::class, $driver);
    }
}
