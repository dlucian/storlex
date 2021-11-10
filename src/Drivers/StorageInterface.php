<?php

namespace App\Drivers;

interface StorageInterface
{
    /**
     * Save an original image to the storage.
     *
     * @param array<string,string|int> $image Image info: 'file', 'name', 'type', 'size'
     */
    public function save(array $image): void;

    /**
     * Retrieve an image from storage and return it.
     *
     * @param string $name Image file name
     * @return ?string The image binary data
     */
    public function get(string $name): ?string;

    /**
     * Remove a file from storage.
     *
     * @param string $name
     * @return void
     */
    public function remove(string $name): void;

    /**
     * Check if a file exists in storage.
     *
     * @param string $name
     * @return bool True if exists, false otherwise
     */
    public function exists(string $name): bool;
}
