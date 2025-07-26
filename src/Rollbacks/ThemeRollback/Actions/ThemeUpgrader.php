<?php

/**
 * @package WpRollback\Free\Rollbacks\ThemeRollback
 * @since 3.0.0
 */

declare(strict_types=1);

// Exit if accessed directly.
namespace WpRollback\Free\Rollbacks\ThemeRollback\Actions;

use Theme_Upgrader;
use WP_Error;

/**
 * @since 3.0.0
 */
class ThemeUpgrader extends Theme_Upgrader
{
    /**
     * Theme rollback.
     *
     * @return array|bool|WP_Error
     */
    public function rollback($theme, $args = [])
    {
        $defaults = [
            'clear_update_cache' => true,
        ];
        $parsedArgs = wp_parse_args($args, $defaults);

        $this->init();
        $this->upgrade_strings();

        $themeSlug = $this->skin->theme; // @phpstan-ignore-line
        $themeVersion = $this->skin->options['version'];

        $downloadEndpoint = 'https://downloads.wordpress.org/theme/';

        $url = $downloadEndpoint . $themeSlug . '.' . $themeVersion . '.zip';

        add_filter('upgrader_pre_install', [$this, 'current_before'], 10, 2);
        add_filter('upgrader_post_install', [$this, 'current_after'], 10, 2);
        add_filter('upgrader_clear_destination', [$this, 'delete_old_theme'], 10, 4);

        // 'source_selection' => array($this, 'source_selection'),
        // there's a trac ticket to move up the directory for zip's that is made a bit differently, useful for non-.org plugins.
        $this->run([
            'package' => $url,
            'destination' => get_theme_root(),
            'clear_destination' => true,
            'clear_working' => true,
            'hook_extra' => [
                'theme' => $theme,
                'type' => 'theme',
                'action' => 'update',
            ],
        ]);

        remove_filter('upgrader_pre_install', [$this, 'current_before']);
        remove_filter('upgrader_post_install', [$this, 'current_after']);
        remove_filter('upgrader_clear_destination', [$this, 'delete_old_theme']);

        if (! $this->result || is_wp_error($this->result)) {
            return $this->result;
        }

        // Force refresh of theme update information.
        wp_clean_themes_cache($parsedArgs['clear_update_cache']);

        return true;
    }
}
