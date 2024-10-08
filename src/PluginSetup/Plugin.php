<?php

/**
 * This class is used to manage the application features and make it available to the application.
 *
 * @package WpRollback\PluginSetup
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\PluginSetup;

use WpRollback\Core\Constants;
use WpRollback\Core\Contracts\ServiceProvider;
use WpRollback\Core\EnqueueScript;
use WpRollback\Core\Exceptions\BindingResolutionException;
use WpRollback\Core\Exceptions\Primitives\InvalidArgumentException;
use WpRollback\Core\Hooks;
use WpRollback\Core\Request;
use function WpRollback\Core\container;
use function WpRollback\Core\dbMetaKeyGenerator;

/**
 * Class Plugin
 *
 * @unreleased
 */
class Plugin
{
    /**
     * This flag is used to check if the service providers have been loaded.
     *
     * @unreleased
     */
    private bool $providersLoaded = false;

    /**
     * The Request class is used to manage the request data.
     * @unreleased
     */
    protected Request $request;

    /**
     * This is a list of service providers that will be loaded into the application.
     *
     * @unreleased
     */
    private array $serviceProviders = [
        \WpRollback\Core\ServiceProvider::class,
    ];

    /**
     * Constructor
     * @unreleased
     */
    public function __construct()
    {
        $this->request = container(Request::class);
    }

    /**
     * Bootstraps the WpRollback Plugin
     *
     * @unreleased
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->setupConstant();

        Hooks::addAction('plugins_loaded', self::class, 'init');

        register_activation_hook(Constants::$PLUGIN_ROOT_FILE, [PluginManager::class, 'activate']);
        register_deactivation_hook(Constants::$PLUGIN_ROOT_FILE, [PluginManager::class, 'deactivate']);

        // Add plugin meta
        Hooks::addFilter(
            'plugin_row_meta',
            PluginMeta::class,
            'addPluginRowMeta',
            10,
            2
        );
        Hooks::addFilter(
            'plugin_action_links_' . Constants::$PLUGIN_ROOT_FILE_RELATIVE_PATH,
            PluginMeta::class,
            'addPluginSettingsMeta'
        );

        Hooks::addAction('admin_enqueue_scripts', self::class, 'addDeactivationAssets');
        Hooks::addAction('deactivated_plugin', self::class, 'handleDeactivation');
        Hooks::addAction('admin_footer', self::class, 'renderDeactivationModal');
    }

    /**
     * Initiate WpRollback when WordPress Initializes plugins.
     *
     * @unreleased
     */
    public function init(): void
    {
        /**
         * Fires before the WpRollback core is initialized.
         *
         * @unreleased
         */
        do_action('before_wprollback_init');


        $this->setupLanguage();
        $this->loadServiceProviders();

        /**
         * Fire the action after WpRollback core loads.
         *
         * @unreleased
         *
         * @param self $instance Plugin class instance.
         *
         */
        do_action('wprollback_init', $this);
    }

    /**
     * This function is used to set up language for application.
     * @unreleased
     */
    private function setupLanguage(): void
    {
        Language::load();
    }

    /**
     * This function is used to load service providers.
     *
     * @unreleased
     */
    private function loadServiceProviders(): void
    {
        if ($this->providersLoaded) {
            return;
        }

        $providers = [];

        foreach ($this->serviceProviders as $serviceProvider) {
            if (!is_subclass_of($serviceProvider, ServiceProvider::class)) {
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
     * This function is used to set up constants.
     *
     * @unreleased
     * @throws BindingResolutionException
     */
    private function setupConstant(): void
    {
        container()->singleton(Constants::class);

        // Set up the plugin constants.
        // Few constants aka static properties are set in the Constants class constructor.
        container(Constants::class);
    }

    /**
     * Add the assets for the Deactivation modal.
     *
     * @param string $hook_suffix The current admin page.
     * @unreleased
     */
    public function addDeactivationAssets(string $hook_suffix): void
    {
        if ('plugins.php' !== $hook_suffix) {
            return;
        }

        $scriptId = 'wprollback-plugin-deactivation';

        (new EnqueueScript($scriptId, "/build/$scriptId.js"))
            ->loadInFooter()
            ->loadStyle()
            ->enqueue();
    }

    /**
     * Handle the plugin deactivation.
     *
     * This function updates the option `delete_all_data_on_delete`
     * when the plugin is deactivated.
     *
     * @param string $plugin Path to the plugin file relative to the plugins directory.
     * @unreleased
     */
    public function handleDeactivation(string $plugin): void
    {
        if (Constants::$PLUGIN_ROOT_FILE_RELATIVE_PATH !== $plugin) {
            return;
        }

        if (!$this->request->hasPermission('deactivate_plugin')) {
            return;
        }

        check_admin_referer('deactivate-plugin_' . $plugin);

    }

    /**
     * Render the Deactivation modal.
     *
     * @unreleased
     */
    public function renderDeactivationModal(): void
    {
        global $pagenow;

        if ('plugins.php' !== $pagenow) {
            return;
        }

        include_once __DIR__ . '/Views/Deactivation/templates/DeactivationModal.php';
    }
}

// @todo setup exception handler
