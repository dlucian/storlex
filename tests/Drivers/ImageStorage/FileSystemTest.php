<?php

namespace Tests\Drivers\ImageStorage;

use App\Drivers\ImageStorage\FileSystem;
use PHPUnit\Framework\TestCase;

/**
 * FileSystemTest
 * @group group
 */
class FileSystemTest extends TestCase
{
    /** @test */
    public function itGeneratesAnImagePath()
    {
        // Arrange
        $fs = new FileSystem();

        // Act
        $path = $fs->getStoragePath('test.jpg');

        // Assert
        $this->assertEquals(ROOT . '/storage/original/041/test.jpg', $path);
    }
}
