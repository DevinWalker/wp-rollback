import { __ } from '@wordpress/i18n';
import { useNavigate } from 'react-router-dom';
import Layout from '../layout/Layout';
import ThemesDataView from '@wp-rollback/shared-core/components/ThemesDataView';
/**
 * ThemeList component displays a list of themes that can be rolled back
 *
 * @return {JSX.Element} The theme list component
 */
export const ThemeList = () => {
    const navigate = useNavigate();

    const handleNavigateToRollback = ( type, slug ) => {
        navigate( `/rollback/${ type }/${ slug }` );
    };

    return (
        <Layout>
            <div className="wpr-subheader">
                <h1>{ __( 'Themes', 'wp-rollback' ) }</h1>
                <p>{ __( 'Select a theme below to rollback to a previous version.', 'wp-rollback' ) }</p>
            </div>

            <div className="wpr-theme-list">
                <ThemesDataView onNavigateToRollback={ handleNavigateToRollback } />
            </div>
        </Layout>
    );
};
