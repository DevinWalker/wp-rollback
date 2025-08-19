<?php

/**
 * AddMultisiteThemeRollbackLinks
 *
 * This file extends the shared AddMultisiteThemeRollbackLinks class for the free plugin.
 * It uses the unified handler with appropriate configuration for the free version.
 * This is specifically for multisite installations.
 *
 * @package WpRollback\Free\Rollbacks\ThemeRollback\Actions
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks\ThemeRollback\Actions;

use WpRollback\SharedCore\Rollbacks\ThemeRollback\Actions\AddMultisiteThemeRollbackLinks as SharedAddMultisiteThemeRollbackLinks;
use WpRollback\Free\Core\Constants;

/**
 * Class AddMultisiteThemeRollbackLinks
 *
 * @since 3.0.0
 */
class AddMultisiteThemeRollbackLinks extends SharedAddMultisiteThemeRollbackLinks
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
