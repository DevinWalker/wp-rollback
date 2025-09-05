<?php

/**
 * Plugin Name: WP Rollback
 * Plugin URI: https://wprollback.com/
 * Description: Rollback (or forward) any WordPress.org plugin, theme or block like a boss.
 * Author: WP Rollback
 * Author URI: https://wprollback.com/
 * Version: 3.0.6
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

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Load Composer autoloaders
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/vendor-prefixed/autoload.php';

// Initialize SharedCore - This is lightweight and just marks it as initialized
WpRollback\SharedCore\Core\SharedCore::initialize();

// Initialize the plugin
add_action('plugins_loaded', function () {
    $pluginSetup = new WpRollback\Free\PluginSetup\PluginSetup();
    $pluginSetup->boot();
}, 5);