<?php

/**
 * Admin Menu
 *
 * This file is responsible for setting up the admin menu.
 *
 * @package WpRollback\AdminDashboard
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\Rollbacks;

use WpRollback\Core\Constants;
use WpRollback\Core\EnqueueScript;
use WpRollback\Core\Exceptions\BindingResolutionException;
use WpRollback\Core\Exceptions\Primitives\Exception;
use WpRollback\Core\Exceptions\Primitives\InvalidPropertyException;
use WP_Admin_Bar;

/**
 * Class AdminMenu
 *
 * @package WpRollback\AdminDashboard
 * @unreleased
 */
class Admin {

    /**
     * @unreleased
     */
    private string $baseSlug = Constants::PLUGIN_SLUG;


    /**
     * Class constructor.
     *
     * @unreleased
     */
    public function __construct() {

    }

    public function registerMenu() {
        // Only show menu item when necessary (user is interacting with plugin, ie rolling back something)
        if ( isset( $_GET['page'] ) && $_GET['page'] === Constants::PLUGIN_SLUG ) {
            // Add it in a native WP way, like WP updates do... (a dashboard page)
            add_dashboard_page(
                __( 'Rollback', 'wp-rollback' ),
                __( 'Rollback', 'wp-rollback' ),
                'update_plugins',
                'wp-rollback',
                [ $this, 'html' ]
            );
        }
    }

    /**
     * Plugin Action Links
     *
     * Adds a "rollback" link into the plugins listing page w/ appropriate query strings
     *
     * @param $actions
     * @param $plugin_file
     * @param $plugin_data
     * @param $context
     *
     * @return array $actions
     */
    public function pluginActionLinks( $actions, $plugin_file, $plugin_data, $context ): array {

        if ( is_multisite() && ! is_network_admin() ) {
            return $actions;
        }

        // Filter for other devs.
        $plugin_data = apply_filters( 'wpr_plugin_data', $plugin_data );

        // If plugin is missing package data do not output Rollback option.
        if ( ! isset( $plugin_data['package'] ) || ! is_string( $plugin_data['package'] ) ||
             ( strpos( $plugin_data['package'], 'downloads.wordpress.org' ) === false ) ) {
            return $actions;
        }

        // Must have version.
        if ( ! isset( $plugin_data['Version'] ) ) {
            return $actions;
        }

        // Base rollback URL
        $rollback_url = is_network_admin() ? network_admin_url( 'index.php' ) : admin_url( 'index.php' );

        $rollback_url = add_query_arg(
            apply_filters(
                'wpr_plugin_query_args', [
                    'page'            => Constants::PLUGIN_SLUG,
                    'type'            => 'plugin',
                    'plugin_file'     => $plugin_file,
                    'current_version' => urlencode( $plugin_data['Version'] ),
                    'rollback_name'   => urlencode( $plugin_data['Name'] ),
                    'plugin_slug'     => urlencode( $plugin_data['slug'] ),
                    //TODO: Use internal plugin nonce function.
                    '_wpnonce'        => wp_create_nonce( 'wpr_rollback_nonce' ),
                ]
            ),
            $rollback_url
        );

        // Final Output
        $actions['rollback'] = apply_filters(
            'wpr_plugin_markup',
            '<a href="' . esc_url( $rollback_url ) . '">' . __( 'Rollback', 'wp-rollback' ) . '</a>'
        );

        return apply_filters( 'wpr_plugin_action_link', $actions );
    }

    /**
     * Enqueue plugin admin scripts and styles.
     *
     * @unreleased
     */
    public function enqueueAdminScripts(): void {
        $prefix = Constants::PLUGIN_SLUG;

        $dashBoardScript = ( new EnqueueScript(
            $prefix . '-dashboard',
            '/build/dashboard.js'
        ) );

        $dashBoardScript
            ->loadInFooter()
            ->loadStyle( [ 'wp-admin', 'wp-components' ] )
            ->registerLocalizeData( 'stellarPayDashboardData', $this->getDashboardData() )
            ->registerTranslations()
            ->enqueue();


        if ( ! in_array( $hook, [ 'index_page_wp-rollback', 'dashboard_page_wp-rollback' ] ) ) {
            return;
        }

        $script_asset = require Constants::$PLUGIN_DIR . 'build/admin.asset.php';

        wp_enqueue_script( 'updates' );
        wp_enqueue_script(
            'wp-rollback-plugin-admin-editor',
            plugins_url( 'build/admin.js', Constants::$PLUGIN_ROOT_FILE ),
            $script_asset['dependencies'],
            $script_asset['version']
        );
        // For i18n.
        wp_set_script_translations( 'wp-rollback-plugin-admin-editor', 'wp-rollback', Constants::$PLUGIN_DIR . 'languages' );

        // Localize the script with vars for JS.
        wp_localize_script( 'wp-rollback-plugin-admin-editor', 'wprData', [
            'rollback_nonce'          => wp_create_nonce( 'wpr_rollback_nonce' ),
            'restApiNonce'            => wp_create_nonce( 'wp_rest' ),
            'adminUrl'                => admin_url( 'index.php' ),
            'restUrl'                 => esc_url_raw( rest_url() ),
            'logo'                    => plugins_url( 'src/assets/logo.svg', Constants::$PLUGIN_ROOT_FILE ),
            'avatarFallback'          => plugins_url( 'src/assets/avatar-plugin-fallback.jpg', Constants::$PLUGIN_ROOT_FILE ),
            'referrer'                => wp_get_referer(),
            'text_no_changelog_found' => isset( $_GET['plugin_slug'] ) ? sprintf(
            // translators: %s Link.
                __(
                    'Sorry, we couldn\'t find a changelog entry found for this version. Try checking the <a href="%s" target="_blank">developer log</a> on WP.org.',
                    'wp-rollback'
                ),
                'https://wordpress.org/plugins/' . $_GET['plugin_slug'] . '/#developers'
            ) : '',
            'version_missing'         => __( 'Please select a version number to perform a rollback.', 'wp-rollback' ),
        ] );

        wp_enqueue_style(
            'wp-rollback-plugin-admin',
            plugins_url( 'build/admin.css', Constants::$PLUGIN_ROOT_FILE ),
            [ 'wp-components' ],
            filemtime( Constants::$PLUGIN_DIR . 'build/admin.css' )
        );

    }

}
