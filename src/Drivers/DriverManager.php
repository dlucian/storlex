<?php

namespace App\Drivers;

use App\Drivers\ImageStorage\ImageStorage;

class DriverManager
{
    public const IMAGE_DRIVERS = [
        'filesystem' => \App\Drivers\ImageStorage\FileSystem::class,
    ];

    public static function imageStorage(string $driver = null): ImageStorage
    {
        if ($driver === null) {
            $driver = $_ENV['IMAGE_STORAGE_DRIVER'];
        }

        $driverClass = self::IMAGE_DRIVERS[$driver];

        return new $driverClass();
    }
}
