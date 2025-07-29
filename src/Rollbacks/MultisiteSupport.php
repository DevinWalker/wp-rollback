<?php

/**
 * Multisite support.
 * @package WpRollback\Free\Rollbacks
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks;

use WpRollback\SharedCore\Core\Helpers\ContainerHelper;

/**
 * @since 3.0.0
 */
class MultisiteSupport
{
    /**
     * @since 3.0.0
     */
    public static function syncUpdateRollbackData(): void
    {
        if (is_main_site()) {
            // Sync plugin update data
            $updatePlugins = ContainerHelper::container()->make(PluginRollback::class);
            $updatePlugins();
        }
    }
}
