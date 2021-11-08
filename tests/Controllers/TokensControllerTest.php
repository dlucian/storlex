<?php

namespace Tests;

use App\Database;
use App\Token;
use PHPUnit\Framework\TestCase;

class TokensControllerTest extends TestCase
{
    public function setUp(): void
    {
        Database::execute('CREATE TABLE IF NOT EXISTS tokens (token VARCHAR(100) NOT NULL PRIMARY KEY)');
        Database::execute('DELETE FROM tokens');
    }

    /** @test */
    public function itAddsAToken()
    {
        // Arrange
        $token = uniqid(md5((string)time()), true);

        // Act
        $controller = new \App\Controllers\TokensController();
        $response = $controller->grant($token);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue((new Token())->isValid($token));
    }
}
