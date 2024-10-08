<?php

/**
 * This is used to add plugin row meta-links.
 *
 * @package WpRollback\PluginSetup
 * @unreleased
 */

declare(strict_types=1);

namespace WpRollback\PluginSetup;

use WpRollback\Core\Constants;

/**
 * @unreleased
 */
class PluginMeta
{
    /**
     * Adds a link in the wider column. Typically used to add docs and support plugin row meta-links.
     *
     * @unreleased
     */
    public static function addPluginRowMeta(array $plugin_meta, string $plugin_file): array
    {
        if (Constants::$PLUGIN_ROOT_FILE_RELATIVE_PATH !== $plugin_file) {
            return $plugin_meta;
        }

        $new_meta_links = [
            sprintf(
                '<a href="%1$s" target="_blank">%2$s</a>',
                esc_url(
                    add_query_arg(
                        [
                            'utm_source'   => 'plugins-page',
                            'utm_medium'   => 'plugin-row',
                            'utm_campaign' => 'admin',
                        ],
                        'https://links.stellarwp.com/stellarpay/documentation/'
                    )
                ),
                __('Documentation', 'stellarpay')
            ),
            sprintf(
                '<a href="%1$s" target="_blank">%2$s</a>',
                esc_url(
                    add_query_arg(
                        [
                            'utm_source'   => 'plugins-page',
                            'utm_medium'   => 'plugin-row',
                            'utm_campaign' => 'admin',
                        ],
                        'https://links.stellarwp.com/stellarpay/support/'
                    )
                ),
                __('Support', 'stellarpay')
            ),
        ];

        return array_merge($plugin_meta, $new_meta_links);
    }

    /**
     * Adds a settings link to the plugin row meta.
     *
     * @unreleased
     */
    public static function addPluginSettingsMeta($actions): array
    {
        $new_actions = [
            'settings' => sprintf(
                '<a href="%1$s">%2$s</a>',
                admin_url('admin.php?page=stellarpay#/settings'),
                __('Settings', 'stellarpay')
            ),
        ];

        return array_merge($new_actions, $actions);
    }
}
