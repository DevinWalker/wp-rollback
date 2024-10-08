<?php

/**
 * This class is used to manage the plugin activate, deactivation, and redirection on plugin activation.
 *
 * @package WpRollback\PluginSetup
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\PluginSetup;

use WpRollback\Core\Constants;

/**
* Class PluginManager
 *
 * @unreleased
 */
class PluginManager
{
    /**
     * @unreleased
     */
    public const OPTION_NAME_PLUGIN_PERMALINK_FLUSHED = Constants::PLUGIN_SLUG . '_plugin_permalinks_flushed';


    /**
     * This is used to manage the plugin activation.
     * @unreleased
     */
    public static function activate(): void
    {
        $optionPrefix = Constants::PLUGIN_SLUG;
        $previousVersionOptionName = $optionPrefix . '_previous_version';
        $currentVersionOptionName = $optionPrefix . '_current_version';
        $currentVersion = get_option($currentVersionOptionName, false);

        update_option($previousVersionOptionName, $currentVersion ?: '', false);
        update_option($currentVersionOptionName, Constants::VERSION, false);

        // This option is used to trigger redirect to the getting-started page when the plugin is activated.
        update_option($optionPrefix . '_just_activated', Constants::VERSION, false);


        // This option is used to decide whether flush rewrites permalinks.
        update_option(self::OPTION_NAME_PLUGIN_PERMALINK_FLUSHED, 0);
    }

    /**
     * This is used to manage the plugin deactivation.
     * @unreleased
     */
    public static function deactivate(): void
    {
        // This option is used to decide whether flush rewrites permalinks.
        update_option(self::OPTION_NAME_PLUGIN_PERMALINK_FLUSHED, 0);
    }

}
