<?php

namespace App\Drivers;

use App\Drivers\ImageCache\ImageCache;
use App\Drivers\ImageProcessor\ImageProcessor;
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

    public const IMAGE_PROCESSOR_DRIVERS = [
        'gd2' => \App\Drivers\ImageProcessor\Gd2::class,
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
            $driver = $_ENV['IMAGE_STORAGE_DRIVER'] ?? 'file';
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
            $driver = $_ENV['IMAGE_CACHE_DRIVER'] ?? 'file';
        }

        if (!isset(self::IMAGE_CACHE_DRIVERS[$driver])) {
            throw new InvalidDriverException(sprintf('Image cache driver %d not found', $driver));
        }

        $driverClass = self::IMAGE_CACHE_DRIVERS[$driver];

        return new $driverClass();
    }

    /**
     * Provide an instance of the Image Cache driver
     *
     * @param ?string $driver
     * @return ImageProcessor
     */
    public static function imageProcessor(string $driver = null): ImageProcessor
    {
        if ($driver === null) {
            $driver = $_ENV['IMAGE_PROCESSOR_DRIVER'] ?? 'gd2';
        }

        if (!isset(self::IMAGE_PROCESSOR_DRIVERS[$driver])) {
            throw new InvalidDriverException(sprintf('Image processor driver %d not found', $driver));
        }

        $driverClass = self::IMAGE_PROCESSOR_DRIVERS[$driver];

        return new $driverClass();
    }
}
