<?php

/**
 * This class is used to manage the application features and make it available to the application.
 *
 * @package WpRollback\PluginSetup
 */

declare(strict_types=1);

namespace WpRollback\Free\PluginSetup;

use WpRollback\Free\Core\Constants;
use WpRollback\SharedCore\Core\Contracts\ServiceProvider;
use WpRollback\SharedCore\Core\Exceptions\Primitives\InvalidArgumentException;
use WpRollback\Free\Core\Request;
use WpRollback\SharedCore\Core\Hooks;
use WpRollback\SharedCore\PluginSetup\PluginSetup as BasePluginSetup;
use WpRollback\SharedCore\PluginSetup\PluginManager;
use WpRollback\SharedCore\Core\Exceptions\BindingResolutionException;
use WpRollback\SharedCore\Core\SharedCore;

/**
 * Class Plugin
 *
 */
class PluginSetup extends BasePluginSetup
{
    /**
     * The Request class is used to manage the request data.
     *
     */
    protected Request $request;

    /**
     * Constants instance
     *
     */
    protected ?Constants $constants = null;

    /**
     * This is a list of service providers that will be loaded into the application.
     *
     */
    protected array $serviceProviders = [
        \WpRollback\SharedCore\Core\ServiceProvider::class,
        \WpRollback\Free\Core\ServiceProvider::class,
        \WpRollback\Free\Rollbacks\ServiceProvider::class,
        \WpRollback\SharedCore\Rollbacks\ServiceProvider::class,
        \WpRollback\SharedCore\RestAPI\ServiceProvider::class,
    ];

    /**
     * Bootstraps the WpRollback Plugin
     *
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        // Get the Constants instance
        $this->constants = SharedCore::container()->make(Constants::class);
        
        Hooks::addAction('plugins_loaded', self::class, 'init');

        register_activation_hook($this->constants->getPluginFile(), [PluginManager::class, 'activate']);
        register_deactivation_hook($this->constants->getPluginFile(), [PluginManager::class, 'deactivate']);

        // Add plugin meta
        Hooks::addFilter( 'plugin_row_meta', PluginMeta::class, 'addPluginRowMeta', 10, 2 );
    }

    /**
     * Initiate WpRollback when WordPress Initializes plugins.
     *
     */
    public function init(): void
    {
        /**
         * Fires before the WpRollback core is initialized.
         *
         */
        do_action('before_wpr_init');

        // Ensure Constants is available
        if (null === $this->constants) {
            $this->constants = SharedCore::container()->make(Constants::class);
        }

        $this->setupLanguage();
        $this->registerLibraries();
        $this->loadServiceProviders();

        // Initialize scripts after service providers are loaded
        $scripts = SharedCore::container()->make(PluginScripts::class);
        $scripts->initialize();

        /**
         * Fire the action after WpRollback core loads.
         *
         *
         * @param self $instance Plugin class instance.
         *
         */
        do_action('wpr_init', $this);
    }

    /**
     * This function is used to set up language for application.
     *
     */
    protected function setupLanguage(): void
    {
        Language::load();
    }

    /**
     * This function is used to load service providers.
     *
     */
    protected function loadServiceProviders(): void
    {
        if ($this->providersLoaded) {
            return;
        }

        $providers = [];

        foreach ($this->serviceProviders as $serviceProvider) {
            if (! is_subclass_of($serviceProvider, ServiceProvider::class)) {
                throw new InvalidArgumentException(
                // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
                    "$serviceProvider class must implement the ServiceProvider interface"
                );
            }

            /** @var ServiceProvider $serviceProvider */
            $serviceProvider = new $serviceProvider();

            $serviceProvider->register();

            $providers[] = $serviceProvider;
        }

        foreach ($providers as $serviceProvider) {
            $serviceProvider->boot();
        }

        $this->providersLoaded = true;
    }

    /**
     * Register third-party libraries.
     *
     */
    protected function registerLibraries(): void
    {
        // No third-party libraries to register
    }
    
    /**
     * Get the Constants instance
     *
     *
     * @return Constants
     */
    public function getConstants(): Constants
    {
        if (null === $this->constants) {
            $this->constants = SharedCore::container()->make(Constants::class);
        }
        
        return $this->constants;
    }
}
