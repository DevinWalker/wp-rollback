<?php

/**
 * This class is used to manage the application features and make it available to the application.
 *
 * @package WpRollback\PluginSetup
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\PluginSetup;

use WpRollback\Free\Core\Constants;
use WpRollback\SharedCore\Core\Contracts\ServiceProvider;
use WpRollback\SharedCore\Core\Exceptions\Primitives\InvalidArgumentException;
use WpRollback\Free\Core\Request;
use WpRollback\Free\Dependencies\StellarWP\AdminNotices\AdminNotices;
use WpRollback\SharedCore\Core\Hooks;
use WpRollback\SharedCore\PluginSetup\PluginSetup as BasePluginSetup;
use WpRollback\SharedCore\PluginSetup\PluginManager;
use WpRollback\SharedCore\Core\Exceptions\BindingResolutionException;
use WpRollback\SharedCore\Core\SharedCore;

/**
 * Class Plugin
 *
 * @since 3.0.0
 */
class PluginSetup extends BasePluginSetup
{
    /**
     * The Request class is used to manage the request data.
     *
     * @since 3.0.0
     */
    protected Request $request;

    /**
     * Constants instance
     *
     * @since 3.0.0
     */
    protected ?Constants $constants = null;

    /**
     * This is a list of service providers that will be loaded into the application.
     *
     * @since 3.0.0
     */
    protected array $serviceProviders = [
        \WpRollback\Free\Core\ServiceProvider::class,
        \WpRollback\Free\Rollbacks\ServiceProvider::class,
        \WpRollback\SharedCore\Rollbacks\ServiceProvider::class,
        \WpRollback\SharedCore\RestAPI\ServiceProvider::class,
    ];

    /**
     * Bootstraps the WpRollback Plugin
     *
     * @since 3.0.0
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
     * @since 3.0.0
     */
    public function init(): void
    {
        /**
         * Fires before the WpRollback core is initialized.
         *
         * @since 3.0.0
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
         * @since 3.0.0
         *
         * @param self $instance Plugin class instance.
         *
         */
        do_action('wpr_init', $this);
    }

    /**
     * This function is used to set up language for application.
     *
     * @since 3.0.0
     */
    protected function setupLanguage(): void
    {
        Language::load();
    }

    /**
     * This function is used to load service providers.
     *
     * @since 3.0.0
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
     * @since 3.0.0.
     */
    protected function registerLibraries(): void
    {
        // Ensure Constants is available
        if (null === $this->constants) {
            $this->constants = SharedCore::container()->make(Constants::class);
        }
        
        AdminNotices::initialize(
            'wp-rollback',
            $this->constants->getPluginUrl() . '/vendor/vendor-prefixed/stellarwp/admin-notices'
        );
    }
    
    /**
     * Get the Constants instance
     *
     * @since 3.0.0
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
