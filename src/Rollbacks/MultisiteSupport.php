<?php

/**
 * @package WpRollback\Free\Rollbacks
 * @since 1.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks;

use WP_Theme;
use WpRollback\Free\Core\Exceptions\BindingResolutionException;
use WpRollback\Free\Rollbacks\ThemeRollback\Actions\UpdateThemeList;

use WpRollback\SharedCore\Core\Helpers\ContainerHelper;

/**
 * @since 1.0.0
 */
class MultisiteSupport
{
    /**
     * Multisite: Theme Action Links
     *
     * Adds a "rollback" link/button to the theme listing page w/ appropriate query strings for multisite installations.
     *
     * @return array $actions
     * @throws BindingResolutionException
     */
    public function addThemeLink(array $actions, WP_Theme $theme): array
    {
        $rollbackThemes = get_site_transient('rollback_themes');
        if (! is_object($rollbackThemes)) {
            $updateThemeList = ContainerHelper::container()->make(UpdateThemeList::class);
            $updateThemeList();
            $rollbackThemes = get_site_transient('rollback_themes');
        }

        $themeSlug = $theme->get_template();

        // Only WP.org themes.
        if (empty($themeSlug) || ! array_key_exists($themeSlug, $rollbackThemes->response)) {
            return $actions;
        }

        $rollbackURL = "settings.php?page=wp-rollback#/rollback/theme/{$themeSlug}";

        // Final Output
        $actions['rollback'] = apply_filters(
            'wpr_theme_markup',
            sprintf(
                '<a href="%1$s">%2$s</a>',
                esc_url($rollbackURL),
                __('Rollback', 'wp-rollback')
            )
        );

        return apply_filters('wpr_theme_action_links', $actions);
    }
}
