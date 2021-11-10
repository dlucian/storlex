<?php

namespace App\Drivers\ImageStorage;

class FileSystem extends ImageStorage
{
    /**
     * @var string
     */
    protected $storagePath;

    public function __construct()
    {
        $this->storagePath = ROOT . '/storage/original/';
    }

    public function save(array $image): void
    {
        if (!is_string($image['name']) || ! is_string($image['file'])) {
            throw new \InvalidArgumentException('Image name must be a string');
        }
        $storagePath = $this->getStoragePath($image['name']);
        if (!is_dir(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0777, true);
        }
        copy($image['file'], $storagePath);
    }

    public function load(string $name): ?string
    {
        $storagePath = $this->getStoragePath($name);
        if (!file_exists($storagePath)) {
            return null;
        }
        return file_get_contents($storagePath) ?: null;
    }

    public function remove(string $name): void
    {
        $storagePath = $this->getStoragePath($name);
        if (file_exists($storagePath)) {
            unlink($storagePath);
        }
    }

    public function exists(string $name): bool
    {
        return file_exists($this->getStoragePath($name));
    }

    /**
     * Generate a path to the image.
     *
     * To keep OS efficiency, we are avoiding saving all the images
     * in the same folder, so we're splitting the images into 3-letter folders
     * based on their hash.
     *
     * @param string $imageName Image name
     * @return string The full path to where the image should exist
     */
    public function getStoragePath(string $imageName): string
    {
        return sprintf('%s%s/%s', $this->storagePath, substr(md5($imageName), 0, 3), $imageName);
    }
}
