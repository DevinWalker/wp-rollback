<?php

declare(strict_types=1);

namespace WpRollback\Free\Rollbacks;

use WpRollback\SharedCore\Plugin\PluginInfo;
use WpRollback\SharedCore\Core\Utilities\PluginUtility;

/**
 * Class PluginRollback
 * 
 * Handles plugin rollback functionality
 */
class PluginRollback {
    /**
     * @var PluginInfo
     */
    private PluginInfo $plugin_info;

    /**
     * Constructor
     *
     * @param string $pluginSlug The plugin slug
     */
    public function __construct(string $pluginSlug) {
        $this->plugin_info = new PluginInfo($pluginSlug);
    }

    /**
     * Initialize the rollback process
     *
     * @param string $version Version to rollback to
     * @return bool|\WP_Error True on success, WP_Error on failure
     */
    public function rollback(string $version) {
        if (!PluginUtility::currentUserCanRollback()) {
            return new \WP_Error(
                'insufficient_permissions',
                __('You do not have permission to perform rollbacks.', 'wp-rollback')
            );
        }

        if (!PluginUtility::isValidVersion($version)) {
            return new \WP_Error(
                'invalid_version',
                __('Invalid version number provided.', 'wp-rollback')
            );
        }

        $currentVersion = $this->plugin_info->getCurrentVersion();
        if ($currentVersion === $version) {
            return new \WP_Error(
                'same_version',
                __('Cannot rollback to the same version.', 'wp-rollback')
            );
        }

        $availableVersions = $this->plugin_info->getAvailableVersions();
        if (!in_array($version, $availableVersions, true)) {
            return new \WP_Error(
                'version_not_found',
                __('The requested version is not available.', 'wp-rollback')
            );
        }

        // Perform rollback logic here
        return true;
    }
} 