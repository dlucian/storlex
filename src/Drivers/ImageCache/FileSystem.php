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

    public function get($key, string $tag = '', $default = null)
    {
        if (!file_exists($this->getCacheKey($key, $tag))) {
            return null;
        }
        return file_get_contents($this->getCacheKey($key, $tag)) ?: $default;
    }

    public function set($key, $value, string $tag = '', $ttl = null)
    {
        $directory = dirname($this->getCacheKey($key, $tag));
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        file_put_contents($this->getCacheKey($key, $tag), $value);
        return true;
    }

    public function delete($key, string $tag = '')
    {
        if ($this->has($key)) {
            unlink($this->getCacheKey($key, $tag));
        }
        return true;
    }

    public function deleteTag(string $tag)
    {
        $this->clearTagFolder($this->getCachePath() . $tag . '.tag');

        return true;
    }

    public function clear()
    {
        $folders = glob($this->getCachePath() . '{.[!.],}*', GLOB_BRACE | GLOB_ONLYDIR);
        if (is_array($folders)) {
            foreach ($folders as $folder) {
                $this->clearTagFolder($folder);
            }
        }
        return true;
    }

    protected function clearTagFolder(string $folder): bool
    {
        $files = glob($folder . '/*.cache');
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file) && substr($file, -6) === '.cache') {
                    unlink($file);
                }
            }
            if (is_dir($folder) && substr($folder, -4) === '.tag') {
                rmdir($folder);
            }
        }

        return true;
    }

    public function has($key, string $tag = '')
    {
        return file_exists($this->getCacheKey($key, $tag));
    }

    protected function getCacheKey(string $key, string $tag = ''): string
    {
        return $this->cachePath . urlencode($tag) . '.tag/' . urlencode($key) . '.cache';
    }
}
