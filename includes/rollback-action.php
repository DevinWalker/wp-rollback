<?php
/**
 * Rollback Action
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nonce   = 'upgrade-plugin_' . $this->plugin_slug;
$url     = 'index.php?page=wp-rollback&plugin_file=' . $args['plugin_file'] . 'action=upgrade-plugin';
$plugin  = $this->plugin_slug;
$version = $args['plugin_version'];

//Is this a theme rollback?
if ( isset( $_GET['theme_file'] ) ) {
	$nonce   = 'upgrade-theme_' . $_GET['theme_file'];
	$url     = 'index.php?page=wp-rollback&theme_file=' . $args['theme_file'] . 'action=upgrade-theme';
	$version = $_GET['theme_version'];
	$theme   = $_GET['theme_file'];
	// Terminology in the class name becomes a little confusing since we're downgrading?
	$upgrader = new WP_Rollback_Theme_Upgrader( new Theme_Upgrader_Skin( compact( 'title', 'nonce', 'url', 'theme', 'version' ) ) );

	$upgrader->rollback( $_GET['theme_file'] );

} else {
	// Terminology in the class name becomes a little confusing since we're downgrading?
	$upgrader = new WP_Rollback_Plugin_Upgrader( new Plugin_Upgrader_Skin( compact( 'title', 'nonce', 'url', 'plugin', 'version' ) ) );

	$upgrader->rollback( $this->plugin_file );
}

