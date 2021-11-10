<?php

namespace App\Drivers;

interface ProcessorInterface
{
    /**
     * Load an image from a string
     *
     * @param string $binaryData
     * @return self
     */
    public function load(string $binaryData): self;

    /**
     * Resize the image to $width and $height
     *
     * @todo Add support for $mode (crop, fill, etc)
     * @param int $width
     * @param int $height
     * @return self
     */
    public function resize(int $width, int $height): self;

    /**
     * Render and return the image in the requested $format
     *
     * @param string $format
     * @return ?string The binary image data or null if it failed
     */
    public function render(string $format): ?string;
}
