<?php

/**
 * Service Provider
 *
 * This file is responsible for registering and booting the service provider for plugin admin dashboard.
 *
 * @package WpRollback\Rollbacks
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks;

use WpRollback\SharedCore\Core\Exceptions\BindingResolutionException;
use WpRollback\SharedCore\Core\Hooks;
use WpRollback\SharedCore\Core\Contracts\ServiceProvider as ServiceProviderContract;
use WpRollback\Free\Rollbacks\Actions\RegisterAdminMenu;
use WpRollback\Free\Rollbacks\PluginRollback\Actions\AddPluginRollbackLinks;
use WpRollback\Free\Rollbacks\PluginRollback\Actions\PreCurrentActivePlugins;
use WpRollback\Free\Rollbacks\ThemeRollback\Actions\PrepareThemeJS;
use WpRollback\Free\Rollbacks\ThemeRollback\Actions\UpdateThemeList;
use WpRollback\Free\Rollbacks\ThemeRollback\Controllers\TypeConfirmationController;
use WpRollback\Free\Rollbacks\ThemeRollback\Views\ThemeRollbackButton;
use WpRollback\SharedCore\Core\SharedCore;
use WpRollback\Free\Core\Constants;
use WpRollback\SharedCore\Rollbacks\DTO\RollbackRequestDTO;
use WpRollback\SharedCore\Rollbacks\Registry\RollbackStepRegisterer;

use WpRollback\Free\PluginSetup\PluginScripts;
use WpRollback\SharedCore\Rollbacks\ToolsPage\ToolsPage;

/**
 * Class ServiceProvider.
 *
 * @since 3.0.0`
 */
class ServiceProvider implements ServiceProviderContract
{
    /**
     * @inheritdoc
     * @since 3.0.0
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        // Register RollbackRequestDTO with Constants dependency
        SharedCore::container()->singleton(RollbackRequestDTO::class, function ($container) {
            return new RollbackRequestDTO($container->make(Constants::class));
        });

        // Register AddPluginRollbackLinks with Constants dependency
        SharedCore::container()->singleton(AddPluginRollbackLinks::class, function ($container) {
            return new AddPluginRollbackLinks($container->make(Constants::class));
        });

        // Register PluginScripts
        SharedCore::container()->singleton(PluginScripts::class);
        
        // Override the shared RollbackStepRegisterer to exclude ValidatePackage and add UpsellValidatePackage
        // This replaces the shared registration to customize steps for the free plugin
        SharedCore::container()->singleton(RollbackStepRegisterer::class, function () {
            $registerer = new RollbackStepRegisterer();
            // Register base steps without ValidatePackage (pro feature)
            $registerer->addStep(\WpRollback\SharedCore\Rollbacks\RollbackSteps\DownloadAsset::class);
            $registerer->addStep(\WpRollback\SharedCore\Rollbacks\RollbackSteps\BackupAsset::class);
            // Add the upsell step instead of actual validation
            $registerer->addStep(\WpRollback\Free\Rollbacks\RollbackSteps\UpsellValidatePackage::class);
            $registerer->addStep(\WpRollback\SharedCore\Rollbacks\RollbackSteps\ReplaceAsset::class);
            $registerer->addStep(\WpRollback\SharedCore\Rollbacks\RollbackSteps\Cleanup::class);
            return $registerer;
        });
        
        // Note: Other shared services (ToolsPage, BackupAsset, BackupService, etc.)
        // are provided by the SharedCore\Rollbacks\ServiceProvider loaded before this one
    }

    /**
     * @inheritDoc
     * @since 3.0.0
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->bootToolsPage();
        $this->bootPluginRollback();
        $this->bootThemeRollback();
        $this->addMultiSiteSupport();

        // Initialize PluginScripts
        $scripts = SharedCore::container()->make(PluginScripts::class);
        $scripts->initialize();
        
        // Note: Backup directory setup is now handled by the shared RollbackServiceProvider
    }


    /**
     * @since 3.0.0
     * @throws BindingResolutionException
     */
    private function bootToolsPage(): void
    {
        Hooks::addAction('admin_menu', RegisterAdminMenu::class);
    }

    /**
     * @since 3.0.0
     * @throws BindingResolutionException
     */
    private function bootPluginRollback(): void
    {
        // Register factory for AddPluginRollbackLinks
        Hooks::registerFactory(
            AddPluginRollbackLinks::class,
            function () {
                return SharedCore::container()->make(AddPluginRollbackLinks::class);
            }
        );

        Hooks::addFilter('plugin_action_links', AddPluginRollbackLinks::class, '__invoke', 20, 4);
        Hooks::addAction('pre_current_active_plugins', PreCurrentActivePlugins::class, '__invoke', 20, 1);
    }

    /**
     * @since 3.0.0
     * @return void
     * @throws BindingResolutionException
     */
    private function bootThemeRollback(): void
    {
        Hooks::addAction('set_site_transient_update_themes', UpdateThemeList::class);
        Hooks::addFilter('wp_prepare_themes_for_js', PrepareThemeJS::class);
        Hooks::addAction('wp_ajax_is_wordpress_theme', TypeConfirmationController::class);
        Hooks::addAction('admin_enqueue_scripts', ThemeRollbackButton::class);
    }

    /**
     * @since 3.0.0
     * @throws BindingResolutionException
     */
    private function addMultiSiteSupport(): void
    {
        Hooks::addFilter('theme_action_links', MultisiteSupport::class, 'addThemeLink', 20, 2);
        
        // For multisite support
        Hooks::addAction('network_admin_menu', self::class, 'registerMultisiteMenu');
        
        Hooks::addFilter('network_admin_plugin_action_links', AddPluginRollbackLinks::class, '__invoke', 20, 4);
    }
    
    /**
     * Register multisite menu.
     *
     * @since 3.0.0
     * @throws BindingResolutionException
     */
    public static function registerMultisiteMenu(): void
    {
        $adminMenu = SharedCore::container()->make(RegisterAdminMenu::class);
        $adminMenu->registerMultisiteMenu();
    }
}
