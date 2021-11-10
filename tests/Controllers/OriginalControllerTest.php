<?php

namespace Tests\Controllers;

use App\Drivers\ImageStorage\FileSystem;
use App\Request;
use PHPUnit\Framework\TestCase;

class OriginalControllerTest extends TestCase
{
    public function setUp(): void
    {
        $_ENV['ADMIN_TOKEN'] = ['testTOKEN'];
    }

    /** @test */
    public function itUploadsAnOriginaImage()
    {
        // Arrange
        $fileName = 'balloons.jpg';
        $filePath = ROOT . '/tests/' . $fileName;
        $uniqueId = uniqid();
        $tempPath = sys_get_temp_dir() . '/' . $uniqueId . '.jpg';
        copy($filePath, $tempPath);

        $request = new Request(
            ['HTTP_AUTHORIZATION' => 'Bearer testTOKEN'],
            [],
            [],
            [],
            [
            'file' => [
                'name' => $fileName,
                'type' => 'image/jpeg',
                'tmp_name' => $tempPath,
                'error' => 0,
                'size' => filesize($tempPath),
            ],
            ]
        );

        // Act
        $controller = new \App\Controllers\OriginalController();
        $response = $controller->upload($request);

        unlink($tempPath);
        // Assert
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function itDeniesUploadingAnOriginaImageWithoutAdminToken()
    {
        // Arrange
        $fileName = 'balloons.jpg';
        $filePath = ROOT . '/tests/' . $fileName;

        $request = new Request([], [], [], [], [
            'file' => [
                'name' => $fileName,
                'type' => 'image/jpeg',
                'tmp_name' => $filePath,
                'error' => 0,
                'size' => filesize($filePath),
            ],
        ]);

        // Act
        $controller = new \App\Controllers\OriginalController();
        $response = $controller->upload($request);

        // Assert
        $this->assertEquals(401, $response->getStatusCode());
    }

    /** @test */
    public function itDeletesAnOriginalImage()
    {
        // Arrange
        $fileName = 'balloons.jpg';
        $filePath = ROOT . '/tests/' . $fileName;
        $fs = new FileSystem();
        if (file_exists($fs->getStoragePath($fileName))) {
            unlink($fs->getStoragePath($fileName));
        }
        $fs->save([
            'name' => $fileName,
            'type' => 'image/jpeg',
            'file' => $filePath,
            'size' => filesize($filePath),
        ]);
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer testTOKEN'], [], ['filename' => $fileName], []);

        // Act
        $controller = new \App\Controllers\OriginalController();
        $response = $controller->delete($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNull($fs->get($fileName));
    }
}
