<?php

namespace Tests\Controllers;

use App\Drivers\DriverManager;
use App\Request;
use PHPUnit\Framework\TestCase;

class ImageControllerTest extends TestCase
{
    /** @test */
    public function itUploadsAnOriginaImage()
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
}
