<?php

/**
 * Plugin Scripts
 *
 * @package WpRollback\Free\PluginSetup
 * @since 1.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\PluginSetup;

use WpRollback\SharedCore\Core\Assets\AssetsManager;
use WpRollback\SharedCore\Core\SharedCore;
use WpRollback\SharedCore\Rollbacks\Registry\RollbackStepRegisterer;

/**
 * Handles script and style registration for the free plugin.
 *
 * @since 1.0.0
 */
class PluginScripts
{
    /**
     * Initialize scripts.
     *
     * @since 1.0.0
     * @return void
     */
    public function initialize(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    /**
     * Enqueue plugin assets.
     *
     * @since 1.0.0
     * @return void
     */
    public function enqueueAssets(): void
    {
        $assetsManager = SharedCore::container()->make(AssetsManager::class);
        
        $assetsManager->enqueueScript('tools', [
            'rollback_nonce' => wp_create_nonce('wpr_rollback_nonce'),
            'restApiNonce' => wp_create_nonce('wp_rest'),
            'adminUrl' => admin_url('tools.php?page=wp-rollback'),
            'restUrl' => esc_url_raw(rest_url()),
            'rollbackSteps' => $this->getRollbackSteps(),
        ]);
    }

    /**
     * Get rollback steps data for script localization.
     *
     * @since 1.0.0
     * @return array
     */
    protected function getRollbackSteps(): array
    {
        $stepRegisterer = SharedCore::container()->make(RollbackStepRegisterer::class);
        $steps = [];
        
        foreach ($stepRegisterer->getAllRollbackSteps() as $stepClass) {
            $steps[] = [
                'id' => $stepClass::id(),
                'rollbackProcessingMessage' => $stepClass::rollbackProcessingMessage()
            ];
        }
        
        return $steps;
    }
} 