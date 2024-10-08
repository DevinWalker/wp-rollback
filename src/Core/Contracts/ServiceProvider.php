<?php

/**
 * ServiceProvider
 *
 * This class is a contract for defining Service Providers.
 *
 * @package WpRollback\Core\Exceptions
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Core\Contracts;

/**
 * Interface ServiceProvider
 *
 * For use when defining Service Providers, see the method docs for when to use them
 *
 * @unreleased
 */
interface ServiceProvider
{
    /**
     * Registers the Service Provider within the application. Use this to bind anything to the
     * Service Container. This prepares the service.
     *
     * @unreleased
     */
    public function register(): void;

    /**
     * The bootstraps the service after all of the services have been registered. The importance of this
     * is that any cross service dependencies should be resolved by this point, so it should be safe to
     * bootstrap the service.
     *
     * @unreleased
     */
    public function boot(): void;
}
