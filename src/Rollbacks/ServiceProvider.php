<?php

/**
 * Service Provider
 *
 * This file is responsible for registering and booting the service provider for plugin admin dashboard.
 *
 * @package WpRollback\Rollbacks
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Rollbacks;

use WpRollback\Core\Exceptions\BindingResolutionException;
use WpRollback\Core\Exceptions\Primitives\InvalidPropertyException;
use WpRollback\Core\Hooks;
use function WpRollback\Core\container;

/**
 * Class ServiceProvider.
 *
* @unreleased`
 */
class ServiceProvider implements \WpRollback\Core\Contracts\ServiceProvider
{
    /**
     * @inheritdoc
     * @unreleased
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        container()->singleton(Admin::class);
    }

    /**
     * @inheritDoc
     * @unreleased
     * @throws BindingResolutionException
     * @throws InvalidPropertyException
     */
    public function boot(): void
    {
        Hooks::addAction('admin_menu', Admin::class, 'registerMenu');

        Hooks::addFilter('plugin_action_links', Admin::class, 'pluginActionLinks', 20, 4);

        add_action( 'admin_enqueue_scripts', [ Admin::class, 'enqueueAdminScripts' ] );

    }

}
