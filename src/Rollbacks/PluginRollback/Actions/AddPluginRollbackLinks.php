<?php

/**
 * AddPluginRollbackLinks
 *
 * This file extends the shared AddPluginRollbackLinks class for the free plugin.
 * It uses the unified handler with appropriate configuration for the free version.
 *
 * @package WpRollback\Free\Rollbacks\PluginRollback\Actions
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks\PluginRollback\Actions;

use WpRollback\SharedCore\Rollbacks\PluginRollback\Actions\AddPluginRollbackLinks as SharedAddPluginRollbackLinks;
use WpRollback\Free\Core\Constants;

/**
 * Class AddPluginRollbackLinks
 *
 */
class AddPluginRollbackLinks extends SharedAddPluginRollbackLinks
{
    /**
     * Constructor.
     *
     * @param Constants $constants The Constants instance
     */
    public function __construct(Constants $constants)
    {
        // Call parent constructor with plugin slug and isProVersion = false
        parent::__construct($constants->getSlug(), false);
    }
}
