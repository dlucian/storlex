<?php

namespace App\Drivers\ImageStorage;

interface ImageStorageContract
{
    /**
     * Store an image.
     *
     * @param array<string, string|int> $image The Image to store.
     */
    public function save(array $image): void;
}
