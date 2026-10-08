<?php

/**
 * Plugin Name: WP Rollback
 * Plugin URI: https://wprollback.com/
 * Description: Roll back (or forward) any WordPress.org plugin, theme or block like a boss.
 * Author: WP Rollback
 * Author URI: https://wprollback.com/
 * Version: 3.2.0
 * Requires at least: 6.5
 * Requires PHP: 7.4
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

declare(strict_types=1);

use WpRollback\Free\PluginSetup\OutdatedProNotice;
use WpRollback\Free\PluginSetup\PluginSetup;
use WpRollback\SharedCore\Core\SharedCore;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Load Composer autoloaders
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/vendor-prefixed/autoload.php';

// Initialize SharedCore - This is lightweight and just marks it as initialized
SharedCore::initialize();

// Lifecycle hooks must be registered here, not in PluginSetup::boot(): the
// activation request loads this file after plugins_loaded has already fired, so
// boot() never runs during activation.
//
// Every use of PluginSetup below is behind the OutdatedProNotice check.
// Next to WP Rollback Pro 1.4.2 or older, loading that class is a fatal error,
// including from these hooks, which would make WP Rollback impossible to deactivate.
register_activation_hook(__FILE__, static function (): void {
    if (!OutdatedProNotice::isSharedCoreOutdated()) {
        PluginSetup::activatePlugin();
    }
});
register_deactivation_hook(__FILE__, static function (): void {
    if (!OutdatedProNotice::isSharedCoreOutdated()) {
        PluginSetup::deactivatePlugin();
    }
});

// Initialize the plugin
add_action('plugins_loaded', function () {
    if (OutdatedProNotice::isSharedCoreOutdated()) {
        (new OutdatedProNotice(plugin_basename(__FILE__)))->register();
        return;
    }

    $pluginSetup = new PluginSetup();
    $pluginSetup->boot();
}, 5);