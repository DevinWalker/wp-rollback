<?php

/**
 * Multisite support.
 * @package WpRollback\Free\Rollbacks
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks;

use WpRollback\SharedCore\Core\Helpers\ContainerHelper;

/**
 */
class MultisiteSupport
{
    /**
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
