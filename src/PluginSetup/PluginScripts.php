<?php

/**
 * Plugin Scripts
 *
 * @package WpRollback\Free\PluginSetup
 */

declare(strict_types=1);

namespace WpRollback\Free\PluginSetup;

use WpRollback\Free\Core\Constants;
use WpRollback\SharedCore\Core\Assets\AssetsManager;
use WpRollback\SharedCore\Core\SharedCore;
use WpRollback\SharedCore\Rollbacks\Registry\RollbackStepRegisterer;
/**
 * Handles script and style registration for the free plugin.
 *
 */
class PluginScripts
{
    /**
     * Initialize scripts.
     *
     * @return void
     */
    public function initialize(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueCommandPaletteScript']);
    }

    /**
     * Enqueue plugin assets.
     *
     * @return void
     */
    public function enqueueAssets(): void
    {
        $assetsManager = SharedCore::container()->make(AssetsManager::class);
        
        // Determine the correct admin URL based on context
        $adminUrl = is_network_admin()
            ? network_admin_url('settings.php?page=wp-rollback')
            : admin_url('tools.php?page=wp-rollback');
        
        $assetsManager->enqueueScript('tools', [
            'rollback_nonce' => wp_create_nonce('wpr_rollback_nonce'),
            'restApiNonce' => wp_create_nonce('wp_rest'),
            'adminUrl' => $adminUrl,
            'pluginsUrl' => admin_url('plugins.php'),
            'themesUrl' => admin_url('themes.php'),
            'restUrl' => esc_url_raw(rest_url()),
            'rollbackSteps' => $this->getRollbackSteps(),
        ]);
    }

    /**
     * Enqueue the command palette script on all admin pages.
     *
     * Bypasses AssetsManager's screen restriction intentionally — the command
     * palette must be available everywhere in wp-admin, not just the Tools page.
     *
     * @return void
     */
    public function enqueueCommandPaletteScript(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Graceful degradation: command palette requires WP 6.3+
        if (!wp_script_is('wp-commands', 'registered')) {
            return;
        }

        $constants = SharedCore::container()->make(Constants::class);
        $assetFile = $constants->getPluginDir() . '/build/commandPalette.asset.php';
        $assetData = file_exists($assetFile)
            ? require $assetFile
            : ['dependencies' => [], 'version' => $constants->getVersion()];

        $handle = $constants->getSlug() . '-command-palette';

        wp_enqueue_script(
            $handle,
            $constants->getPluginUrl() . '/build/commandPalette.js',
            $assetData['dependencies'],
            $assetData['version'],
            true
        );

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = [];
        foreach (get_plugins() as $pluginFile => $pluginData) {
            $pluginFile = (string) $pluginFile;
            $slug = dirname($pluginFile);
            if ('.' === $slug) {
                $slug = basename($pluginFile, '.php');
            }
            $plugins[] = [
                'name' => $pluginData['Name'],
                'slug' => $slug,
            ];
        }

        $themes = [];
        foreach (wp_get_themes() as $themeSlug => $themeObject) {
            $themes[] = [
                'name' => $themeObject->get('Name'),
                'slug' => $themeSlug,
            ];
        }

        $adminUrl = is_network_admin()
            ? network_admin_url('settings.php?page=wp-rollback')
            : admin_url('tools.php?page=wp-rollback');

        wp_localize_script(
            $handle,
            'wprCommandPaletteData',
            [
                'plugins'  => $plugins,
                'themes'   => $themes,
                'adminUrl' => $adminUrl,
            ]
        );

        wp_set_script_translations($handle, $constants->getTextDomain(), $constants->getPluginDir() . '/languages');
    }

    /**
     * Get rollback steps data for script localization.
     *
     * @return array
     */
    protected function getRollbackSteps(): array
    {
        $stepRegisterer = SharedCore::container()->make(RollbackStepRegisterer::class);
        $steps = [];
        
        foreach ($stepRegisterer->getAllRollbackSteps() as $stepClass) {
            $steps[] = [
                'id' => $stepClass::id(),
                'rollbackProcessingMessage' => $stepClass::rollbackProcessingMessage()
            ];
        }
        
        return $steps;
    }
} 