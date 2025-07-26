<?php

/**
 * Language setup.
 *
 * This class is used to manage the application language.
 *
 * @package WpRollback\PluginSetup
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\PluginSetup;

use WpRollback\Free\Core\Constants;
use WpRollback\SharedCore\Core\SharedCore;

/**
 * Class Language.
 *
 * @since 3.0.0
 */
class Language
{
    /**
     * @since 3.0.0
     */
    public static function load(): void
    {
        $constants = SharedCore::container()->make(Constants::class);
        $pluginRelativePath = self::getRelativePath($constants);

        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        // Traditional WordPress plugin locale filter.
        $locale = apply_filters('plugin_locale', $locale, $constants->getTextDomain());

        // Setup paths to current locale file.
        $moFile = sprintf('%1$s-%2$s.mo', $constants->getTextDomain(), $locale);
        $moFileLocal = trailingslashit(WP_PLUGIN_DIR) . $pluginRelativePath . $moFile;
        $moFileGlobal = trailingslashit(WP_LANG_DIR) . 'plugins/' . $moFile;

        unload_textdomain($constants->getTextDomain());
        if (file_exists($moFileGlobal)) {
            // Look in global /wp-content/languages/plugins folder.
            load_textdomain($constants->getTextDomain(), $moFileGlobal);
        } elseif (file_exists($moFileLocal)) {
            // Look in local /wp-content/plugins/wp-rollback/languages/ folder.
            load_textdomain($constants->getTextDomain(), $moFileLocal);
        } else {
            // Load the default language files.
            load_plugin_textdomain($constants->getTextDomain(), false, $pluginRelativePath);
        }
    }

    /**
     * Return the plugin language dir relative path, e.g. "wp-rollback/languages/"
     *
     * @since 3.0.0
     */
    public static function getRelativePath(Constants $constants): string
    {
        $pluginRelativePath = dirname(plugin_basename($constants->getPluginFile())) . '/languages/';
        $pluginRelativePath = ltrim(apply_filters('wprollback_languages_directory', $pluginRelativePath), '/\\');

        return trailingslashit($pluginRelativePath);
    }
}
