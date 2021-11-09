<?php

namespace Tests;

use App\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /** @test */
    public function itInitializesWithTheRequestGlobals()
    {
        // Arrange
        $server = ['server' => 'foo'];
        $get = ['get' => 'bar'];
        $post = ['post' => 'baz'];
        $cookie = ['cookie' => 'lorem'];
        $files = ['files' => ['ipsum' => 'dolor']];

        // Act
        $request = new Request($server, $get, $post, $cookie, $files);

        // Assert
        $this->assertEquals($server, $request->getServer());
        $this->assertEquals($get, $request->getGet());
        $this->assertEquals($post, $request->getPost());
        $this->assertEquals($cookie, $request->getCookie());
        $this->assertEquals($files, $request->getFiles());
    }

    /** @test */
    public function itInitializezWithGlobalDefaultsIfNonePassedInConstructor()
    {
        /// Act
        $request = new Request();

        // Assert
        $this->assertEquals($_SERVER, $request->getServer());
        $this->assertEquals($_GET, $request->getGet());
        $this->assertEquals($_POST, $request->getPost());
        $this->assertEquals($_COOKIE, $request->getCookie());
        $this->assertEquals($_FILES, $request->getFiles());
    }

    /** @test */
    public function itGetsASpecificGetorPostParameter()
    {
        // Arrange
        $request = new Request([], ['foo' => 'bar']);

        // Act
        $parameter = $request->input('foo');

        // Assert
        $this->assertEquals('bar', $parameter);
    }

    /** @test */
    public function itGetsFileInformationFromAnUpload()
    {
        // Arrange
        $request = new Request([], [], [], [], [
            'foo' => [
                'name' => 'manki-kim-LLWS6gBToQ4-unsplash.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => '/var/folders/kc/m3sz9v8s3694m081z1fh62lc0000gn/T/618ad4a10a9d3.jpg',
                'error' => '0',
                'size' => '2499055',
            ]
        ]);

        // Act
        $file = $request->getFile('foo');

        // Assert
        $this->assertEquals('manki-kim-LLWS6gBToQ4-unsplash.jpg', $file['name']);
    }
}
