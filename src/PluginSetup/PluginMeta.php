<?php

/**
 * This is used to add plugin row meta-links.
 *
 * @package WpRollback\PluginSetup
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\PluginSetup;

use WpRollback\Free\Core\Constants;
use WpRollback\SharedCore\Core\SharedCore;

/**
 * @since 3.0.0
 */
class PluginMeta
{
    /**
     * Adds a link in the wider column. Typically used to add docs and support plugin row meta-links.
     *
     * @since 3.0.0
     */
    public static function addPluginRowMeta(array $pluginMeta, string $pluginFile): array
    {
        $constants = SharedCore::container()->make(Constants::class);
        
        if ($constants->getBasename() !== $pluginFile) {
            return $pluginMeta;
        }

        $newMetaLinks = [
            sprintf(
                '<a href="%1$s" target="_blank">%2$s</a>',
                esc_url(
                    add_query_arg(
                        [
                            'utm_source'   => 'plugins-page',
                            'utm_medium'   => 'plugin-row',
                            'utm_campaign' => 'admin',
                        ],
                        'https://wprollback.com/documentation/'
                    )
                ),
                esc_html__('Documentation', 'wp-rollback')
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
                        'https://wprollback.com/support/'
                    )
                ),
                esc_html__('Support', 'wp-rollback')
            ),
        ];

        return array_merge($pluginMeta, $newMetaLinks);
    }
}
