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
}
