<?php
/**
 * Rollback Action
 */

include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

wp_enqueue_script( 'updates' );

$nonce   = 'upgrade-plugin_' . $this->plugin_slug;
$url     = 'plugins.php?page=wp-rollback&plugin_file=' . $args['plugin_file'] . 'action=upgrade-plugin';
$plugin  = $this->plugin_slug;
$version = $args['plugin_version'];

// Terminology in the class name becomes a little confusing since we're downgrading?
$upgrader = new WP_Rollback_Plugin_Upgrader( new Plugin_Upgrader_Skin( compact( 'title', 'nonce', 'url', 'plugin', 'version' ) ) );

$upgrader->rollback( $this->plugin_file );