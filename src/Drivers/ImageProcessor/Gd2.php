<?php

namespace App\Drivers\ImageProcessor;

use App\Drivers\ProcessorInterface;
use App\Exceptions\ProcessingException;
use App\Exceptions\UnsupportedFormatException;

/**
 * GD2 Driver for image processing
 */
class Gd2 extends ImageProcessor
{
    public const DEFAULT_QUALITY = 80;
    public const ALLOWED_FORMATS = ['jpeg', 'png', 'webp'];

    /** @var resource|false */
    protected $image = false;

    /** @var int */
    protected $width = 0;

    /** @var int */
    protected $height = 0;

    /** @var float */
    protected $aspectRatio = 0;

    public function load(string $binaryData): self
    {
        $this->image = imagecreatefromstring($binaryData);
        if ($this->image) {
            $this->width = imagesx($this->image);
            $this->height = imagesy($this->image);
            if ($this->height) {
                $this->aspectRatio = $this->width / $this->height;
            }
        }

        return $this;
    }

    /**
     * @link https://www.php.net/manual/en/function.imagecopyresampled.php
     */
    public function resize(int $width, int $height): self
    {
        if (!is_resource($this->image)) {
            throw new ProcessingException('No image loaded');
        }
        $resized = imagecreatetruecolor($width, $height);
        if (!is_resource($resized)) {
            throw new ProcessingException('Failed to create intermediate image');
        }

        $ratio = max($width / $this->width, $height / $this->height);
        $h = $height / $ratio;
        $x = ($this->width - $width / $ratio) / 2;
        $y = ($this->height - $height / $ratio) / 2;
        $w = $width / $ratio;

        imagecopyresampled(
            $resized,        // GdImage $dst_image,
            $this->image,    // GdImage $src_image,
            0,               // $dst_x,
            0,               // $dst_y,
            (int)$x,              // $src_x
            (int)$y,              // $src_y,
            $width,          // $dst_width
            $height,         // $dst_height
            (int)$w,              // $src_width
            (int)$h               // $src_height
        );
        $this->image = $resized;
        return $this;
    }

    public function render(string $format): ?string
    {
        if (!in_array($format, self::ALLOWED_FORMATS)) {
            throw new UnsupportedFormatException('Unsupported format: ' . $format);
        }

        $renderFunction = sprintf('renderImage%s', ucfirst(strtolower($format)));

        ob_start();
        $this->$renderFunction();
        $finalImage = ob_get_contents();
        ob_end_clean();

        if ($finalImage === false) {
            return null;
        }

        return $finalImage;
    }

    /**
     * Render image as JPEG
     *
     * @return void
     */
    protected function renderImageJpeg(): void
    {
        if (!is_resource($this->image)) {
            ob_end_clean();
            throw new ProcessingException('No image loaded');
        }
        imagejpeg($this->image, null, self::DEFAULT_QUALITY);
    }

    /**
     * Render image as PNG
     */
    protected function renderImagePng(): void
    {
        if (!is_resource($this->image)) {
            ob_end_clean();
            throw new ProcessingException('No image loaded');
        }
        imagepng($this->image);
    }

    /**
     * Render image as WebP
     */
    protected function renderImageWebp(): void
    {
        if (!is_resource($this->image)) {
            ob_end_clean();
            throw new ProcessingException('No image loaded');
        }
        imagewebp($this->image, null, self::DEFAULT_QUALITY);
    }
}
