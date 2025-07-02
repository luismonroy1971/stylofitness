<?php

namespace StyleFitness\Config;

class CacheManager
{
    private static $instance = null;
    private $cacheDir;
    private $defaultTtl;

    private function __construct()
    {
        $this->cacheDir = ROOT_PATH . '/storage/cache';
        $this->defaultTtl = Environment::getInt('CACHE_TTL', 3600);

        // Create cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Get cache manager instance
     *
     * @return CacheManager
     */
    public static function getInstance(): CacheManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Store data in cache
     *
     * @param string $key
     * @param mixed $data
     * @param int|null $ttl Time to live in seconds
     * @return bool
     */
    public function set(string $key, $data, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        $filename = $this->getCacheFilename($key);

        $cacheData = [
            'data' => $data,
            'expires_at' => time() + $ttl,
            'created_at' => time(),
        ];

        $serialized = serialize($cacheData);

        return file_put_contents($filename, $serialized, LOCK_EX) !== false;
    }

    /**
     * Get data from cache
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $filename = $this->getCacheFilename($key);

        if (!file_exists($filename)) {
            return $default;
        }

        $content = file_get_contents($filename);
        if ($content === false) {
            return $default;
        }

        $cacheData = unserialize($content);
        if ($cacheData === false) {
            return $default;
        }

        // Check if cache has expired
        if (time() > $cacheData['expires_at']) {
            $this->delete($key);
            return $default;
        }

        return $cacheData['data'];
    }

    /**
     * Check if cache key exists and is not expired
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $filename = $this->getCacheFilename($key);

        if (!file_exists($filename)) {
            return false;
        }

        $content = file_get_contents($filename);
        if ($content === false) {
            return false;
        }

        $cacheData = unserialize($content);
        if ($cacheData === false) {
            return false;
        }

        // Check if cache has expired
        if (time() > $cacheData['expires_at']) {
            $this->delete($key);
            return false;
        }

        return true;
    }

    /**
     * Delete cache entry
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $filename = $this->getCacheFilename($key);

        if (file_exists($filename)) {
            return unlink($filename);
        }

        return true;
    }

    /**
     * Clear all cache
     *
     * @return bool
     */
    public function clear(): bool
    {
        $files = glob($this->cacheDir . '/*.cache');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * Get or set cache with callback
     *
     * @param string $key
     * @param callable $callback
     * @param int|null $ttl
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $ttl = null)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $data = $callback();
        $this->set($key, $data, $ttl);

        return $data;
    }

    /**
     * Get cache filename for key
     *
     * @param string $key
     * @return string
     */
    private function getCacheFilename(string $key): string
    {
        $hash = md5($key);
        return $this->cacheDir . '/' . $hash . '.cache';
    }

    /**
     * Clean expired cache entries
     *
     * @return int Number of cleaned entries
     */
    public function cleanExpired(): int
    {
        $files = glob($this->cacheDir . '/*.cache');
        $cleaned = 0;

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $cacheData = unserialize($content);
            if ($cacheData === false) {
                unlink($file);
                $cleaned++;
                continue;
            }

            // Check if cache has expired
            if (time() > $cacheData['expires_at']) {
                unlink($file);
                $cleaned++;
            }
        }

        return $cleaned;
    }

    /**
     * Get cache statistics
     *
     * @return array
     */
    public function getStats(): array
    {
        $files = glob($this->cacheDir . '/*.cache');
        $totalSize = 0;
        $validEntries = 0;
        $expiredEntries = 0;

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $totalSize += filesize($file);

            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $cacheData = unserialize($content);
            if ($cacheData === false) {
                $expiredEntries++;
                continue;
            }

            if (time() > $cacheData['expires_at']) {
                $expiredEntries++;
            } else {
                $validEntries++;
            }
        }

        return [
            'total_entries' => count($files),
            'valid_entries' => $validEntries,
            'expired_entries' => $expiredEntries,
            'total_size' => $totalSize,
            'total_size_human' => $this->formatBytes($totalSize),
        ];
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Cache products for store
     *
     * @param array $products
     * @param string $category
     * @return bool
     */
    public function cacheProducts(array $products, string $category = 'all'): bool
    {
        return $this->set("products_{$category}", $products, 1800); // 30 minutes
    }

    /**
     * Get cached products
     *
     * @param string $category
     * @return array|null
     */
    public function getCachedProducts(string $category = 'all'): ?array
    {
        return $this->get("products_{$category}");
    }

    /**
     * Cache user routines
     *
     * @param int $userId
     * @param array $routines
     * @return bool
     */
    public function cacheUserRoutines(int $userId, array $routines): bool
    {
        return $this->set("user_routines_{$userId}", $routines, 3600); // 1 hour
    }

    /**
     * Get cached user routines
     *
     * @param int $userId
     * @return array|null
     */
    public function getCachedUserRoutines(int $userId): ?array
    {
        return $this->get("user_routines_{$userId}");
    }
}
