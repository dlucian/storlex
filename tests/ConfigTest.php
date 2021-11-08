<?php

namespace Tests;

use App\Config;
use PHPUnit\Framework\TestCase;

/**
 * ConfigTest
 */
class ConfigTest extends TestCase
{
    /** @test */
    public function itLoadsConfigValuesInArray()
    {
        // Arrange
        $enviroment = [
            'TEST_APP_ENV' => 'local',
        ];

        // Act
        $config = new Config();
        $config->load($enviroment);

        // Assert
        $this->assertEquals('local', $enviroment['TEST_APP_ENV']);
    }

    /** @test */
    public function itThrowsExceptionIfConfigFileIsMissing()
    {
        $env = [];
        $this->expectException(\Exception::class);

        $config = new Config('/foo/bar.ini');
        $config->load($env);
    }
}
