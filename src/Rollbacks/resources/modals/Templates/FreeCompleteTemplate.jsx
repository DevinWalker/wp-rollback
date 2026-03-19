/**
 * Free Rollback Complete Modal.
 * Uses RollbackContext for state management.
 *
 * @param {Object} props         Component properties
 * @param {Object} props.buttons Button configuration for the template
 * @return {JSX.Element} Complete template content
 */

import { ExternalLink, Icon, Button } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import { useRollbackContext } from '@wp-rollback/shared-core/context/RollbackContext';
import { useEffect } from '@wordpress/element';
import { starFilled, backup, list, plugins } from '@wordpress/icons';
import RollbackButtons from '@wp-rollback/shared-core/components/modals/RollbackButtons';
import Lottie from 'lottie-react';
import rollbackCompleteAnimation from '@wp-rollback/shared-core/animations/rollback-complete.json';

const proFeatureChips = [
    { icon: plugins, label: __( 'Premium plugins', 'wp-rollback' ) },
    { icon: backup, label: __( 'Auto-archives', 'wp-rollback' ) },
    { icon: list, label: __( 'Activity logs', 'wp-rollback' ) },
];

const FreeCompleteTemplate = ( { buttons } ) => {
    const { rollbackInfo, rollbackVersion, setCurrentVersion } = useRollbackContext();

    useEffect( () => {
        if ( rollbackVersion ) {
            setCurrentVersion( rollbackVersion );
        }
    }, [ rollbackVersion, setCurrentVersion ] );

    if ( ! rollbackInfo || ! rollbackVersion ) {
        return null;
    }

    const successMessage = sprintf(
        /* translators: 1: Asset name 2: Asset version */
        __( '%1$s has been successfully rolled back to version %2$s.', 'wp-rollback' ),
        `<strong>${ decodeEntities( rollbackInfo.name ) }</strong>`,
        `<strong>${ rollbackVersion }</strong>`
    );

    return (
        <>
            { /* Hero — same as Pro: Lottie + title + message */ }
            <div className="wpr-complete-hero">
                <div className="wpr-complete-hero__animation">
                    <Lottie
                        animationData={ rollbackCompleteAnimation }
                        loop={ false }
                        autoplay={ true }
                        style={ { width: 120, height: 120 } }
                    />
                </div>
                <h3 className="wpr-complete-hero__title">{ __( 'Rollback Complete', 'wp-rollback' ) }</h3>
                <p className="wpr-complete-hero__message" dangerouslySetInnerHTML={ { __html: successMessage } } />
            </div>

            <div className="wpr-modal-content">
                { /* Pro upsell card */ }
                <div className="wpr-pro-upgrade-card">
                    <div className="wpr-pro-upgrade-card__eyebrow">
                        <Icon icon={ starFilled } size={ 13 } style={ { fill: '#fbbf24' } } />
                        { __( 'WP Rollback Pro', 'wp-rollback' ) }
                    </div>

                    <h3 className="wpr-pro-upgrade-card__headline">
                        { __( 'Rollback any plugin — not just WordPress.org.', 'wp-rollback' ) }
                    </h3>

                    <p className="wpr-pro-upgrade-card__description">
                        { __(
                            "Pro archives your version before every update and supports premium plugins like Elementor, Gravity Forms, and WooCommerce. Next time something breaks, you're one click from safety.",
                            'wp-rollback'
                        ) }
                    </p>

                    <div className="wpr-pro-upgrade-card__chips">
                        { proFeatureChips.map( ( chip, index ) => (
                            <span key={ index } className="wpr-pro-upgrade-chip">
                                <Icon icon={ chip.icon } size={ 14 } style={ { fill: 'rgba(255, 255, 255, 0.85)' } } />
                                { chip.label }
                            </span>
                        ) ) }
                    </div>

                    <div className="wpr-pro-upgrade-card__actions">
                        <Button
                            variant="primary"
                            className="wpr-pro-upgrade-card__cta"
                            onClick={ () => window.open( 'https://wprollback.com/pricing/', '_blank' ) }
                        >
                            { __( 'Upgrade Now', 'wp-rollback' ) }
                        </Button>
                        <ExternalLink href="https://wprollback.com/features/">
                            { __( 'See all Pro features', 'wp-rollback' ) }
                        </ExternalLink>
                    </div>
                </div>

                <RollbackButtons buttons={ buttons } />
            </div>
        </>
    );
};

export default FreeCompleteTemplate;
