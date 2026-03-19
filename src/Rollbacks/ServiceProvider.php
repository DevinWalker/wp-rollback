<?php

/**
 * Service Provider
 *
 * This file is responsible for registering and booting the service provider for plugin admin dashboard.
 *
 * @package WpRollback\Rollbacks
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks;

use WpRollback\SharedCore\Core\Exceptions\BindingResolutionException;
use WpRollback\SharedCore\Core\Hooks;
use WpRollback\SharedCore\Core\Contracts\ServiceProvider as ServiceProviderContract;
use WpRollback\Free\Rollbacks\Actions\RegisterAdminMenu;
use WpRollback\Free\Rollbacks\PluginRollback\Actions\AddPluginRollbackLinks;
use WpRollback\Free\Rollbacks\PluginRollback\Actions\PreCurrentActivePlugins;
use WpRollback\Free\Rollbacks\ThemeRollback\Actions\AddMultisiteThemeRollbackLinks;
use WpRollback\Free\Rollbacks\ThemeRollback\Views\ThemeRollbackButton;
use WpRollback\SharedCore\Core\SharedCore;
use WpRollback\Free\Core\Constants;
use WpRollback\SharedCore\Rollbacks\Registry\RollbackStepRegisterer;
use WpRollback\Free\PluginSetup\PluginScripts;
use WpRollback\SharedCore\Rollbacks\Admin\AdminPageHeaderLinks;

/**
 * Class ServiceProvider.
 *
 */
class ServiceProvider implements ServiceProviderContract
{
    /**
     * @inheritdoc
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        // Register AddPluginRollbackLinks with Constants dependency
        SharedCore::container()->singleton(AddPluginRollbackLinks::class, function ($container) {
            return new AddPluginRollbackLinks($container->make(Constants::class));
        });

        // Register PluginScripts
        SharedCore::container()->singleton(PluginScripts::class);

        // Register AdminPageHeaderLinks with the free plugin slug
        SharedCore::container()->singleton(AdminPageHeaderLinks::class, function ($container) {
            return new AdminPageHeaderLinks($container->make(Constants::class)->getSlug());
        });
        
        // Override the shared RollbackStepRegisterer to exclude ValidatePackage and add UpsellValidatePackage
        // This uses the base steps and modifies them for the free version
        SharedCore::container()->singleton(RollbackStepRegisterer::class, function () {
            $registerer = new RollbackStepRegisterer();

            // Register all base steps from shared-core
            $registerer->register(RollbackStepRegisterer::getBaseSteps());
            
            // Insert the upsell step after BackupAsset (replacing ValidatePackage position)
            $registerer->registerAfter(
                \WpRollback\Free\Rollbacks\RollbackSteps\UpsellValidatePackage::class,
                \WpRollback\SharedCore\Rollbacks\RollbackSteps\BackupAsset::class
            );
            
            return $registerer;
        });
        
        // Note: Other shared services (ToolsPage, BackupAsset, BackupService, etc.)
        // are provided by the SharedCore\Rollbacks\ServiceProvider loaded before this one
    }

    /**
     * @inheritDoc
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

        // Inject page-level rollback links on plugins.php and themes.php
        SharedCore::container()->make(AdminPageHeaderLinks::class)->initialize();
        
        // Note: Backup directory setup is now handled by the shared RollbackServiceProvider
    }


    /**
     * @throws BindingResolutionException
     */
    private function bootToolsPage(): void
    {
        Hooks::addAction('admin_menu', RegisterAdminMenu::class);
    }

    /**
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
     * @return void
     * @throws BindingResolutionException
     */
    private function bootThemeRollback(): void
    {
        // Theme rollback.
        Hooks::addAction('admin_enqueue_scripts', ThemeRollbackButton::class);
    }

    /**
     * @throws BindingResolutionException
     */
    private function addMultiSiteSupport(): void
    {
        // For multisite support
        Hooks::addAction('network_admin_menu', self::class, 'registerMultisiteMenu');
        
        Hooks::addFilter('network_admin_plugin_action_links', AddPluginRollbackLinks::class, '__invoke', 20, 4);
        
        // Register factory for AddMultisiteThemeRollbackLinks
        // Note: This only applies in network admin - single sites use ThemeRollbackButton instead
        Hooks::registerFactory(
            AddMultisiteThemeRollbackLinks::class,
            function () {
                return SharedCore::container()->make(AddMultisiteThemeRollbackLinks::class);
            }
        );
        
        // Add theme rollback links in network admin themes table
        Hooks::addFilter('theme_action_links', AddMultisiteThemeRollbackLinks::class, '__invoke', 20, 3);
    }
    
    /**
     * Register multisite menu.
     *
     * @throws BindingResolutionException
     */
    public static function registerMultisiteMenu(): void
    {
        $adminMenu = SharedCore::container()->make(RegisterAdminMenu::class);
        $adminMenu->registerMultisiteMenu();
    }
}
