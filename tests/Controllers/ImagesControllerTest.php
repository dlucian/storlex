<?php

namespace Tests\Controllers;

use App\Drivers\DriverManager;
use App\Request;
use PHPUnit\Framework\TestCase;

class ImagesControllerTest extends TestCase
{
    public function setUp(): void
    {
        DriverManager::imageCache()->clear();
    }

    /** @test */
    public function itRetrievesAResizedWebpImageEndToEnd()
    {
        // Arrange
        $fileName = 'balloons.jpg';
        $fs = DriverManager::imageStorage();
        $fs->save([
            'name' => 'balloons.jpg',
            'file' => ROOT . '/tests/' . $fileName,
            'size' => filesize(ROOT . '/tests/' . $fileName),
            'type' => 'image/jpeg',
        ]);
        $request = new Request([], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve(
            $fileName . '-300x200.webp',
            $request,
            DriverManager::imageStorage() // for non-E2E, mock this
        );

        // Assert
        $this->assertNotEmpty($response->getBody());
        $size = getimagesizefromstring($response->getBody());
        $this->assertNotEmpty($size);
        $this->assertEquals(300, $size[0]);
        $this->assertEquals(200, $size[1]);
        $this->assertEquals('image/webp', $size['mime']);
    }

    /** @test */
    public function itRetrievesAResizedJpegImageEndToEnd()
    {
        // Arrange
        $fileName = 'balloons.jpg';
        $fs = DriverManager::imageStorage();
        $fs->save([
            'name' => 'balloons.jpg',
            'file' => ROOT . '/tests/' . $fileName,
            'size' => filesize(ROOT . '/tests/' . $fileName),
            'type' => 'image/jpeg',
        ]);
        $request = new Request([], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve(
            $fileName . '-300x200.jpg',
            $request,
            DriverManager::imageStorage() // for non-E2E, mock this
        );

        // Assert
        $this->assertNotEmpty($response->getBody());
        $size = getimagesizefromstring($response->getBody());
        $this->assertNotEmpty($size);
        $this->assertEquals(300, $size[0]);
        $this->assertEquals(200, $size[1]);
        $this->assertEquals('image/jpeg', $size['mime']);
    }

    /** @test */
    public function itReturns400BadRequestIfTheImageSyntaxIsInvalid()
    {
        // Arrange
        $fileName = 'balloons.jpg';
        $request = new Request([], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve($fileName . 'zzz', $request);

        // Assert
        $this->assertEquals(400, $response->getStatusCode());
    }

    /** @test */
    public function itReturns404NotFoundIfTheOriginalDoesntExist()
    {
        // Arrange
        $fileName = 'some-inexisting-image.jpg';
        $request = new Request([], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve($fileName . '-300x200.jpg', $request);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
    }
}
