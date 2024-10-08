<?php
/**
 * Plugin Name: WP Rollback
 * Plugin URI: https://wprollback.com/
 * Description: Rollback (or forward) any WordPress.org plugin, theme or block like a boss.
 * Author: WP Rollback
 * Author URI: https://wprollback.com/
 * Version: 2.0.7
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
use WpRollback\PluginSetup\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Start WP-Rollback
 *
 * The main function responsible for returning the one true WpRollback instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $wpRollback = wpRollback(); ?>
 *
 * @unreleased
 */
function wpRollback(): Plugin
{
    static $instance = null;

    if (null === $instance) {
        $instance = new Plugin();
    }

    return $instance;
}

// StellarPay Autoloader.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/vendor-prefixed/autoload.php';

// Boot the plugin.
wpRollback()->boot();
