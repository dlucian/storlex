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

    /**
     * Save an original image to the storage.
     *
     * @param array<string,string> $image Image info: 'file', 'name', 'type', 'size'
     */
    public function save(array $image): void
    {
        $storagePath = $this->getStoragePath($image['name']);
        if (!is_dir(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0777, true);
        }
        copy($image['file'], $storagePath);
    }

    /**
     * Retrieve an image from storage and return it.
     *
     * @param string $name Image file name
     * @return ?string The image binary data
     */
    public function get(string $name): ?string
    {
        $storagePath = $this->getStoragePath($name);
        if (!file_exists($storagePath)) {
            return null;
        }
        return file_get_contents($storagePath) ?: null;
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
