/**
 * External dependencies.
 */
import { Route, Routes as ReactRoutes, Navigate } from 'react-router-dom';

/**
 * Internal dependencies.
 */
import { Dashboard } from './pages/Dashboard';
import { PluginList } from './pages/PluginList';
import { Rollbacks as RollbackPage } from './pages/Rollbacks';
import { ThemeList } from './pages/ThemeList';

/**
 * Routes Component - Main router for WP Rollback Free Plugin
 *
 * @return {JSX.Element} The routes component
 */
const Routes = () => {
    return (
        <ReactRoutes>
            <Route path="/" element={ <Dashboard /> } />
            <Route path="plugin-list" element={ <PluginList /> } />
            <Route path="theme-list" element={ <ThemeList /> } />
            <Route path="rollback/:type/:slug" element={ <RollbackPage /> } />

            { /* When no routes match, redirect to dashboard */ }
            <Route path="*" element={ <Navigate to="/" replace /> } />
        </ReactRoutes>
    );
};

export default Routes;
