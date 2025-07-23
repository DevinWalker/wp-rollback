import { BaseThemeRollbackHandler } from '@wp-rollback/shared-core/handlers/BaseThemeRollbackHandler';

/**
 * Theme Specific WP Rollback
 *
 * Adds a rollback option to themes using the base handler
 */
new BaseThemeRollbackHandler().initialize();
