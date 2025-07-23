import { __ } from '@wordpress/i18n';
import { useNavigate } from 'react-router-dom';
import Layout from '../layout/Layout';
import PluginsDataView from '@wp-rollback/shared-core/components/PluginsDataView';

/**
 * PluginList component displays a list of plugins that can be rolled back
 *
 * @return {JSX.Element} The plugin list component
 */
export const PluginList = () => {
    const navigate = useNavigate();

    const handleNavigateToRollback = ( type, slug ) => {
        navigate( `/rollback/${ type }/${ slug }` );
    };

    return (
        <Layout>
            <div className="wpr-subheader">
                <h1>{ __( 'Plugins', 'wp-rollback' ) }</h1>
                <p>{ __( 'Select a plugin below to rollback to a previous version.', 'wp-rollback' ) }</p>
            </div>

            <div className="wpr-plugin-list">
                <PluginsDataView onNavigateToRollback={ handleNavigateToRollback } />
            </div>
        </Layout>
    );
};
