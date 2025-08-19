<?php

/**
 * @package WpRollback\Free\Rollbacks\ThemeRollback\Views
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks\ThemeRollback\Views;

use WpRollback\SharedCore\Core\Assets\AssetsManager;
use WpRollback\SharedCore\Core\SharedCore;
use WpRollback\SharedCore\Rollbacks\Traits\PluginHelpers;

/**
 * Handles theme rollback button functionality and assets.
 *
 * @since 3.0.0
 */
class ThemeRollbackButton
{
    use PluginHelpers;
    /**
     * Register and enqueue theme rollback assets.
     *
     * @since 3.0.0
     */
    public function __invoke(): void
    {
        global $pagenow;

        if ('themes.php' !== $pagenow) {
            return;
        }

        // Don't enqueue on network admin - themes use table view with action links there
        if (is_network_admin()) {
            return;
        }

        // Don't enqueue on individual sites if plugin is network activated
        if ($this->isNetworkActivated()) {
            return;
        }

        $assetsManager = SharedCore::container()->make(AssetsManager::class);
        $assetsManager->enqueueScript('themesAdmin', [], false);
    }
} 