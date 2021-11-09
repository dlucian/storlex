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

    /**
     * Retrieve an image from storage and return it.
     *
     * @param string $name Image file name
     * @return string The image binary data
     */
    public function get(string $name): ?string;
}
