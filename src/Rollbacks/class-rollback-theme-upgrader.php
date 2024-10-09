<?php
/**
 * WP Rollback Theme Upgrader
 *
 * Class that extends the WP Core Theme_Upgrader found in core to do rollbacks.
 *
 * @copyright  : http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      : 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WP_Rollback_Theme_Upgrader
 */
class WP_Rollback_Theme_Upgrader extends Theme_Upgrader {

	/**
	 * Theme rollback.
	 *
	 * @param       $theme
	 * @param array $args
	 *
	 * @return array|bool|\WP_Error
	 */
	public function rollback( $theme, $args = array() ) {

		$defaults    = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->upgrade_strings();

		if ( 0 ) {
			$this->skin->before();
			$this->skin->set_result( false );
			$this->skin->error( 'up_to_date' );
			$this->skin->after();

			return false;
		}

		$theme_slug = $this->skin->theme;

		$theme_version = $this->skin->options['version'];

		$download_endpoint = 'https://downloads.wordpress.org/theme/';

		$url = $download_endpoint . $theme_slug . '.' . $theme_version . '.zip';

		add_filter( 'upgrader_pre_install', array( $this, 'current_before' ), 10, 2 );
		add_filter( 'upgrader_post_install', array( $this, 'current_after' ), 10, 2 );
		add_filter( 'upgrader_clear_destination', array( $this, 'delete_old_theme' ), 10, 4 );

		// 'source_selection' => array($this, 'source_selection'), //there's a trac ticket to move up the directory for zip's which are made a bit differently, useful for non-.org plugins.
		$this->run( array(
			'package'           => $url,
			'destination'       => get_theme_root(),
			'clear_destination' => true,
			'clear_working'     => true,
			'hook_extra'        => array(
				'theme'  => $theme,
				'type'   => 'theme',
				'action' => 'update',
			),
		) );

		remove_filter( 'upgrader_pre_install', array( $this, 'current_before' ) );
		remove_filter( 'upgrader_post_install', array( $this, 'current_after' ) );
		remove_filter( 'upgrader_clear_destination', array( $this, 'delete_old_theme' ) );

		if ( ! $this->result || is_wp_error( $this->result ) ) {
			return $this->result;
		}

		// Force refresh of theme update information.
		wp_clean_themes_cache( $parsed_args['clear_update_cache'] );

		return true;

	}

}
