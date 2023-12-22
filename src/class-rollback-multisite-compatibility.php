<?php
/**
 * WP Rollback Multisite Compatibility
 *
 * The sole responsibility of this class is to provide compatibility for WordPress multisite installs.
 *
 * @copyright  : http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      : 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Rollback_Multisite_Compatibility {

    private $wp_rollback;

    public function __construct(WP_Rollback $wp_rollback) {
        $this->wp_rollback = $wp_rollback;
        $this->register_hooks();
    }

    public function register_hooks() {
        add_action('network_admin_menu', [$this->wp_rollback, 'admin_menu'], 20);
        add_filter('network_admin_plugin_action_links', [$this->wp_rollback, 'plugin_action_links'], 20, 4);
        add_filter('theme_action_links', [$this, 'theme_action_links'], 20, 4);
    }


    /**
     * Multisite: Theme Action Links
     *
     * Adds a "rollback" link/button to the theme listing page w/ appropriate query strings for multisite installations.
     *
     * @param $actions
     * @param $theme WP_Theme
     * @param $context
     *
     * @return array $actions
     */
    public function theme_action_links($actions, $theme, $context): array
    {
        $rollback_themes = get_site_transient('rollback_themes');
        if ( ! is_object($rollback_themes)) {
            $this->wp_rollback->wpr_theme_updates_list();
            $rollback_themes = get_site_transient('rollback_themes');
        }

        $theme_slug = $theme->template ?? '';

        // Only WP.org themes.
        if (empty($theme_slug) || ! array_key_exists($theme_slug, $rollback_themes->response)) {
            return $actions;
        }

        $theme_file = $rollback_themes->response[ $theme_slug ]['package'] ?? '';

        // Base rollback URL.
        $rollback_url = 'index.php?page=wp-rollback&type=theme&theme_file=' . $theme_file;

        // Add in the current version for later reference.
        if ( ! $theme->get('Version')) {
            return $actions;
        }

        $rollback_url = add_query_arg(
            apply_filters(
                'wpr_theme_query_args', [
                    'theme_file' => urlencode($theme_slug),
                    'current_version' => urlencode($theme->get('Version')),
                    'rollback_name' => urlencode($theme->get('Name')),
                    '_wpnonce' => wp_create_nonce('wpr_rollback_nonce'),
                ]
            ),
            $rollback_url
        );

        // Final Output
        $actions['rollback'] = apply_filters(
            'wpr_theme_markup',
            '<a href="' . esc_url($rollback_url) . '">' . __('Rollback', 'wp-rollback') . '</a>'
        );

        return apply_filters('wpr_theme_action_links', $actions);
    }

}
