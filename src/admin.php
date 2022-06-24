<?php

function wpr_plugin_settings_page()
{
    add_options_page(
        __('WP Rollback', 'wp-rollback'),
        __('WP Rollback', 'wp-rollback'),
        'manage_options',
        'wp_rollback',
        function () {
            ?>
            <div id="root-wp-rollback-admin"></div>
            <?php
        }
    );
}

add_action('admin_menu', 'wpr_plugin_settings_page', 10);


function wpr_plugin_admin_scripts()
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

    $admin_css = 'build/admin.css';
    wp_enqueue_style(
        'wp-rollback-plugin-admin',
        plugins_url($admin_css, WP_ROLLBACK_PLUGIN_FILE),
        ['wp-components'],
        filemtime(WP_ROLLBACK_PLUGIN_DIR . "/$admin_css")
    );
}

add_action('admin_enqueue_scripts', 'wpr_plugin_admin_scripts', 10);
