<?php

/**
 * Upsell validation step for the free plugin.
 *
 * This step shows an upsell message instead of actually validating the package,
 * promoting the pro version's enhanced security features.
 *
 * @package WpRollback\Free\Rollbacks\RollbackSteps
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks\RollbackSteps;

use WpRollback\SharedCore\Rollbacks\DTO\RollbackApiRequestDTO;
use WpRollback\SharedCore\Rollbacks\Contract\RollbackStep;
use WpRollback\SharedCore\Rollbacks\Contract\RollbackStepResult;

/**
 * Upsell step that promotes pro validation features instead of actual validation
 *
 * @since 3.0.0
 */
class UpsellValidatePackage implements RollbackStep
{
    /**
     * @inheritdoc
     * @since 3.0.0
     */
    public static function id(): string
    {
        return 'validate-package';
    }

    /**
     * @inheritdoc
     * @since 3.0.0
     */
    public function execute(RollbackApiRequestDTO $rollbackApiRequestDTO): RollbackStepResult
    {
        // Simulate processing time for better UX
        usleep(800000); // 0.8 seconds
        
        $assetType = $rollbackApiRequestDTO->getType();
        $assetSlug = $rollbackApiRequestDTO->getSlug();
        
        // Get the downloaded package from transient to verify it exists
        $package = get_transient("wpr_{$assetType}_{$assetSlug}_package");
        
        // Basic existence check (free version does minimal validation)
        if (empty($package) || !is_string($package) || !file_exists($package)) {
            return new RollbackStepResult(
                false,
                $rollbackApiRequestDTO,
                __('Package not found for rollback.', 'wp-rollback')
            );
        }
        
        // For the free version, we skip comprehensive validation and show success
        // with an upsell message about the pro version's enhanced security features
        $upsellMessage = __(
            'Basic validation complete. 🔒 WP Rollback Pro includes advanced package integrity scanning. Upgrade at wprollback.com/pricing',
            'wp-rollback'
        );
        
        return new RollbackStepResult(
            true,
            $rollbackApiRequestDTO,
            $upsellMessage,
            null,
            [
                'validation_status' => 'basic',
                'upsell_shown' => true,
                'pro_features' => [
                    'advanced_security_scanning',
                    'malware_detection', 
                    'comprehensive_integrity_checks',
                    'detailed_validation_reports'
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     * @since 3.0.0
     */
    public static function rollbackProcessingMessage(): string
    {
        return esc_html__('Validating package integrity…', 'wp-rollback');
    }
} 