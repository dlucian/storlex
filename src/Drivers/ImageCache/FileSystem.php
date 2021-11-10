<?php

namespace App\Drivers\ImageCache;

class FileSystem extends ImageCache
{
    /**
     * @var string
     */
    protected $cachePath;

    public function __construct()
    {
        $this->cachePath = ROOT . '/storage/cache/';
    }

    /**
     * Get the folder path for the cache.
     *
     * @return string
     */
    public function getCachePath(): string
    {
        return $this->cachePath;
    }

    public function get($key, $default = null)
    {
        if (!file_exists($this->cachePath . urlencode($key) . '.cache')) {
            return null;
        }
        return file_get_contents($this->cachePath . urlencode($key) . '.cache');
    }

    public function set($key, $value, $ttl = null)
    {
        file_put_contents($this->cachePath . urlencode($key) . '.cache', $value);
        return true;
    }

    public function delete($key)
    {
        if ($this->has($key)) {
            unlink($this->cachePath . urlencode($key) . '.cache');
        }
        return true;
    }

    public function clear()
    {
        $files = glob($this->getCachePath() . '*');
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        throw new \Exception('Not implemented');
    }

    public function setMultiple($values, $ttl = null)
    {
        throw new \Exception('Not implemented');
    }

    public function deleteMultiple($keys)
    {
        throw new \Exception('Not implemented');
    }

    public function has($key)
    {
        return file_exists($this->cachePath . urlencode($key) . '.cache');
    }
}
