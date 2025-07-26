<?php

/**
 *
 * @package WpRollback\Free\Rollbacks\ThemeRollback\Actions
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks\ThemeRollback\Actions;

use WpRollback\Free\Core\Exceptions\BindingResolutionException;
use WpRollback\SharedCore\Core\Helpers\ContainerHelper;
use WpRollback\SharedCore\Rollbacks\ThemeRollback\Actions\UpdateThemeList;

/**
 * @since 3.0.0
 */
class PrepareThemeJS
{
    /**
     * @throws BindingResolutionException
     */
    public function __invoke(array $preparedThemes): array
    {
        $themes = [];
        $rollbacks = [];
        $wpThemes = get_site_transient('rollback_themes');

        // Double-check our transient is present.
        if (empty($wpThemes) || ! is_object($wpThemes)) {
            $invokable = ContainerHelper::container()->make(UpdateThemeList::class);
            $invokable();
            $wpThemes = get_site_transient('rollback_themes');
        }

        // Set $rollback response variable for loop ahead.
        if (is_object($wpThemes)) {
            $rollbacks = $wpThemes->response;
        }

        // Loop through themes and provide a 'hasRollback' boolean key for JS.
        foreach ($preparedThemes as $key => $value) {
            $themes[$key] = $value;
            $themes[$key]['hasRollback'] = isset($rollbacks[$key]);
        }

        return $themes;
    }
}
