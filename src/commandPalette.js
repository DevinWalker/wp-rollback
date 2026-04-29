import { store as commandsStore } from '@wordpress/commands';
import { dispatch } from '@wordpress/data';
import { useRollbackCommandLoader } from '../../shared-core/src/Frontend/commandPalette/RollbackCommandPalette';

/**
 * Register the rollback command loader directly with the WordPress commands
 * store. This runs at module load time — no React root or PluginArea needed.
 * The command palette reads from this store and calls the hook in its own
 * React rendering context, making React hooks valid inside the loader.
 */
dispatch( commandsStore ).registerCommandLoader( {
    name: 'wp-rollback/rollback-asset',
    hook: useRollbackCommandLoader,
} );
