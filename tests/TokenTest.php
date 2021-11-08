<?php

namespace Tests;

use App\Database;
use PHPUnit\Framework\TestCase;

/**
 * TokenTest
 */
class TokenTest extends TestCase
{
    public function setUp(): void
    {
        Database::execute('CREATE TABLE IF NOT EXISTS tokens (token VARCHAR(100) NOT NULL)');
    }

    /** @test */
    public function itCreatesAToken()
    {
        // Arrange
        $token = new \App\Token();

        // Act
        $token->grant('foo');

        // Assert
        $this->assertTrue($token->isValid('foo'));
    }

    /** @test */
    public function itValidatesAPreviouslyCreatedToken()
    {
        // Arrange
        $token = new \App\Token();

        // Act
        $token->grant('bar');

        // Assert
        $this->assertFalse($token->isValid('foo'));
    }
}
