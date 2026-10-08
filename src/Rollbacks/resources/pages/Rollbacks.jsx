/**
 * External dependencies.
 */
import { __, sprintf } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import { useParams, useNavigate } from 'react-router-dom';
import Loading from '@wp-rollback/shared-core/components/Loading';
import RollbackModal from '@wp-rollback/shared-core/components/modals/RollbackModal';
import RollbackHeader from '@wp-rollback/shared-core/components/Rollbacks/RollbackHeader';
import RollbackActions from '@wp-rollback/shared-core/components/Rollbacks/RollbackActions';
import { RollbackProvider, useRollbackContext } from '@wp-rollback/shared-core/context/RollbackContext';
import { toPlainText } from '@wp-rollback/shared-core/utils';
import Layout from '../layout/Layout';
import RollbackContent from './RollbackContent';
import PremiumRollbackInlineUpsell from '../components/PremiumRollbackInlineUpsell';

/**
 * Inner component that consumes the context
 *
 * @return {JSX.Element} The rollback page component content
 */
const RollbacksContent = () => {
    const { isLoading, error, rollbackInfo, isPremiumAsset } = useRollbackContext();

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
                    <p>{ toPlainText( rollbackInfo.message || error ) }</p>
                </div>
            </Layout>
        );
    }

    // Show premium upsell for premium assets (in free plugin)
    if ( isPremiumAsset ) {
        const assetName = decodeEntities( rollbackInfo?.name || rollbackInfo.slug );

        return (
            <Layout className="wpr-rollback-page wpr-premium-rollback-page">
                <div className="wpr-subheader">
                    <h1>
                        { sprintf(
                            // translators: %s: plugin or theme name.
                            __( 'Roll back %s with Pro', 'wp-rollback' ),
                            assetName
                        ) }
                    </h1>
                    <p>
                        { sprintf(
                            // translators: %s: plugin or theme name.
                            __(
                                "%s isn't on WordPress.org, so earlier versions come from Plugin Vault and your local backups. Rolling back to them needs WP Rollback Pro.",
                                'wp-rollback'
                            ),
                            assetName
                        ) }
                    </p>
                </div>

                <PremiumRollbackInlineUpsell />
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
