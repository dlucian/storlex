<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * TokenTest
 */
class TokenTest extends TestCase
{
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
