<?php
/**
 * Plugin Name: WP Rollback
 * Plugin URI: https://impress.org/
 * Description: Rollback (or forward) any WordPress.org plugin or theme like a boss.
 * Author: Impress.org
 * Author URI: https://impress.org/
 * Version: 1.7.0
 * Text Domain: wp-rollback
 * Domain Path: /languages
 *
 * WP Rollback is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Rollback is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Rollback. If not, see <http://www.gnu.org/licenses/>.
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main WP Rollback Class
 *
 * @since 1.0
 */
if ( ! class_exists( 'WP_Rollback' ) ) :

	/**
	 * Class WP_Rollback
	 */
	final class WP_Rollback {

		/**
		 * WP_Rollback instance
		 *
		 * @var WP_Rollback The one and only
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * WP_Rollback Settings Object
		 *
		 * @var object
		 * @since 1.0
		 */
		public $wpr_settings;

		/**
		 * Plugins API url.
		 *
		 * @var string
		 */
		public $plugins_api = 'https://api.wordpress.org/plugins';

		/**
		 * Themes repo url.
		 *
		 * @var string
		 */
		public $themes_repo = 'http://themes.svn.wordpress.org';

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
		public $versions = array();

		/**
		 * Current version.
		 *
		 * @var string
		 */
		public $current_version;


		/**
		 * Main WP_Rollback Instance
		 *
		 * Insures that only one instance of WP Rollback exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since     1.0
		 * @static
		 * @staticvar array $instance
		 * @uses      WP_Rollback::setup_constants() Setup the constants needed
		 * @uses      WP_Rollback::includes() Include the required files
		 * @uses      WP_Rollback::load_textdomain() load the language files
		 * @see       WP_Rollback()
		 * @return    WP_Rollback
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Rollback ) && is_admin() ) {
				self::$instance = new WP_Rollback();
				self::$instance->setup_constants();

				// Only setup plugin rollback on specific page
				if ( isset( $_GET['plugin_file'] ) && $_GET['page'] == 'wp-rollback' ) {
					self::$instance->setup_plugin_vars();
				}

				self::$instance->hooks();
				self::$instance->includes();

			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since  1.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-rollback' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since  1.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-rollback' ), '1.0' );
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since  1.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin Folder Path
			if ( ! defined( 'WP_ROLLBACK_PLUGIN_DIR' ) ) {
				define( 'WP_ROLLBACK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'WP_ROLLBACK_PLUGIN_URL' ) ) {
				define( 'WP_ROLLBACK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'WP_ROLLBACK_PLUGIN_FILE' ) ) {
				define( 'WP_ROLLBACK_PLUGIN_FILE', __FILE__ );
			}

		}

		/**
		 * Setup Variables
		 *
		 * @access     private
		 * @description:
		 */
		private function setup_plugin_vars() {
			$this->set_plugin_slug();

			$svn_tags = $this->get_svn_tags( 'plugin', $this->plugin_slug );
			$this->set_svn_versions_data( $svn_tags );
		}

		/**
		 * Plugin hooks.
		 *
		 * @access private
		 * @since  1.5
		 * @return void
		 */
		private function hooks() {
			// i18n
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
			// Admin
			add_action( 'admin_enqueue_scripts', array( self::$instance, 'scripts' ) );
			add_action( 'admin_menu', array( self::$instance, 'admin_menu' ), 20 );
			add_action(
				'pre_current_active_plugins', array(
					self::$instance,
					'pre_current_active_plugins',
				), 20, 1
			);
			add_action( 'wp_ajax_is_wordpress_theme', array( self::$instance, 'is_wordpress_theme' ) );
			add_action( 'set_site_transient_update_themes', array( self::$instance, 'wpr_theme_updates_list' ) );

			add_filter( 'wp_prepare_themes_for_js', array( self::$instance, 'wpr_prepare_themes_js' ) );
			add_filter( 'plugin_action_links', array( self::$instance, 'plugin_action_links' ), 20, 4 );

			add_action( 'network_admin_menu', array( self::$instance, 'admin_menu' ), 20 );
			add_filter( 'network_admin_plugin_action_links', array( self::$instance, 'plugin_action_links' ), 20, 4 );

			add_filter( 'theme_action_links', array( self::$instance, 'theme_action_links' ), 20, 4 );
			add_filter( 'wp_ajax_wpr_check_changelog', array( self::$instance, 'get_plugin_changelog' ) );

		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since  1.0
		 * @return void
		 */
		private function includes() {

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
		public function scripts( $hook ) {

			if ( 'themes.php' === $hook ) {
				wp_enqueue_script( 'wp_rollback_themes_script', plugin_dir_url( __FILE__ ) . 'assets/js/themes-wp-rollback.js', array( 'jquery' ), false, true );
				// Localize for i18n
				wp_localize_script(
					'wp_rollback_themes_script', 'wpr_vars', array(
						'ajaxurl'               => admin_url(),
						'ajax_loader'           => admin_url( 'images/spinner.gif' ),
						'nonce'                 => wp_create_nonce( 'wpr_rollback_nonce' ),
						'text_rollback_label'   => __( 'Rollback', 'wp-rollback' ),
						'text_not_rollbackable' => __( 'No Rollback Available: This is a non-WordPress.org theme.', 'wp-rollback' ),
						'text_loading_rollback' => __( 'Loading...', 'wp-rollback' ),
					)
				);
			}

			if ( ! in_array( $hook, array( 'index_page_wp-rollback', 'dashboard_page_wp-rollback' ) ) ) {
				return;
			}

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			// CSS
			wp_register_style( 'wp_rollback_css', plugin_dir_url( __FILE__ ) . 'assets/css/wp-rollback.css' );
			wp_enqueue_style( 'wp_rollback_css' );

			wp_register_style( 'wp_rollback_modal_css', plugin_dir_url( __FILE__ ) . 'assets/css/magnific-popup.css' );
			wp_enqueue_style( 'wp_rollback_modal_css' );

			// JS
			wp_register_script( 'wp_rollback_modal', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.magnific-popup' . $suffix . '.js', array( 'jquery' ) );
			wp_enqueue_script( 'wp_rollback_modal' );

			wp_register_script( 'wp_rollback_script', plugin_dir_url( __FILE__ ) . 'assets/js/wp-rollback.js', array( 'jquery' ) );
			wp_enqueue_script( 'wp_rollback_script' );

			wp_enqueue_script( 'updates' );

			// Localize for i18n.
			wp_localize_script(
				'wp_rollback_script', 'wpr_vars', array(
					'ajaxurl'                 => admin_url(),
					'text_no_changelog_found' => isset( $_GET['plugin_slug'] ) ? sprintf( __( 'Sorry, we couldn\'t find a changelog entry found for this version. Try checking the <a href="%s" target="_blank">developer log</a> on WP.org.', 'wp-rollback' ), 'https://wordpress.org/plugins/' . $_GET['plugin_slug'] . '/#developers' ) : '',
					'version_missing'         => __( 'Please select a version number to perform a rollback.', 'wp-rollback' ),
				)
			);

		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since  1.0
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory
			$wpr_lang_dir = dirname( plugin_basename( WP_ROLLBACK_PLUGIN_FILE ) ) . '/languages/';
			$wpr_lang_dir = apply_filters( 'wpr_languages_directory', $wpr_lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-rollback' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'wp-rollback', $locale );

			// Setup paths to current locale file
			$mofile_local  = $wpr_lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/wp-rollback/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/wpr folder
				load_textdomain( 'wp-rollback', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/wpr/languages/ folder
				load_textdomain( 'wp-rollback', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'wp-rollback', false, $wpr_lang_dir );
			}
		}

		/**
		 * HTML
		 */
		public function html() {

			// Permissions check
			if ( ! current_user_can( 'update_plugins' ) ) {
				wp_die( __( 'You do not have sufficient permissions to perform rollbacks for this site.', 'wp-rollback' ) );
			}

			// Get the necessary class
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

			$defaults = apply_filters(
				'wpr_rollback_html_args', array(
					'page'           => 'wp-rollback',
					'plugin_file'    => '',
					'action'         => '',
					'plugin_version' => '',
					'plugin'         => '',
				)
			);

			$args = wp_parse_args( $_GET, $defaults );

			if ( ! empty( $args['plugin_version'] ) ) {
				// Plugin: rolling back.
				check_admin_referer( 'wpr_rollback_nonce' );
				include WP_ROLLBACK_PLUGIN_DIR . '/includes/class-rollback-plugin-upgrader.php';
				include WP_ROLLBACK_PLUGIN_DIR . '/includes/rollback-action.php';
			} elseif ( ! empty( $args['theme_version'] ) ) {
				// Theme: rolling back.
				check_admin_referer( 'wpr_rollback_nonce' );
				include WP_ROLLBACK_PLUGIN_DIR . '/includes/class-rollback-theme-upgrader.php';
				include WP_ROLLBACK_PLUGIN_DIR . '/includes/rollback-action.php';
			} else {
				// This is the menu.
				check_admin_referer( 'wpr_rollback_nonce' );
				include WP_ROLLBACK_PLUGIN_DIR . '/includes/rollback-menu.php';

			}

		}


		/**
		 * Get Plugin Changelog
		 *
		 * Uses WP.org API to get a plugin's
		 *
		 * @return null|string
		 */
		public function get_plugin_changelog() {

			// Need slug to continue.
			if ( ! isset( $_POST['slug'] ) || empty( $_POST['slug'] ) ) {
				return false;
			}

			$url = 'https://api.wordpress.org/plugins/info/1.0/' . $_POST['slug'];

			$response = wp_remote_get( $url );

			// Do we have an error?
			if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
				return null;
			}

			$response = maybe_unserialize( wp_remote_retrieve_body( $response ) );

			// Nope: Return that bad boy
			echo $response->sections['changelog'];

			wp_die();

		}


		/**
		 * Get Subversion Tags
		 *
		 * cURLs wp.org repo to get the proper tags
		 *
		 * @param $type
		 * @param $slug
		 *
		 * @return null|string
		 */
		public function get_svn_tags( $type, $slug ) {

			if ( 'plugin' === $type ) {
				$url = $this->plugins_api . '/info/1.0/' . $this->plugin_slug . '.json';
			} elseif ( 'theme' === $type ) {
				$url = $this->themes_repo . '/' . $slug;
			}

			$response = wp_remote_get( $url );

			// Do we have an error?
			if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
				return null;
			}

			// Nope: Return that bad boy
			return wp_remote_retrieve_body( $response );

		}

		/**
		 * Set SVN Version Data
		 *
		 * @param $html
		 *
		 * @return array|bool
		 */
		public function set_svn_versions_data( $html ) {

			if ( ! $html ) {
				return false;
			}

			if ( ( $json = json_decode( $html ) ) && ( $html != $json ) ) {
				$versions = array_keys( (array) $json->versions );
			} else {
				$DOM = new DOMDocument();
				$DOM->loadHTML( $html );

				$versions = array();

				$items = $DOM->getElementsByTagName( 'a' );

				foreach ( $items as $item ) {
					$href = str_replace( '/', '', $item->getAttribute( 'href' ) ); // Remove trailing slash

					if ( strpos( $href, 'http' ) === false && '..' !== $href ) {
						$versions[] = $href;
					}
				}
			}

			$this->versions = array_reverse( $versions );

			return $versions;
		}

		/**
		 * Versions Select
		 *
		 * Outputs the version radio buttons to select a rollback; types = 'plugin' or 'theme'.
		 *
		 * @param $type
		 *
		 * @return bool|string
		 */
		public function versions_select( $type ) {

			if ( empty( $this->versions ) ) {

				$versions_html = '<div class="wpr-error"><p>' . sprintf( __( 'It appears there are no version to select. This is likely due to the %s author not using tags for their versions and only committing new releases to the repository trunk.', 'wp-rollback' ), $type ) . '</p></div>';

				return apply_filters( 'versions_failure_html', $versions_html );

			}

			$versions_html = '<ul class="wpr-version-list">';

			usort( $this->versions, 'version_compare' );

			$this->versions = array_reverse( $this->versions );

			// Loop through versions and output in a radio list.
			foreach ( $this->versions as $version ) {

				$versions_html .= '<li class="wpr-version-li">';
				$versions_html .= '<label><input type="radio" value="' . esc_attr( $version ) . '" name="' . $type . '_version">' . $version;

				// Is this the current version?
				if ( $version === $this->current_version ) {
					$versions_html .= '<span class="current-version">' . __( 'Installed Version', 'wp-rollback' ) . '</span>';
				}

				$versions_html .= '</label>';

				// View changelog link.
				if ( 'plugin' === $type ) {
					$versions_html .= ' <a href="#" class="wpr-changelog-link" data-version="' . $version . '">' . __( 'View Changelog', 'wp-rollback' ) . '</a>';
				}

				$versions_html .= '</li>';

			}

			$versions_html .= '</ul>';

			return apply_filters( 'versions_select_html', $versions_html );
		}

		/**
		 * Set Plugin Slug
		 *
		 * @return array|bool
		 */
		private function set_plugin_slug() {

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
		 * Admin Menu
		 *
		 * @description: Adds a 'hidden' menu item that is activated when the user elects to rollback
		 */
		public function admin_menu() {

			// Only show menu item when necessary (user is interacting with plugin, ie rolling back something)
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'wp-rollback' ) {

				// Add it in a native WP way, like WP updates do... (a dashboard page)
				add_dashboard_page(
					__( 'Rollback', 'wp-rollback' ), __( 'Rollback', 'wp-rollback' ), 'update_plugins', 'wp-rollback', array(
						self::$instance,
						'html',
					)
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
		public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {

			// Filter for other devs.
			$plugin_data = apply_filters( 'wpr_plugin_data', $plugin_data );

			// If plugin is missing package data do not output Rollback option.
			if ( ! isset( $plugin_data['package'] ) || strpos( $plugin_data['package'], 'https://downloads.wordpress.org' ) === false ) {
				return $actions;
			}

			// Multisite check.
			if ( is_multisite() && ( ! is_network_admin() && ! is_main_site() ) ) {
				return $actions;
			}

			// Must have version.
			if ( ! isset( $plugin_data['Version'] ) ) {
				return $actions;
			}

			// Base rollback URL
			$rollback_url = 'index.php?page=wp-rollback&type=plugin&plugin_file=' . $plugin_file;

			$rollback_url = add_query_arg(
				apply_filters(
					'wpr_plugin_query_args', array(
						'current_version' => urlencode( $plugin_data['Version'] ),
						'rollback_name'   => urlencode( $plugin_data['Name'] ),
						'plugin_slug'     => urlencode( $plugin_data['slug'] ),
						'_wpnonce'        => wp_create_nonce( 'wpr_rollback_nonce' ),
					)
				), $rollback_url
			);

			// Final Output
			$actions['rollback'] = apply_filters( 'wpr_plugin_markup', '<a href="' . esc_url( $rollback_url ) . '">' . __( 'Rollback', 'wp-rollback' ) . '</a>' );

			return apply_filters( 'wpr_plugin_action_links', $actions );

		}


		/**
		 * Theme Action Links
		 *
		 * Adds a "rollback" link into the plugins listing page w/ appropriate query strings
		 *
		 * @param $actions
		 * @param $theme WP_Theme
		 * @param $context
		 *
		 * @return array $actions
		 */
		public function theme_action_links( $actions, $theme, $context ) {

			$rollback_themes = get_site_transient( 'rollback_themes' );
			if ( ! is_object( $rollback_themes ) ) {
				$this->wpr_theme_updates_list();
				$rollback_themes = get_site_transient( 'rollback_themes' );
			}

			$theme_slug = isset( $theme->template ) ? $theme->template : '';

			// Only WP.org themes.
			if ( empty( $theme_slug ) || ! array_key_exists( $theme_slug, $rollback_themes->response ) ) {
				return $actions;
			}

			$theme_file = isset( $rollback_themes->response[ $theme_slug ]['package'] ) ? $rollback_themes->response[ $theme_slug ]['package'] : '';

			// Base rollback URL.
			$rollback_url = 'index.php?page=wp-rollback&type=theme&theme_file=' . $theme_file;

			// Add in the current version for later reference.
			if ( ! $theme->get( 'Version' ) ) {
				return $actions;
			}

			$rollback_url = add_query_arg(
				apply_filters(
					'wpr_theme_query_args', array(
						'theme_file'      => urlencode( $theme_slug ),
						'current_version' => urlencode( $theme->get( 'Version' ) ),
						'rollback_name'   => urlencode( $theme->get( 'Name' ) ),
						'_wpnonce'        => wp_create_nonce( 'wpr_rollback_nonce' ),
					)
				), $rollback_url
			);

			// Final Output
			$actions['rollback'] = apply_filters( 'wpr_theme_markup', '<a href="' . esc_url( $rollback_url ) . '">' . __( 'Rollback', 'wp-rollback' ) . '</a>' );

			return apply_filters( 'wpr_theme_action_links', $actions );

		}

		/**
		 * Is WordPress Theme?
		 *
		 * Queries the WordPress.org API via theme's slug to see if this theme is on WordPress.
		 *
		 * @return bool
		 */
		public function is_wordpress_theme() {

			// Multisite check.
			if ( is_multisite() && ( ! is_network_admin() && ! is_main_site() ) ) {
				return false;
			}

			$url    = add_query_arg( 'request[slug]', $_POST['theme'], 'https://api.wordpress.org/themes/info/1.1/?action=theme_information' );
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
		function wpr_theme_updates_list() {

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

			$themes = $checked = $request = array();

			// Put slug of current theme into request.
			$request['active'] = get_option( 'stylesheet' );

			foreach ( $installed_themes as $theme ) {
				$checked[ $theme->get_stylesheet() ] = $theme->get( 'Version' );

				$themes[ $theme->get_stylesheet() ] = array(
					'Name'       => $theme->get( 'Name' ),
					'Title'      => $theme->get( 'Name' ),
					'Version'    => '0.0.0.0.0.0',
					'Author'     => $theme->get( 'Author' ),
					'Author URI' => $theme->get( 'AuthorURI' ),
					'Template'   => $theme->get_template(),
					'Stylesheet' => $theme->get_stylesheet(),
				);
			}

			$request['themes'] = $themes;

			$timeout = 3 + (int) ( count( $themes ) / 10 );

			global $wp_version;

			$options = array(
				'timeout'    => $timeout,
				'body'       => array(
					'themes' => json_encode( $request ),
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
			);

			$url = $http_url = 'http://api.wordpress.org/themes/update-check/1.1/';
			if ( $ssl = wp_http_supports( array( 'ssl' ) ) ) {
				$url = set_url_scheme( $url, 'https' );
			}

			$raw_response = wp_remote_post( $url, $options );
			if ( $ssl && is_wp_error( $raw_response ) ) {
				trigger_error( __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.', 'wp-rollback' ) . ' ' . __( '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)', 'wp-rollback' ), headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE );
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
		function wpr_prepare_themes_js( $prepared_themes ) {
			$themes    = array();
			$rollbacks = array();
			$wp_themes = get_site_transient( 'rollback_themes' );

			// Double check our transient is present.
			if ( empty( $wp_themes ) || ! is_object( $wp_themes ) ) {
				$this->wpr_theme_updates_list();
				$wp_themes = get_site_transient( 'rollback_themes' );
			}

			// Set $rollback response variable for loop ahead.
			if ( is_object( $wp_themes ) ) {
				$rollbacks = $wp_themes->response;
			}

			// Loop through themes and provide a 'hasRollback' boolean key for JS.
			foreach ( $prepared_themes as $key => $value ) {
				$themes[ $key ]                = $prepared_themes[ $key ];
				$themes[ $key ]['hasRollback'] = isset( $rollbacks[ $key ] );
			}

			return $themes;
		}

	}

endif; // End if class_exists check


/**
 * The main function responsible for returning the one true WP Rollback
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $wp_rollback = WP_Rollback(); ?>
 *
 * @since 1.0
 * @return WP_Rollback object  The one true WP Rollback Instance
 */
function WP_Rollback() {
	return WP_Rollback::instance();
}

// Get WP Rollback Running
WP_Rollback();
