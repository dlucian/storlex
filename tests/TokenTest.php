<?php

namespace Tests;

use App\Database;
use App\Token;
use PHPUnit\Framework\TestCase;

/**
 * TokenTest
 */
class TokenTest extends TestCase
{
    public function setUp(): void
    {
        Database::execute('CREATE TABLE IF NOT EXISTS tokens (token VARCHAR(100) NOT NULL PRIMARY KEY)');
        Database::execute('DELETE FROM tokens');
    }

    /** @test */
    public function itCreatesAToken()
    {
        // Arrange
        $token = new Token();

        // Act
        $token->grant('foo');

        // Assert
        $this->assertTrue($token->isValid('foo'));
    }

    /** @test */
    public function itValidatesAPreviouslyCreatedToken()
    {
        // Arrange
        $token = new Token();

        // Act
        $token->grant('bar');

        // Assert
        $this->assertFalse($token->isValid('foo'));
    }

    /** @test */
    public function itRevokesAToken()
    {
        // Arrange
        $token = new Token();
        $token->grant('baz');
        $this->assertTrue($token->isValid('baz'));

        // Act
        $token->revoke('baz');

        // Assert
        $this->assertFalse($token->isValid('baz'));
    }

    /** @test */
    public function itCanGrantATokenThatWasAlreadyGranted()
    {
        // Arrange
        $token = new Token();

        // Act
        $token->grant('fooBar');
        $token->grant('fooBar');

        // Assert
        $this->assertTrue($token->isValid('fooBar'));
    }
}
