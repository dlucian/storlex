<?php

namespace App\Controllers;

use App\Drivers\CacheInterface;
use App\Drivers\DriverManager;
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
     * Defines maximum time an image processing job
     * should keep concurrent jobs waiting.
    */
    public const DEFAULT_LOCK_SECONDS = 15;

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
            return $this->errorJson(400, 'Invalid image slug');
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
            return $this->errorJson(400, 'Bad request');
        }

        if (!isset($_ENV['ALLOWED_SIZE'])) {
            return $this->errorJson(500, 'Missing ALLOWED_SIZE configuration');
        }

        if (!in_array(sprintf('%dx%d', $attributes['width'], $attributes['height']), $_ENV['ALLOWED_SIZE'])) {
            return $this->errorJson(404, 'Bad request');
        }

        // Is it being processed in another thread/job?
        while ($this->isLocked($imageSlug, $cache)) {
            sleep(1);
        }

        // Do we have it cached?
        if ($cached = $cache->get($imageSlug, (string)$attributes['name'])) {
            if (!is_string($cached)) {
                return $this->errorJson(500, 'Cache error');
            }
            return new Response(
                200,
                $cached,
                ['Content-Type' => $this->extensionToMimeType($attributes['extension'])]
            );
        }

        $this->setLock($imageSlug, self::DEFAULT_LOCK_SECONDS, $cache);

        // Retrieve image
        $image = $storage->load((string)$attributes['name']);
        if ($image === null) {
            return $this->error404();
        }

        // Process image
        try {
            $processed = $processor->load($image)
                ->resize((int)$attributes['width'], (int)$attributes['height'])
                ->render((string)$attributes['extension']);

            if ($processed === null) {
                return $this->errorJson(500, 'Processing error');
            }
        } catch (\Exception $e) {
            return $this->errorJson(500, 'Processing error: ' . $e->getMessage());
        }

        $cache->set($imageSlug, $processed, (string)$attributes['name']);

        $this->removeLock($imageSlug, $cache);

        return new Response(
            200,
            $processed,
            ['Content-Type' => $this->extensionToMimeType($attributes['extension'])]
        );
    }

    /**
     * Set a lock with an expiration time
     *
     * @param string $imageSlug
     * @param int $lockTime
     * @param CacheInterface $cache
     * @return void
     */
    protected function setLock(string $imageSlug, int $lockTime, CacheInterface $cache): void
    {
        $cache->set('locked-' . $imageSlug, time() + $lockTime);
    }

    /**
     * Check if an $imageSlug is locked and not expired
     *
     * @param string $imageSlug
     * @param CacheInterface $cache
     * @return bool
     */
    protected function isLocked(string $imageSlug, CacheInterface $cache): bool
    {
        $locked = $cache->get('locked-' . $imageSlug);
        return $locked !== null && $locked > time();
    }

    /**
     * Remove a lock
     *
     * @param string $imageSlug
     * @param CacheInterface $cache
     * @return void
     */
    protected function removeLock(string $imageSlug, CacheInterface $cache): void
    {
        $cache->delete('locked-' . $imageSlug);
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
