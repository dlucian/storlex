<?php

namespace App\Controllers;

use App\Drivers\CacheInterface;
use App\Drivers\DriverManager;
use App\Drivers\ImageCache\ImageCache;
use App\Drivers\ImageProcessor\ImageProcessor;
use App\Drivers\ImageStorage\ImageStorage;
use App\Drivers\ProcessorInterface;
use App\Drivers\StorageInterface;
use App\Request;
use App\Response;
use App\Traits\ValidatesTokenRequest;

/**
 * Images Controller
 */
class ImagesController extends BaseController
{
    use ValidatesTokenRequest;

    /**
     * Retrieve image
     *
     * @param Request $request
     * @return Response
     */
    public function retrieve(
        string $imageSlug,
        Request $request,
        StorageInterface $storage = null,
        CacheInterface $cache = null,
        ProcessorInterface $processor = null
    ): Response {
        // Validate authorization
        if ($tokenRequest = $this->validateTokenRequest($request)) {
            return $tokenRequest;
        }
        if (strpos($imageSlug, '/') !== false || strpos($imageSlug, '\\') !== false) {
            return new Response(400, 'Invalid image slug');
        }

        // Load drivers
        if (is_null($storage)) {
            $storage = DriverManager::imageStorage();
        }
        if (is_null($cache)) {
            $cache = DriverManager::imageCache();
        }
        if (is_null($processor)) {
            $processor = DriverManager::imageProcessor();
        }

        $attributes = $this->expandAttributes($imageSlug);
        if ($attributes === null) {
            return new Response(400, 'Bad request');
        }

        // Do we have it cached?
        if ($cached = $cache->get($imageSlug, (string)$attributes['name'])) {
            if (!is_string($cached)) {
                return new Response(500, 'Cache error');
            }
            return new Response(
                200,
                $cached,
                ['Content-Type' => $this->extensionToMimeType($attributes['extension'])]
            );
        }

        // Retrieve image
        $image = $storage->load((string)$attributes['name']);
        if ($image === null) {
            return new Response(404, 'Not found');
        }

        // Process image
        try {
            $processed = $processor->load($image)
                ->resize((int)$attributes['width'], (int)$attributes['height'])
                ->render((string)$attributes['extension']);

            if ($processed === null) {
                return new Response(500, 'Processing error');
            }
        } catch (\Exception $e) {
            return new Response(500, 'Processing error: ' . $e->getMessage());
        }

        $cache->set($imageSlug, $processed, (string)$attributes['name']);

        return new Response(
            200,
            $processed,
            ['Content-Type' => $this->extensionToMimeType($attributes['extension'])]
        );
    }

    /**
     * @param mixed $extension
     */
    protected function extensionToMimeType($extension): string
    {
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
            case 'png':
                return 'image/png';
            case 'webp':
                return 'image/webp';
            default:
                return 'application/octet-stream';
        }
    }

    /**
     * Convert an IMG url such as
     * 'Screenshot 2021-11-03 at 16.50.20.png-300x200.jpg'
     * to parameters for the retrieve method, such as name,
     * width, height, filetype.
     *
     * @param string $image
     * @return ?array<string,string|int> The parameters if matched, or null.
     */
    public function expandAttributes(string $image): ?array
    {
        $matches = [];
        preg_match('/(.+)-(\d*)x(\d*)\.([a-zA-Z]{3,4})/', $image, $matches);
        if (empty($matches) || count($matches) < 5) {
            return null;
        }
        return [
            'name' => (string)$matches[1],
            'width' => (int)$matches[2],
            'height' => (int)$matches[3],
            'extension' => $matches[4] === 'jpg' ? 'jpeg' : (string)$matches[4],
        ];
    }
}
