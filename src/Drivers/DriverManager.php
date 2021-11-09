<?php

namespace App\Drivers;

use App\Drivers\ImageStorage\ImageStorage;
use App\Exceptions\InvalidDriverException;

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

        if (!isset(self::IMAGE_DRIVERS[$driver])) {
            throw new InvalidDriverException(sprintf('Image storage driver %d not found', $driver));
        }

        $driverClass = self::IMAGE_DRIVERS[$driver];

        return new $driverClass();
    }
}
