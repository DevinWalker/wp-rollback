<?php

/**
 * @package WpRollback\Free\Core
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Core;

use WpRollback\SharedCore\Core\BaseConstants;

/**
 * Free plugin constants implementation.
 *
 * @since 3.0.0
 */
class Constants extends BaseConstants
{
    /**
     * Constants constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'wp-rollback',    // Text domain
            '3.0.2',          // Version
            'wp-rollback',    // Slug
            'wp-rollback-nonce', // Nonce
            self::findPluginFile('wp-rollback', __FILE__) // Plugin file path
        );
    }
}
