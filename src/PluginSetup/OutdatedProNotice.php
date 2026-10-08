<?php

/**
 * Stand-down for sites running WP Rollback next to WP Rollback Pro 1.4.2 or older.
 *
 * Free and Pro both ship the WpRollback\SharedCore classes, and whichever
 * plugin loads first defines them for both. Pro 1.4.2 and older load before
 * Free and boot as soon as their file is included, so their older copy wins.
 * This version can't run on that copy: declaring Free's PluginSetup against
 * it is a fatal error on every request.
 *
 * So when the older copy is in memory, Free doesn't boot and shows this notice
 * instead. Pro 1.4.2 doesn't load its features or its updater while Free is
 * active either, so the way out is to deactivate Free.
 *
 * Nothing here may touch another SharedCore class, or Free's PluginSetup.
 *
 * @package WpRollback\Free\PluginSetup
 */

declare(strict_types=1);

namespace WpRollback\Free\PluginSetup;

use WpRollback\SharedCore\PluginSetup\PluginSetup as BasePluginSetup;

/**
 * Detects the older shared code and tells the admin how to get out of it.
 */
class OutdatedProNotice
{
    /**
     * @var string WP Rollback's plugin file, relative to the plugins folder
     */
    private string $pluginFile;

    /**
     * @param string $pluginFile WP Rollback's plugin file, relative to the plugins folder
     */
    public function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

    /**
     * Whether an older WP Rollback Pro has already loaded its copy of the shared code.
     *
     * loadPreBootServiceProviders() is the first thing boot() calls and only
     * exists in the copy this version was built with.
     *
     * @param string $baseClass The shared base class in memory. Only tests pass another one.
     */
    public static function isSharedCoreOutdated(string $baseClass = BasePluginSetup::class): bool
    {
        return !method_exists($baseClass, 'loadPreBootServiceProviders');
    }

    /**
     * Show the notice on site and network admin screens.
     */
    public function register(): void
    {
        add_action('admin_notices', [$this, 'render']);
        add_action('network_admin_notices', [$this, 'render']);
    }

    /**
     * Persistent warning with a one-click way to deactivate WP Rollback.
     */
    public function render(): void
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        $message = sprintf(
            '%s &nbsp; <a class="button button-secondary button-small" href="%s">%s</a>',
            esc_html__(
                'WP Rollback is paused because it can\'t run alongside this older version of WP Rollback Pro. Deactivate WP Rollback to keep using Pro, then update WP Rollback Pro.',
                'wp-rollback'
            ),
            esc_url($this->buildDeactivateUrl()),
            esc_html__('Deactivate WP Rollback', 'wp-rollback')
        );

        wp_admin_notice(
            $message,
            [
                'type' => 'warning',
                'id' => 'wpr-outdated-pro-notice',
                'attributes' => ['role' => 'alert'],
            ]
        );
    }

    /**
     * Nonced URL that deactivates WP Rollback through the standard plugins.php handler.
     */
    private function buildDeactivateUrl(): string
    {
        $adminUrl = (is_multisite() && is_plugin_active_for_network($this->pluginFile))
            ? network_admin_url('plugins.php')
            : self_admin_url('plugins.php');

        return wp_nonce_url(
            add_query_arg(
                [
                    'action' => 'deactivate',
                    'plugin' => $this->pluginFile,
                ],
                $adminUrl
            ),
            'deactivate-plugin_' . $this->pluginFile
        );
    }
}
