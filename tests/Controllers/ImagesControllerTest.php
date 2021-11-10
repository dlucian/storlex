<?php

namespace Tests\Controllers;

use App\Drivers\DriverManager;
use App\Request;
use PHPUnit\Framework\TestCase;

class ImageControllerTest extends TestCase
{
    /** @test */
    public function itRetrievesAnImage()
    {
        // Arrange
        $fileName = 'manki-kim-LLWS6gBToQ4-unsplash.jpg';
        $fs = DriverManager::imageStorage();
        $fs->save([
            'name' => 'manki-kim-LLWS6gBToQ4-unsplash.jpg',
            'file' => ROOT . '/tests/' . $fileName,
            'size' => filesize(ROOT . '/tests/' . $fileName),
            'type' => 'image/jpeg',
        ]);
        $request = new Request([], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve($fileName . '-300x200.jpg', $request);

        // Assert
        $this->assertNotEmpty($response->getBody());
        $this->assertNotEmpty(getimagesizefromstring($response->getBody()));
    }

    /** @test */
    public function itReturns400BadRequestIfTheImageSyntaxIsInvalid()
    {
        // Arrange
        $fileName = 'manki-kim-LLWS6gBToQ4-unsplash.jpg';
        $fs = DriverManager::imageStorage();
        $fs->save([
            'name' => 'manki-kim-LLWS6gBToQ4-unsplash.jpg',
            'file' => ROOT . '/tests/' . $fileName,
            'size' => filesize(ROOT . '/tests/' . $fileName),
            'type' => 'image/jpeg',
        ]);
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
