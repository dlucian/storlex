<?php

namespace Tests;

use App\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    /** @test */
    public function itCanConnectAndRunAQuery()
    {
        // Act
        $result = Database::query("SELECT 5 + 5");

        // Assert
        $this->assertEquals(10, $result[0]['5 + 5']);
    }

    /** @test */
    public function itCanExecuteAQueryWithoutResults()
    {
        // Prepare
        $uniqueId = uniqid();

        // Act
        Database::execute('CREATE TABLE IF NOT EXISTS tokens (token VARCHAR(100) NOT NULL)');
        Database::execute('INSERT INTO tokens (token) VALUES (:token)', ['token' => $uniqueId]);

        // Assert
        $result = Database::query("SELECT token FROM tokens WHERE token = :token", ['token' => $uniqueId]);
        $this->assertEquals($uniqueId, $result[0]['token']);
    }
}
