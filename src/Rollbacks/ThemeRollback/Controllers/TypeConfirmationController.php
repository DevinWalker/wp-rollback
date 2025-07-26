<?php

/**
 * This controller uses to check whether theme is available on WP.ORG
 *
 * @package WpRollback\Free\Rollbacks\ThemeRollback\Controllers
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks\ThemeRollback\Controllers;

use WpRollback\Free\Core\Exceptions\Primitives\Exception;
use WpRollback\SharedCore\Core\Contracts\Controller;

use function WpRollback\SharedCore\Core\Helpers\remote_get;

/**
 * @since 3.0.0
 */
class TypeConfirmationController extends Controller
{
    /**
     * @since 3.0.0
     * @throws Exception
     */
    public function __invoke(): void
    {
        // Multisite check.
        if (is_multisite() && (! is_network_admin() && ! is_main_site())) {
            return;
        }

        $url = add_query_arg(
            'request[slug]',
            $this->request->post('theme'),
            'https://api.wordpress.org/themes/info/1.1/?action=theme_information'
        );

        $wpApi = remote_get($url);
        if (! is_wp_error($wpApi)) {
            if (
                isset($wpApi['body'])
                && strlen($wpApi['body']) > 0
                && 'false' !== $wpApi['body']
            ) {
                echo 'wp';
            } else {
                echo 'non-wp';
            }
        } else {
            echo 'error';
        }

        // Die is required to terminate immediately and return a proper response.
        wp_die();
    }
}
