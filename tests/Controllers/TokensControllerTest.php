<?php

namespace Tests;

use App\Database;
use App\Request;
use App\Token;
use PHPUnit\Framework\TestCase;

class TokensControllerTest extends TestCase
{
    public function setUp(): void
    {
        Database::execute('CREATE TABLE IF NOT EXISTS tokens (token VARCHAR(100) NOT NULL PRIMARY KEY)');
        Database::execute('DELETE FROM tokens');

        $_ENV['ADMIN_TOKEN'] = ['testTOKEN'];
    }

    /** @test */
    public function itAddsAToken()
    {
        // Arrange
        $random = uniqid(md5((string)time()), true);
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer testTOKEN'], [], ['token' => $random]);

        // Act
        $controller = new \App\Controllers\TokensController();
        $response = $controller->grant($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue((new Token($random))->isValid());
    }

    /** @test */
    public function itDeniesAddingATokenWithoutAdminToken()
    {
        // Arrange
        $random = uniqid(md5((string)time()), true);
        $request = new Request([], [], ['token' => $random]);

        // Act
        $controller = new \App\Controllers\TokensController();
        $response = $controller->grant($request);

        // Assert
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertFalse((new Token($random))->isValid());
    }

    /** @test */
    public function itReturns422UnprocessableEntityIfMissingTokenOnGrant()
    {
        // Arrange
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer testTOKEN']);

        // Act
        $controller = new \App\Controllers\TokensController();
        $response = $controller->grant($request);

        // Assert
        $this->assertEquals(422, $response->getStatusCode());
    }

    /** @test */
    public function itRevokesAToken()
    {
        // Arrange
        $random = uniqid(md5((string)time()), true);
        (new Token($random))->grant();
        $this->assertTrue((new Token($random))->isValid());
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer testTOKEN'], [], ['token' => $random]);

        // Act
        $controller = new \App\Controllers\TokensController();
        $response = $controller->revoke($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertFalse((new Token($random))->isValid());
    }

    /** @test */
    public function itDeniesRevokingATokenWithoutAdminTokenHeader()
    {
        // Arrange
        $random = uniqid(md5((string)time()), true);
        (new Token($random))->grant();
        $this->assertTrue((new Token($random))->isValid());
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer otherToken'], [], ['token' => $random]);

        // Act
        $controller = new \App\Controllers\TokensController();
        $response = $controller->revoke($request);

        // Assert
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue((new Token($random))->isValid());
    }

    /** @test */
    public function itReturns422UnprocessableEntityIfMissingTokenOnRevoke()
    {
        // Arrange
        $request = new Request(['HTTP_AUTHORIZATION' => 'Bearer testTOKEN']);

        // Act
        $controller = new \App\Controllers\TokensController();
        $response = $controller->revoke($request);

        // Assert
        $this->assertEquals(422, $response->getStatusCode());
    }
}
