<?php

/**
 * WP Rollback Free Uninstall
 *
 * Fired when the free plugin is deleted via WP Admin.
 *
 * @package WpRollback\Free
 */

declare(strict_types=1);

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Free and Pro share the backup directory hash and the migrations log.
// Upgrading deactivates Free and people then delete it, so only remove shared
// data when the other plugin isn't installed; otherwise its archives
// (uploads/wp-rollback-{hash}) would be lost.
//
// The rollback_activity_log, rollback_activity_meta and rollback_report_log
// tables are Pro's: Pro creates them and only Pro's uninstall drops them. Free
// leaves them alone even when Pro isn't installed, so history isn't lost when
// Pro was deleted first and is installed again later.
if (!function_exists('get_plugins')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$wprKeepSharedData = false;
foreach (get_plugins() as $wprPluginData) {
    if (($wprPluginData['TextDomain'] ?? '') === 'wp-rollback-pro') { // WP Rollback Pro
        $wprKeepSharedData = true;
        break;
    }
}

if (!$wprKeepSharedData) {
    // Clean up lingering maintenance mode transients and lock file
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_wpr_maintenance_mode%' OR option_name LIKE '_transient_timeout_wpr_maintenance_mode%'");

    $wprMaintenanceFile = ABSPATH . '.wpr-maintenance';
    if (file_exists($wprMaintenanceFile)) {
        @unlink($wprMaintenanceFile);
    }
}

// Clean up stored options
$options = [
    'wp-rollback_previous_version',
    'wp-rollback_current_version',
    // Written by 3.1.x and older on activation and deactivation
    'wp-rollback_just_activated',
    'wp-rollback_plugin_permalinks_flushed',
];

if (!$wprKeepSharedData) {
    $options[] = 'wp_rollback_dir_hash';
    $options[] = 'wp_rollback_migrations_log';
}

foreach ($options as $option) {
    delete_option($option);
}

wp_cache_flush(); // Clear object cache to ensure transients are removed from cache as well.