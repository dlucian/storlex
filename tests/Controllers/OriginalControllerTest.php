<?php

namespace Tests\Controllers;

use App\Request;
use PHPUnit\Framework\TestCase;

class OriginalControllerTest extends TestCase
{
    /** @test */
    public function itUploadsAnOriginaImage()
    {
        // Arrange
        $fileName = 'balloons.jpg';
        $filePath = ROOT . '/tests/' . $fileName;
        $uniqueId = uniqid();
        $tempPath = sys_get_temp_dir() . '/' . $uniqueId . '.jpg';
        copy($filePath, $tempPath);

        $request = new Request([], [], [], [], [
            'file' => [
                'name' => $fileName,
                'type' => 'image/jpeg',
                'tmp_name' => $tempPath,
                'error' => 0,
                'size' => filesize($tempPath),
            ],
        ]);

        // Act
        $controller = new \App\Controllers\OriginalController();
        $response = $controller->upload($request);

        unlink($tempPath);
        // Assert
        $this->assertEquals(200, $response->getStatusCode());
    }
}
