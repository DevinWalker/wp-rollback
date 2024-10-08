<?php

/**
 * Service Provider
 *
 * @package WpRollback\Core
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core;

use WpRollback\Core\Exceptions\BindingResolutionException;

/**
 * Class ServiceProvider
 *
 * @package WpRollback\Core
 */
class ServiceProvider implements Contracts\ServiceProvider
{
    /**
     * This function registers the service provider.
     *
     * @unreleased
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        container()->singleton(Cache::class);
    }

    /**
     * This function boots the service provider.
     *
     * @unreleased
     */
    public function boot(): void
    {
    }
}
