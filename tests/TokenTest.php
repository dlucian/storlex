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
        // Act
        (new Token('foo'))->grant();

        // Assert
        $this->assertTrue((new Token('foo'))->isValid());
    }

    /** @test */
    public function itValidatesAPreviouslyCreatedToken()
    {
        // Arrange
        $token = new Token('bar');

        // Act
        $token->grant();

        // Assert
        $this->assertFalse((new Token('foo'))->isValid());
    }

    /** @test */
    public function itRevokesAToken()
    {
        // Arrange
        (new Token('baz'))->grant();
        $this->assertTrue((new Token('baz'))->isValid());

        // Act
        (new Token('baz'))->revoke();

        // Assert
        $this->assertFalse((new Token('baz'))->isValid());
    }

    /** @test */
    public function itCanGrantATokenThatWasAlreadyGranted()
    {
        // Act
        (new Token('fooBar'))->grant();
        (new Token('fooBar'))->grant();

        // Assert
        $this->assertTrue((new Token('fooBar'))->isValid());
    }
}
