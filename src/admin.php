<?php
/**
 * WP Rollback Plugin Admin
 *
 * @package WP Rollback
 */
function wpr_plugin_admin_scripts(): void
{
    $admin_js = 'build/admin.js';
    $script_asset = require WP_ROLLBACK_PLUGIN_DIR . '/build/admin.asset.php';

    wp_enqueue_script(
        'wp-rollback-plugin-admin-editor',
        plugins_url($admin_js, WP_ROLLBACK_PLUGIN_FILE),
        $script_asset['dependencies'],
        $script_asset['version']
    );
    wp_set_script_translations('wp-rollback-plugin-block-editor', 'wp-rollback');

    // Create a nonce
    $nonce = wp_create_nonce('wpr_rollback_nonce');

    // Localize the script with your nonce
    wp_localize_script('wp-rollback-plugin-admin-editor', 'wprData', [
        'nonce' => $nonce,
        'adminUrl' => admin_url('index.php'),
    ]);


    $admin_css = 'build/admin.css';
    wp_enqueue_style(
        'wp-rollback-plugin-admin',
        plugins_url($admin_css, WP_ROLLBACK_PLUGIN_FILE),
        ['wp-components'],
        filemtime(WP_ROLLBACK_PLUGIN_DIR . "/$admin_css")
    );
}

add_action('admin_enqueue_scripts', 'wpr_plugin_admin_scripts', 10);
