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
        copy($image['file'], $this->storagePath . $image['name']);
    }
}
