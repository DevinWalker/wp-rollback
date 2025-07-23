/**
 * External dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useParams, useNavigate } from 'react-router-dom';
import { Notice } from '@wordpress/components';
import Loading from '@wp-rollback/shared-core/components/Loading';
import RollbackModal from '@wp-rollback/shared-core/components/modals/RollbackModal';
import RollbackHeader from '@wp-rollback/shared-core/components/Rollbacks/RollbackHeader';
import RollbackActions from '@wp-rollback/shared-core/components/Rollbacks/RollbackActions';
import { RollbackProvider, useRollbackContext } from '@wp-rollback/shared-core/context/RollbackContext';
import VersionsList from '@wp-rollback/shared-core/components/Rollbacks/VersionsList';
import Layout from '../layout/Layout';
import RollbackContent from './RollbackContent';
import PremiumRollbackInlineUpsell from '../components/PremiumRollbackInlineUpsell';

/**
 * Inner component that consumes the context
 *
 * @return {JSX.Element} The rollback page component content
 */
const RollbacksContent = () => {
    const { 
        isLoading, 
        error, 
        rollbackInfo, 
        isPremiumAsset,
        rollbackVersion,
        setRollbackVersion,
        currentVersion
    } = useRollbackContext();

    if ( isLoading ) {
        return (
            <Layout>
                <Loading />
            </Layout>
        );
    }

    // Handle error state
    if ( error || rollbackInfo.message ) {
        return (
            <Layout>
                <div className="wpr-api-error">
                    <h1>{ rollbackInfo.code || __( 'Error', 'wp-rollback' ) }</h1>
                    <p>{ rollbackInfo.message || error }</p>
                </div>
            </Layout>
        );
    }

    // Show premium upsell for premium assets (in free plugin)
    if ( isPremiumAsset ) {
        return (
            <Layout className="wpr-rollback-page wpr-premium-rollback-page">
                {/* Custom header for premium assets */}
                <div className="wpr-subheader">
                    <h1>{ __('Unlock Premium Rollbacks', 'wp-rollback') }</h1>
                    <p>{ __('This premium asset requires WP Rollback Pro for safe version rollbacks.', 'wp-rollback') }</p>
                </div>
                
                <div className="wpr-rollback-component-wrap">
                    <div className="wpr-premium-upsell">
                        <Notice status="warning" isDismissible={false} className="wpr-premium-notice">
                            <p>
                                <strong>{rollbackInfo?.name || slug}</strong> { __('is not available on WordPress.org and requires WP Rollback Pro for version control.', 'wp-rollback') }
                            </p>
                        </Notice>

                        {/* Show available versions if they exist - moved higher */}
                        { rollbackInfo?.versions && Object.keys( rollbackInfo.versions ).length > 0 && (
                            <div className="wpr-available-versions">
                                <h3>{ __( 'Available Versions (Pro Feature)', 'wp-rollback' ) }</h3>
                                <p className="wpr-versions-note">
                                    { __( 'These versions would be available for rollback with WP Rollback Pro:', 'wp-rollback' ) }
                                </p>
                                <VersionsList
                                    versions={ rollbackInfo.versions }
                                    rollbackVersion={ rollbackVersion }
                                    setRollbackVersion={ setRollbackVersion }
                                    currentVersion={ currentVersion }
                                    disabled={ true }
                                />
                            </div>
                        ) }

                        <PremiumRollbackInlineUpsell />
                    </div>
                </div>
            </Layout>
        );
    }

    // Show normal rollback content for wp.org assets
    return (
        <Layout className="wpr-rollback-page">
            <RollbackHeader />
            <div className="wpr-rollback-component-wrap">
                <RollbackContent />
                <RollbackActions />
            </div>
            <RollbackModal />
        </Layout>
    );
};

/**
 * RollbackPage component handles the rollback process for plugins and themes
 *
 * @return {JSX.Element} The rollback page component
 */
export const Rollbacks = () => {
    const { type, slug } = useParams();
    const navigate = useNavigate();

    // Handle navigation back to home
    const handleCancel = () => {
        navigate( '/' );
    };

    return (
        <RollbackProvider type={ type } slug={ slug } onCancel={ handleCancel }>
            <RollbacksContent />
        </RollbackProvider>
    );
};

export default Rollbacks;
