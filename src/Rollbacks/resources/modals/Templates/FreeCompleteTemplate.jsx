/**
 * Free Rollback Complete Modal.
 * Uses RollbackContext for state management.
 *
 * @param {Object} props         Component properties
 * @param {Object} props.buttons Button configuration for the template
 * @return {JSX.Element} Complete template content
 */

import { ExternalLink, Icon, Button, Notice } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { useRollbackContext } from '@wp-rollback/shared-core/context/RollbackContext';
import { useNavigate } from 'react-router-dom';
import { useEffect } from '@wordpress/element';
import { check, starFilled, shield, backup, list, help } from '@wordpress/icons';
import RollbackButtons from '@wp-rollback/shared-core/components/modals/RollbackButtons';

const FreeCompleteTemplate = ( { buttons } ) => {
    const { rollbackInfo, rollbackVersion, setCurrentVersion } = useRollbackContext();
    const navigate = useNavigate();

    // Update the current version to the rolled-back version.
    useEffect( () => {
        if ( rollbackVersion ) {
            setCurrentVersion( rollbackVersion );
        }
    }, [ rollbackVersion, setCurrentVersion ] );

    // Don't render until we have the required data
    if ( ! rollbackInfo || ! rollbackVersion ) {
        return null;
    }

    const successMessage = sprintf(
        /* translators: 1: Asset name 2: Asset version */
        __( '%1$s has been successfully rolled back to version %2$s.', 'wp-rollback' ),
        `<strong>${ rollbackInfo.name }</strong>`,
        `<strong>${ rollbackVersion }</strong>`
    );

    const proFeatures = [
        {
            icon: list,
            title: __( 'Detailed Activity Logs', 'wp-rollback' ),
            description: __( 'Track every rollback with comprehensive logs and notes', 'wp-rollback' ),
        },
        {
            icon: backup,
            title: __( 'Version Preservation', 'wp-rollback' ),
            description: __( 'Preserve current versions of premium assets before updates', 'wp-rollback' ),
        },
        {
            icon: shield,
            title: __( 'Priority Support', 'wp-rollback' ),
            description: __( 'Get expert help when you need it most', 'wp-rollback' ),
        },
    ];

    return (
        <>
            {/* Success Message */}
            <Notice status="success" isDismissible={ false } className="wpr-success-notice">
                <div className="wpr-success-notice__content">
                    <Icon icon={ check } size={ 48 } />
                    <div dangerouslySetInnerHTML={ { __html: successMessage } } />
                </div>
            </Notice>

            <div className="wpr-modal-content">
                {/* What's Next Section */}
                <div className="wpr-next-steps">
                    <h4 className="wpr-next-steps__heading">
                        <Icon icon={ help } size={ 20 } />
                        { __( "What's next?", 'wp-rollback' ) }
                    </h4>
                    <ol className="wpr-next-steps__list">
                        <li>
                            { __(
                                'Check your website to verify the rollback resolved any visual or functional issues',
                                'wp-rollback'
                            ) }
                        </li>
                        <li>
                            { __(
                                "If you rolled back due to an error message, review your error logs to confirm it's resolved",
                                'wp-rollback'
                            ) }
                        </li>
                        <li>
                            { __(
                                'Test key functionality on your site to ensure everything works as expected',
                                'wp-rollback'
                            ) }
                        </li>
                    </ol>
                </div>

                {/* Pro Features Upgrade Card */}
                <div className="wpr-pro-upgrade-card">
                    <div className="wpr-pro-upgrade-card__body">
                        <div className="wpr-pro-upgrade-card__header">
                            <div style={{ fill: '#8b5cf6' }}><Icon icon={ starFilled } size={ 24 } /></div>
                            <h3>
                                { __( 'Upgrade to WP Rollback Pro', 'wp-rollback' ) }
                            </h3>
                        </div>
                        
                        <p className="wpr-pro-upgrade-card__description">
                            { __( 'Take your rollback management to the next level with professional features designed for serious WordPress sites.', 'wp-rollback' ) }
                        </p>

                        <div className="wpr-pro-upgrade-card__features">
                            { proFeatures.map( ( feature, index ) => (
                                <div key={ index } className="wpr-pro-upgrade-card__feature">
                                    <Icon icon={ feature.icon } size={ 20 } />
                                    <div className="wpr-pro-upgrade-card__feature-content">
                                        <h5>{ feature.title }</h5>
                                        <p>{ feature.description }</p>
                                    </div>
                                </div>
                            ) ) }
                        </div>

                        <div className="wpr-pro-upgrade-card__actions">
                            <Button
                                variant="secondary"
                                onClick={ () => {
                                    window.open( 'https://wprollback.com/pricing/', '_blank' );
                                } }
                            >
                                { __( 'Upgrade Now', 'wp-rollback' ) }
                            </Button>
                            <ExternalLink href="https://wprollback.com/features/">
                                { __( 'Learn more', 'wp-rollback' ) }
                            </ExternalLink>
                        </div>
                    </div>
                </div>

                {/* Help Section */}
                <div className="wpr-help-section">
                    <p className="wpr-help-section__text">
                        { __( 'Need help with your rollback?', 'wp-rollback' ) }
                    </p>
                    <ExternalLink href="https://wprollback.com/troubleshooting-guide/">
                        { __( 'View our troubleshooting guide', 'wp-rollback' ) }
                    </ExternalLink>
                </div>

                <RollbackButtons buttons={ buttons } />
            </div>
        </>
    );
};

export default FreeCompleteTemplate;
