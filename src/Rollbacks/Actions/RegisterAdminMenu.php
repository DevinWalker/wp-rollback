<?php

/**
 * Menu registration for WP Rollback free plugin.
 *
 * @package WpRollback\Free\Rollbacks\Actions
 * @since 3.0.0
 */

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks\Actions;

use WpRollback\SharedCore\Rollbacks\Actions\BaseRegisterAdminMenu;

/**
 * RegisterAdminMenu implementation for free version.
 *
 * @since 3.0.0
 */
class RegisterAdminMenu extends BaseRegisterAdminMenu
{
    /**
     * {@inheritdoc}
     */
    protected function getMenuTitle(): string
    {
        return __('WP Rollback', 'wp-rollback');
    }

    /**
     * {@inheritdoc}
     */
    protected function getPageTitle(): string
    {
        return __('WP Rollback', 'wp-rollback');
    }

}
