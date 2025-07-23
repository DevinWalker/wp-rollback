/**
 * WP Rollback Free Plugin
 * Frontend Application Entry Point
 */

import { HashRouter } from 'react-router-dom';
import { createRoot } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import { addFilter } from '@wordpress/hooks';
import Routes from './Routes';
import registerTemplates from './modals/registerTemplates';

import '@wp-rollback/shared-core/styles/main.scss';
import './styles/main.scss';

// Register templates before initializing the app
addFilter( 'wpRollback.templates', 'wpRollback-free/registerTemplates', registerTemplates, 10 );

/**
 * Initialize the React app on DOM ready
 */
domReady( function () {
    const container = document.getElementById( 'root-wp-rollback-admin' );
    if ( ! container ) {
        return;
    }

    createRoot( container ).render(
        <HashRouter>
            <Routes />
        </HashRouter>
    );
} );
