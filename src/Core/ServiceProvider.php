<?php

/**
 * Service Provider
 *
 * @package WpRollback\Free\Core
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Core;

use WpRollback\SharedCore\Core\DebugMode;
use WpRollback\SharedCore\Core\Cache;
use WpRollback\SharedCore\Core\Exceptions\BindingResolutionException;
use WpRollback\SharedCore\Core\Contracts\ServiceProvider as ServiceProviderContract;
use WpRollback\Free\Core\Constants;
use WpRollback\SharedCore\Core\SharedCore;
use WpRollback\SharedCore\Core\BaseConstants;
use WpRollback\SharedCore\Core\Request;
use WpRollback\Free\PluginSetup\PluginScripts;

/**
 * Class ServiceProvider
 *
 * @package WpRollback\Free\Core
 */
class ServiceProvider implements ServiceProviderContract
{
    /**
     * This function registers the service provider.
     *
     * @since 3.0.0
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        // Create a single instance of Constants
        $constants = new Constants();

        // Bind both BaseConstants and Constants to the same instance
        SharedCore::container()->singleton(BaseConstants::class, function () use ($constants) {
            return $constants;
        });

        SharedCore::container()->singleton(Constants::class, function () use ($constants) {
            return $constants;
        });

        // Register Request with Constants dependency
        SharedCore::container()->singleton(Request::class, function () use ($constants) {
            return new Request($constants);
        });

        // Register other services using the same constants instance
        SharedCore::container()->singleton(Cache::class, function () use ($constants) {
            return new Cache($constants->getSlug());
        });

        SharedCore::container()->singleton(DebugMode::class, function () {
            return DebugMode::makeWithWpDebugConstant();
        });

        // Register PluginScripts
        SharedCore::container()->singleton(PluginScripts::class);
    }

    /**
     * This function boots the service provider.
     *
     * @since 3.0.0
     */
    public function boot(): void
    {
    }
}
