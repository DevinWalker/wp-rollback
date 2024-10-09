<?php

namespace WpRollback;

use stdClass;
use WP_REST_Request;
use WpRollback\Core\Constants;
use WpRollback\Rollbacks\ApiRequests;
use WpRollback\Rollbacks\Multisite;

class WpRollback {

    /**
     * Plugin file.
     *
     * @var string
     */
    public $plugin_file;

    /**
     * Plugin slug.
     *
     * @var string
     */
    public $plugin_slug;

    /**
     * Versions.
     *
     * @var array
     */
    public $versions = [];

    /**
     * Current version.
     *
     * @var string
     */
    public $current_version;

    private $multisiteCompatibility;

    public function __construct() {
        add_action('init', [$this, 'hooks']);
    }

    /**
     * Plugin hooks.
     *
     * @access private
     * @since  1.5
     * @return void
     */
    public function hooks(): void {

        // Multisite compatibility: only loads on main site.
        $this->multisiteCompatibility = new Multisite( $this );

        // i18n
//        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );

        // Normal WordPress WP-Admin
        add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );
        add_action( 'admin_menu', [ $this, 'admin_menu' ], 20 );
        add_action( 'pre_current_active_plugins', [ $this, 'pre_current_active_plugins' ], 20, 1 );
        add_action( 'wp_ajax_isWordPressTheme', [ $this, 'isWordPressTheme' ] );
        add_action( 'set_site_transient_update_themes', [ $this, 'themeUpdatesList' ] );

        // REST API
        add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );

        // Filters
        add_filter( 'wp_prepare_themes_for_js', [ $this, 'prepareThemesJs' ] );
        add_filter( 'plugin_action_links', [ $this, 'pluginActionLinks' ], 20, 4 );

    }

    /**
     * Enqueue Admin Scripts
     *
     * @access private
     * @since  1.0
     *
     * @param $hook
     *
     * @return void
     */
    public function scripts( $hook ): void {

        // Theme's listing page JS
        if ( 'themes.php' === $hook && ! is_multisite() ) {
            $theme_script_asset = require Constants::$PLUGIN_DIR . 'build/themes.asset.php';

            wp_enqueue_script(
                'wp-rollback-themes-script',
                Constants::$PLUGIN_URL . 'build/themes.js',
                $theme_script_asset['dependencies'],
                $theme_script_asset['version']
            );
            // Localize for i18n
            wp_localize_script(
                'wp-rollback-themes-script', 'wprData', [
                    'ajaxurl'               => admin_url(),
                    'rollback_nonce'        => wp_create_nonce( 'wpr_rollback_nonce' ),
                    'logo'                  => plugins_url( 'src/assets/logo.svg', Constants::$PLUGIN_ROOT_FILE ),
                    'avatarFallback'        => plugins_url( 'src/assets/avatar-plugin-fallback.jpg', Constants::$PLUGIN_ROOT_FILE ),
                    'text_rollback_label'   => __( 'Rollback', 'wp-rollback' ),
                    'text_not_rollbackable' => __(
                        'No Rollback Available: This is a non-WordPress.org theme.',
                        'wp-rollback'
                    ),
                    'text_loading_rollback' => __( 'Loading...', 'wp-rollback' ),
                ]
            );
        }

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

    public function register_rest_route() {
        include Constants::$PLUGIN_DIR . 'src/class-rollback-api-requests.php';

        register_rest_route( 'wp-rollback/v1', '/fetch-info/', [
            'methods'             => 'GET',
            'callback'            => function ( WP_REST_Request $request ) {
                $fetcher = new ApiRequests();

                return $fetcher->fetch_plugin_or_theme_info( $request['type'], $request['slug'] );
            },
            'permission_callback' => function () {
                return current_user_can( 'update_plugins' );
            },
            'args'                => [
                'type' => [
                    'required' => true,
                    'type'     => 'string',
                ],
                'slug' => [
                    'required' => true,
                    'type'     => 'string',
                ],
            ],
        ] );
    }

    /**
     * HTML
     */
    public function html(): void {
        // Permissions check
        if ( ! current_user_can( 'update_plugins' ) ) {
            wp_die( __( 'You do not have sufficient permissions to perform rollbacks for this site.', 'wp-rollback' ) );
        }

        // Get the necessary class
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $defaults = apply_filters(
            'wpr_rollback_html_args', [
                'page'           => 'wp-rollback',
                'plugin_file'    => '',
                'action'         => '',
                'plugin_version' => '',
                'plugin'         => '',
            ]
        );

        $args = wp_parse_args( $_GET, $defaults );

        check_admin_referer( 'wpr_rollback_nonce' );

        if ( ! empty( $args['plugin_version'] ) ) {
            // Plugin: rolling back.
            include Constants::$PLUGIN_DIR . 'src/PluginUpgrader.php';
            include Constants::$PLUGIN_DIR . 'src/rollback-action.php';
        } elseif ( ! empty( $args['theme_version'] ) ) {
            // Theme: rolling back.
            include Constants::$PLUGIN_DIR . 'src/class-rollback-theme-upgrader.php';
            include Constants::$PLUGIN_DIR . 'src/rollback-action.php';
        } else {
            // Rollback main screen.
            echo '<div id="root-wp-rollback-admin"></div>';
        }
    }

    /**
     * Admin Menu
     *
     * Adds a 'hidden' menu item that is activated when the user elects to rollback
     */
    public function admin_menu() {
        // Only show menu item when necessary (user is interacting with plugin, ie rolling back something)
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'wp-rollback' ) {
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
     * Pre-Current Active Plugins
     *
     * @param $plugins
     *
     * @return mixed
     */
    public function pre_current_active_plugins( $plugins ) {
        $updated = $plugins;
        foreach ( $updated as $key => $value ) {
            $updated[ $key ]             = $value;
            $updated[ $key ]['rollback'] = true;
        }

        return $updated;
    }

    /**
     * Setup Variables
     *
     * @access     private
     */
    private function setup_plugin_vars() {
        if ( ! isset( $_GET['plugin_file'] ) ) {
            return false;
        }

        if ( isset( $_GET['current_version'] ) ) {
            $curr_version          = explode( ' ', $_GET['current_version'] );
            $this->current_version = apply_filters( 'wpr_current_version', $curr_version[0] );
        }

        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $plugin_file = WP_PLUGIN_DIR . '/' . $_GET['plugin_file'];

        if ( ! file_exists( $plugin_file ) ) {
            wp_die( 'Plugin you\'re referencing does not exist.' );
        }

        // the plugin slug is the base directory name without the path to the main file
        $plugin_slug = explode( '/', plugin_basename( $plugin_file ) );

        $this->plugin_file = apply_filters( 'wpr_plugin_file', $plugin_file );
        $this->plugin_slug = apply_filters( 'wpr_plugin_slug', $plugin_slug[0] );

        return $plugin_slug;
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
                    'page'            => 'wp-rollback',
                    'type'            => 'plugin',
                    'plugin_file'     => $plugin_file,
                    'current_version' => urlencode( $plugin_data['Version'] ),
                    'rollback_name'   => urlencode( $plugin_data['Name'] ),
                    'plugin_slug'     => urlencode( $plugin_data['slug'] ),
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

        return apply_filters( 'wpr_pluginActionLinks', $actions );
    }

    /**
     * Is WordPress Theme?
     *
     * Queries the WordPress.org API via theme's slug to see if this theme is on WordPress.
     *
     * @return bool
     */
    public function isWordPressTheme(): bool {
        // Multisite check.
        if ( is_multisite() && ( ! is_network_admin() && ! is_main_site() ) ) {
            return false;
        }

        $url    = add_query_arg(
            'request[slug]',
            $_POST['theme'],
            'https://api.wordpress.org/themes/info/1.1/?action=theme_information'
        );
        $wp_api = wp_remote_get( $url );

        if ( ! is_wp_error( $wp_api ) ) {
            if ( isset( $wp_api['body'] ) && strlen( $wp_api['body'] ) > 0 && $wp_api['body'] !== 'false' ) {
                echo 'wp';
            } else {
                echo 'non-wp';
            }
        } else {
            echo 'error';
        }

        // Die is required to terminate immediately and return a proper response.
        wp_die();
    }

    /**
     * Plugin Row Meta.
     *
     * @param $plugin_meta
     * @param $plugin_file
     * @param $plugin_data
     * @param $status
     *
     * @return mixed
     */
    public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
        return $plugin_meta;
    }


    /**
     * Updates theme list.
     *
     * @return bool
     */
    public function themeUpdatesList(): bool {
        include ABSPATH . WPINC . '/version.php'; // include an unmodified $wp_version

        // Bounce out if improperly called.
        if ( defined( 'WP_INSTALLING' ) || ! is_admin() ) {
            return false;
        }

        $expiration       = 12 * HOUR_IN_SECONDS;
        $installed_themes = wp_get_themes();

        $last_update = get_site_transient( 'update_themes' );
        if ( ! is_object( $last_update ) ) {
            set_site_transient( 'rollback_themes', time(), $expiration );
        }

        $themes = $checked = $request = [];

        // Put slug of current theme into request.
        $request['active'] = get_option( 'stylesheet' );

        foreach ( $installed_themes as $theme ) {
            $checked[ $theme->get_stylesheet() ] = $theme->get( 'Version' );

            $themes[ $theme->get_stylesheet() ] = [
                'Name'       => $theme->get( 'Name' ),
                'Title'      => $theme->get( 'Name' ),
                'Version'    => '0.0.0.0.0.0',
                'Author'     => $theme->get( 'Author' ),
                'Author URI' => $theme->get( 'AuthorURI' ),
                'Template'   => $theme->get_template(),
                'Stylesheet' => $theme->get_stylesheet(),
            ];
        }

        $request['themes'] = $themes;

        $timeout = 3 + (int) ( count( $themes ) / 10 );

        global $wp_version;

        $options = [
            'timeout'    => $timeout,
            'body'       => [
                'themes' => json_encode( $request ),
            ],
            'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
        ];

        $url = $http_url = 'http://api.wordpress.org/themes/update-check/1.1/';
        if ( $ssl = wp_http_supports( [ 'ssl' ] ) ) {
            $url = set_url_scheme( $url, 'https' );
        }

        $raw_response = wp_remote_post( $url, $options );
        if ( $ssl && is_wp_error( $raw_response ) ) {
            trigger_error(
                __(
                    'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.',
                    'wp-rollback'
                ) . ' ' . __(
                    '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)',
                    'wp-rollback'
                ),
                headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE
            );
            $raw_response = wp_remote_post( $http_url, $options );
        }

        set_site_transient( 'rollback_themes', time(), $expiration );

        if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
            return false;
        }

        $new_update               = new stdClass();
        $new_update->last_checked = time();
        $new_update->checked      = $checked;

        $response = json_decode( wp_remote_retrieve_body( $raw_response ), true );

        if ( is_array( $response ) && isset( $response['themes'] ) ) {
            $new_update->response = $response['themes'];
        }

        set_site_transient( 'rollback_themes', $new_update );

        return true;
    }


    /**
     * Prepare Themes JS.
     *
     * @param $prepared_themes
     *
     * @return array
     */
    public function prepareThemesJs( $prepared_themes ): array {
        $themes    = [];
        $rollbacks = [];
        $wp_themes = get_site_transient( 'rollback_themes' );

        // Double-check our transient is present.
        if ( empty( $wp_themes ) || ! is_object( $wp_themes ) ) {
            $this->themeUpdatesList();
            $wp_themes = get_site_transient( 'rollback_themes' );
        }

        // Set $rollback response variable for loop ahead.
        if ( is_object( $wp_themes ) ) {
            $rollbacks = $wp_themes->response;
        }

        // Loop through themes and provide a 'hasRollback' boolean key for JS.
        foreach ( $prepared_themes as $key => $value ) {
            $themes[ $key ]                = $value;
            $themes[ $key ]['hasRollback'] = isset( $rollbacks[ $key ] );
        }

        return $themes;
    }

}
