<?php

namespace Tests\Controllers;

use App\Database;
use App\Drivers\DriverManager;
use App\Request;
use App\Token;
use PHPUnit\Framework\TestCase;

class ImagesControllerTest extends TestCase
{
    public function setUp(): void
    {
        Database::execute('CREATE TABLE IF NOT EXISTS tokens (token VARCHAR(1024) NOT NULL PRIMARY KEY)');
        Database::execute('DELETE FROM tokens');
        (new Token('imageTOKEN'))->grant();

        DriverManager::imageCache()->clear();

        $_ENV['ALLOWED_SIZE'] = ["300x200", "300x150", "1024x800", "400x400", "800x800"];
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
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer imageTOKEN'], [], [], [], []);

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
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer imageTOKEN'], [], [], [], []);

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
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer imageTOKEN'], [], [], [], []);

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
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer imageTOKEN'], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve($fileName . '-300x200.jpg', $request);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function itReturns401UnauthorizedIfAuthenticationHeaderIsMissing()
    {
        // Arrange
        $fileName = 'some-inexisting-photo.jpg';
        $request = new Request([], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve($fileName . '-300x200.jpg', $request);

        // Assert
        $this->assertEquals(401, $response->getStatusCode());
    }

    /** @test */
    public function itReturns401UnauthorizedIfAuthenticationHeaderIsWrong()
    {
        // Arrange
        $fileName = 'some-inexisting-photo.jpg';
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer somethingELSE'], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve($fileName . '-300x200.jpg', $request);

        // Assert
        $this->assertEquals(401, $response->getStatusCode());
    }

    /** @test */
    public function itReturns401UnauthorizedIfAuthenticationHeaderIsInvalid()
    {
        // Arrange
        $fileName = 'some-inexisting-photo.jpg';
        $request = new Request(['HTTP_AUTHORIZATION' => 'Com/pletely$wrong 1'], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve($fileName . '-300x200.jpg', $request);

        // Assert
        $this->assertEquals(401, $response->getStatusCode());
    }

    /** @test */
    public function itDoesNotAllowForwardSlashesInImageNames()
    {
        // Arrange
        $fileName = 'some-inexisting/-image.jpg';
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer imageTOKEN'], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve($fileName . '-300x200.jpg', $request);

        // Assert
        $this->assertEquals(400, $response->getStatusCode());
    }

    /** @test */
    public function itDoesNotAllowBackSlashesInImageNames()
    {
        // Arrange
        $fileName = 'some-inexisting\-image.jpg';
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer imageTOKEN'], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve($fileName . '-300x200.jpg', $request);

        // Assert
        $this->assertEquals(400, $response->getStatusCode());
    }

    /** @test */
    public function itReturns404IfRequestedSiteNotAllowed()
    {
        // Arrange
        $fileName = 'balloons.jpg';
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer imageTOKEN'], [], [], [], []);

        // Act
        $controller = new \App\Controllers\ImagesController();
        $response = $controller->retrieve('balloons.jpg-310x250.jpg', $request);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
    }
}
