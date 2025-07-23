<?php

/**
 * @package WpRollback\Free\Rollbacks\ThemeRollback\Views
 * @since 1.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks\ThemeRollback\Views;

use WpRollback\SharedCore\Core\Assets\AssetsManager;
use WpRollback\SharedCore\Core\SharedCore;

/**
 * Handles theme rollback button functionality and assets.
 *
 * @since 1.0.0
 */
class ThemeRollbackButton
{
    /**
     * Register and enqueue theme rollback assets.
     *
     * @since 1.0.0
     */
    public function __invoke(): void
    {
        global $pagenow;

        if ('themes.php' !== $pagenow) {
            return;
        }

        $assetsManager = SharedCore::container()->make(AssetsManager::class);
        $assetsManager->enqueueScript('themesAdmin', [], false);
    }
} 