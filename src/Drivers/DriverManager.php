<?php

namespace App\Drivers;

use App\Drivers\ImageCache\ImageCache;
use App\Drivers\ImageStorage\ImageStorage;
use App\Exceptions\InvalidDriverException;

/**
 * Driver Manager
 *
 * Used to provide various drivers for the application.
 */
class DriverManager
{
    public const IMAGE_STORAGE_DRIVERS = [
        'file' => \App\Drivers\ImageStorage\FileSystem::class,
    ];

    public const IMAGE_CACHE_DRIVERS = [
        'file' => \App\Drivers\ImageCache\FileSystem::class,
    ];

    /**
     * Provide an instance of the Image Storage driver
     *
     * @param ?string $driver
     * @return ImageStorage
     */
    public static function imageStorage(string $driver = null): ImageStorage
    {
        if ($driver === null) {
            $driver = $_ENV['IMAGE_STORAGE_DRIVER'];
        }

        if (!isset(self::IMAGE_STORAGE_DRIVERS[$driver])) {
            throw new InvalidDriverException(sprintf('Image storage driver %d not found', $driver));
        }

        $driverClass = self::IMAGE_STORAGE_DRIVERS[$driver];

        return new $driverClass();
    }

    /**
     * Provide an instance of the Image Cache driver
     *
     * @param ?string $driver
     * @return ImageCache
     */
    public static function imageCache(string $driver = null): ImageCache
    {
        if ($driver === null) {
            $driver = $_ENV['IMAGE_CACHE_DRIVER'];
        }

        if (!isset(self::IMAGE_CACHE_DRIVERS[$driver])) {
            throw new InvalidDriverException(sprintf('Image cache driver %d not found', $driver));
        }

        $driverClass = self::IMAGE_CACHE_DRIVERS[$driver];

        return new $driverClass();
    }
}
