<?php

/**
 * This class is used to manage the application features and make it available to the application.
 *
 * @package WpRollback\PluginSetup
 */

declare(strict_types=1);

namespace WpRollback\Free\PluginSetup;

use WpRollback\Free\Core\Constants;
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
     * List of pre-boot service providers loaded before/during boot.
     *
     */
    protected array $preBootServiceProviders = [
        \WpRollback\SharedCore\Core\ServiceProvider::class,
        \WpRollback\Free\Core\ServiceProvider::class,
    ];

    /**
     * List of main service providers loaded during init.
     *
     */
    protected array $serviceProviders = [
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
        // Load pre-boot service providers early so core bindings & Constants are registered prior to boot
        $this->loadPreBootServiceProviders();

        // Get the Constants instance
        $this->constants = SharedCore::container()->make(Constants::class);

        Hooks::addAction('plugins_loaded', self::class, 'init');

        // Add plugin meta
        Hooks::addFilter('plugin_row_meta', PluginMeta::class, 'addPluginRowMeta', 10, 2);
    }

    /**
     * Static activation method called by WordPress activation hook.
     *
     * Registered at the top level of wp-rollback.php, not from boot(): the
     * activation request loads the plugin file after plugins_loaded has fired,
     * so boot() never runs during activation.
     */
    public static function activatePlugin(): void
    {
        PluginManager::activate(SharedCore::container()->make(Constants::class));
    }

    /**
     * Static deactivation method. Clears the report cron events (best practice
     * for a clean uninstall).
     */
    public static function deactivatePlugin(): void
    {
        PluginManager::deactivate(SharedCore::container()->make(Constants::class));
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

        PluginManager::handleVersionUpdates();

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
