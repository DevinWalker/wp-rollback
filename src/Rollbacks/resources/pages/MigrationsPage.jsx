/**
 * Migrations Page Component for Free Plugin
 */

import { __ } from '@wordpress/i18n';
import MigrationsDataView from '@wp-rollback/shared-core/components/MigrationsDataView';
import Layout from '../layout/Layout';

/**
 * MigrationsPage component for Free plugin
 *
 * @return {JSX.Element} The rendered component
 */
export const MigrationsPage = () => {
    return (
        <Layout className="wpr-tools-content wpr-migrations-page">
            <div className="wpr-subheader">
                <h1>{ __( 'Database & Storage Migrations', 'wp-rollback' ) }</h1>
                <p>{ __( 'Inspect, run, revert, and debug system migrations.', 'wp-rollback' ) }</p>
            </div>

            <div className="wpr-migrations-content">
                <MigrationsDataView />
            </div>
        </Layout>
    );
};

export default MigrationsPage;
