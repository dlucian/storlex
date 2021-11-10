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

    /** @test */
    public function itSavesAnImage()
    {
        // Arrange
        $fileName = 'balloons.jpg';
        $filePath = ROOT . '/tests/' . $fileName;
        $fs = new FileSystem();

        // Act
        unlink($fs->getStoragePath($fileName));
        $fs->save([
            'name' => $fileName,
            'type' => 'image/jpeg',
            'file' => $filePath,
            'size' => filesize($filePath),
        ]);

        // Assert
        $this->assertTrue(file_exists($fs->getStoragePath($fileName)));
        // Slow, but maybe useful
        // $this->assertNotEmpty(imagecreatefromjpeg($fs->getStoragePath($fileName)));
    }

    /** @test */
    public function itRetrievesAndImageFromStorage()
    {
        // Arrange
        $fileName = 'balloons.jpg';
        $filePath = ROOT . '/tests/' . $fileName;
        $fs = new FileSystem();
        unlink($fs->getStoragePath($fileName));
        $fs->save([
            'name' => $fileName,
            'type' => 'image/jpeg',
            'file' => $filePath,
            'size' => filesize($filePath),
        ]);

        // Act
        $image = $fs->get($fileName);

        // Assert
        $this->assertNotEmpty($image);
        $this->assertNotEmpty(getimagesizefromstring($image));
    }
}
