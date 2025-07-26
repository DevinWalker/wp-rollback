<?php

/**
 * @package WpRollback\Free\Rollbacks\ThemeRollback\Actions
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks\ThemeRollback\Actions;

/**
 * @since 3.0.0
 */
class UpdateThemeList
{
    /**
     * @since x.x.
     * @return bool
     */
    public function __invoke(): bool
    {
        // Bounce out if improperly called.
        if (defined('WP_INSTALLING') || ! is_admin()) {
            return false;
        }

        $expiration = 12 * HOUR_IN_SECONDS;
        $installedThemes = wp_get_themes();

        $lastUpdate = get_site_transient('update_themes');
        if (! is_object($lastUpdate)) {
            set_site_transient('rollback_themes', time(), $expiration);
        }

        $themes = $checked = $request = [];

        // Put slug of current theme into request.
        $request['active'] = get_option('stylesheet');

        foreach ($installedThemes as $theme) {
            $checked[$theme->get_stylesheet()] = $theme->get('Version');

            $themes[$theme->get_stylesheet()] = [
                'Name' => $theme->get('Name'),
                'Title' => $theme->get('Name'),
                'Version' => '0.0.0.0.0.0',
                'Author' => $theme->get('Author'),
                'Author URI' => $theme->get('AuthorURI'),
                'Template' => $theme->get_template(),
                'Stylesheet' => $theme->get_stylesheet(),
            ];
        }

        $request['themes'] = $themes;

        $timeout = 3 + (int)(count($themes) / 10);

        $options = [
            'timeout' => $timeout,
            'body' => ['themes' => wp_json_encode($request),],
            'user-agent' => sprintf(
                'WordPress/%1$s; %2$s',
                // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
                get_bloginfo('version'),
                get_bloginfo('url')
            ),
        ];

        $url = $httpUrl = 'http://api.wordpress.org/themes/update-check/1.1/';
        if ($ssl = wp_http_supports(['ssl'])) {
            $url = set_url_scheme($url, 'https');
        }

        $rawResponse = wp_remote_post($url, $options);
        if ($ssl && is_wp_error($rawResponse)) {
            $link = sprintf(
                '<a href="https://wordpress.org/support/">%1$s</a>',
                esc_html__('support forums', 'wp-rollback')
            );

            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
            trigger_error(
                sprintf(
                    '%1$s %2$s',
                    sprintf(
                        /* translators: 1: link to WP.org support*/
                        esc_html__(
                            'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the %1$s.',
                            'wp-rollback'
                        ),
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        $link
                    ),
                    esc_html__(
                        '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)',
                        'wp-rollback'
                    )
                ),
                headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE
            );
            $rawResponse = wp_remote_post($httpUrl, $options);
        }

        set_site_transient('rollback_themes', time(), $expiration);

        if (is_wp_error($rawResponse) || 200 !== wp_remote_retrieve_response_code($rawResponse)) {
            return false;
        }

        $newUpdate = new \stdClass();
        $newUpdate->last_checked = time();
        $newUpdate->checked = $checked;

        $response = json_decode(wp_remote_retrieve_body($rawResponse), true);

        if (is_array($response) && isset($response['themes'])) {
            $newUpdate->response = $response['themes'];
        }

        set_site_transient('rollback_themes', $newUpdate);

        return true;
    }
}
