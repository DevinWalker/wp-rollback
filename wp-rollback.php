<?php
/**
 * Plugin Name: WP Rollback
 * Plugin URI: http://wordimpress.com
 * Description:
 * Author: WordImpress
 * Author URI: http://wordimpress.com
 * Version: 1.0
 * Text Domain: wpr
 * Domain Path: languages
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
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP Rollback' ) ) : /**
 * Main WP Rollback Class
 *
 * @since 1.0
 */ {
	final class WP_Rollback {
		/** Singleton *************************************************************/

		/**
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

		public $plugins_repo = 'http://plugins.svn.wordpress.org';

		public $plugin_file;

		public $plugin_slug;

		public $versions;


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
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Rollback ) ) {
				self::$instance = new WP_Rollback;
				self::$instance->setup_constants();
				self::$instance->setup_vars();

				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
				add_action( 'admin_menu', array( self::$instance, 'admin_menu' ), 20 );
				add_action( 'pre_current_active_plugins', array(
					self::$instance,
					'pre_current_active_plugins'
				), 20, 1 );
				add_filter( 'plugin_action_links', array( self::$instance, 'plugin_action_links' ), 20, 4 );

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
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wpr' ), '1.0' );
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
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wpr' ), '1.0' );
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since  1.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version
			if ( ! defined( 'WP_ROLLBACK_VERSION' ) ) {
				define( 'WP_ROLLBACK_VERSION', '1.0' );
			}

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
		private function setup_vars() {
			$this->set_plugin_slug();
			$svn_tags = $this->get_svn_tags();
			$this->set_svn_versions_data( $svn_tags );
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
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wpr' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'wpr', $locale );

			// Setup paths to current locale file
			$mofile_local  = $wpr_lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/wp-rollback/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/wpr folder
				load_textdomain( 'wpr', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/wpr/languages/ folder
				load_textdomain( 'wpr', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'wpr', false, $wpr_lang_dir );
			}
		}

		/**
		 * HTML
		 *
		 * @description: FILL ME IN
		 *
		 */
		public function html() {

			if ( ! current_user_can( 'update_plugins' ) ) {
				wp_die( __( 'You do not have sufficient permissions to update plugins for this site.' ) );
			}

			$defaults = array(
				'page'           => 'wp-rollback',
				'plugin_file'    => '',
				'action'         => '',
				'plugin_version' => '',
				'plugin'         => ''
			);

			$args = wp_parse_args( $_GET, $defaults );

			if ( ! empty( $args['plugin_version'] ) ) {
				include WP_ROLLBACK_PLUGIN_DIR . '/includes/rollback-action.php';
			} else {
				include WP_ROLLBACK_PLUGIN_DIR . '/includes/rollback-menu.php';
			}

		}


		/**
		 * Get Subversion Tags
		 *
		 * @description cURLs wp.org repo to get the proper tags
		 *
		 * @return null|string
		 */
		private function get_svn_tags() {

			$plugin_slug = $this->plugin_slug;

			$response = wp_remote_get( $this->plugins_repo . '/' . $plugin_slug . '/tags/' );

			//Do we have an error?
			if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
				return null;
			}

			//Nope: Return that bad boy
			return wp_remote_retrieve_body( $response );

		}

		/**
		 * Set SVN Version Data
		 *
		 * @description FILL ME IN
		 *
		 * @param $html
		 *
		 * @return array|bool
		 */
		private function set_svn_versions_data( $html ) {

			if ( ! $html ) {
				return false;
			}

			$DOM = new DOMDocument;
			$DOM->loadHTML( $html );

			$versions = array();

			$items = $DOM->getElementsByTagName( 'a' );
			foreach ( $items as $item ) {
				$href = str_replace( '/', '', $item->getAttribute( 'href' ) );
				if ( 0 != intval( $href[0] ) ) {
					$versions[] = $href;
				}
			}
			$this->versions = $versions;

			return $versions;
		}

		/**
		 * Versions Select
		 *
		 * @return bool|string
		 */
		public function versions_select() {

			if ( ! $this->versions ) {
				return false;
			}

			$versions_html = '<select name="plugin_version">';

			$versions = $this->versions;

			foreach ( $versions as $version ) {
				if ( 0 != $version[0] ) {
					$versions_html .= '<option value="' . $version . '">' . $version . '</option>';
				}
			}

			$versions_html .= '</select>';

			return $versions_html;
		}

		/**
		 * Set Plugin Slug
		 *
		 * @return bool
		 */
		private function set_plugin_slug() {


			if ( ! isset( $_GET['plugin_file'] ) ) {
				return false;
			}

			include_once ABSPATH . 'wp-admin/includes/plugin.php';

			$plugin_file = WP_PLUGIN_DIR . '/' . $_GET['plugin_file'];

			$plugin_data = get_plugin_data( $plugin_file );

			$plugin_path_array = array_reverse( array_filter( explode( '/', $plugin_data['PluginURI'] ) ) );

			$plugin_slug = $plugin_path_array[0];

			$this->plugin_file = $plugin_file;
			$this->plugin_slug = $plugin_slug;

			return $plugin_slug;

		}

		/**
		 * Admin Menu
		 *
		 * @description: Adds a 'hidden' menu item that is activated when the user elects to rollback
		 */
		public function admin_menu() {

			//Only show menu item when necessary (user is interacting with plugin, ie rolling back something)
			if(!isset($_GET['page']) && $_GET['page'] !== 'wp-rollback') {
				return;
			}

			//Add it in a native WP way, like WP updates do... (a dashboard page)
			add_dashboard_page( __('Rollback', 'wpr'), __('Rollback', 'wpr'), 'update_plugins', 'wp-rollback', array(
				self::$instance,
				'html'
			) );
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


		public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
			$actions['rollback'] = '<a href="index.php?page=wp-rollback&plugin_file=' . $plugin_file . '">Rollback</a>';

			return $actions;
		}

		public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
			return $plugin_meta;
		}
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
 * @return object The one true WP Rollback Instance
 */
function WP_Rollback() {
	return WP_Rollback::instance();
}

// Get WP Rollback Running
WP_Rollback();