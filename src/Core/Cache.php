<?php

/**
 * Cache class.
 *
 * This class a wrapper for WordPress cache functions.
 * It provides a simple way to set, get and delete cache data using WordPress cache functions.
 * It also provides a way use persistence or object cache for caching.
 *
 * @package WpRollback\Core
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core;

/**
 * Class Cache
 *
 * @unreleased
 */
class Cache
{
    /**
     * Cache group name.
     *
     * @unreleased
     */
    private string $cacheGroup;

    /**
     * Cache key prefix.
     *
     * This is used to prefix the persistence cache key name.
     *
     * @unreleased
     */
    private string $cacheKeyPrefix;

    /**
     * Constructor.
     *
     * @unreleased
     */
    public function __construct()
    {
        $this->cacheGroup = Constants::PLUGIN_SLUG;
        $this->cacheKeyPrefix = Constants::PLUGIN_SLUG;
    }

    /**
     * This method sets the cache.
     *
     * @unreleased
     */
    public function set(string $key, $value, int $expiration = 0, bool $cacheInDatabase = false): bool
    {
        if ($cacheInDatabase) {
            return set_transient($this->getCacheKey($key), $value, $expiration);
        }

        return wp_cache_set($key, $value, $this->cacheGroup, $expiration); // phpcs:ignore WordPressVIPMinimum.Performance.LowExpiryCacheTime.CacheTimeUndetermined
    }

    /**
     * This method gets the cache.
     *
     * @unreleased
     *
     * @return bool|mixed
     */
    public function get(string $key, bool $cacheInDatabase = false)
    {
        if ($cacheInDatabase) {
            return get_transient($this->getCacheKey($key));
        }

        return wp_cache_get($key, $this->cacheGroup);
    }

    /**
     * This method deletes the cache.
     *
     * @unreleased
     */
    public function delete(string $key, $cacheInDatabase = false): bool
    {
        if ($cacheInDatabase) {
            return delete_transient($this->getCacheKey($key));
        }

        return wp_cache_delete($key, $this->cacheGroup);
    }

    /**
     * This method gets the cache key.
     *
     * This method is used to get the cache key when data stores in database.
     *
     * @unreleased
     */
    private function getCacheKey(string $key): string
    {
        return "{$this->cacheKeyPrefix}_{$key}";
    }
}
