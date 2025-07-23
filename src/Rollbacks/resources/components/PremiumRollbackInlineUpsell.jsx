/**
 * External dependencies.
 */
import { __ } from '@wordpress/i18n';
import { Button, Icon, Flex, FlexItem } from '@wordpress/components';
import { 
    starFilled, 
    shield, 
    backup, 
    info, 
    list
} from '@wordpress/icons';
import { useRollbackContext } from '@wp-rollback/shared-core/context/RollbackContext';

/**
 * PremiumRollbackInlineUpsell component provides an inline upsell experience within the rollback view
 *
 * @return {JSX.Element} The premium rollback inline upsell component
 */
const PremiumRollbackInlineUpsell = () => {
    const { handleCancel } = useRollbackContext();

    const handleUpgrade = () => {
        window.open('https://wprollback.com/pricing/?utm_source=free-plugin&utm_medium=rollback-upsell&utm_campaign=premium-rollback', '_blank');
    };

    const features = [
        {
            icon: backup,
            title: __('Premium Plugin & Theme Rollbacks', 'wp-rollback'),
            description: __('Roll back any premium plugin or theme from any marketplace - not just WordPress.org.', 'wp-rollback')
        },
        {
            icon: shield,
            title: __('Version Preservation', 'wp-rollback'),
            description: __('For premium assets, creates a zip archive of the current version and stores the archive.', 'wp-rollback')
        },
        {
            icon: info,
            title: __('Rollback Notes & Documentation', 'wp-rollback'),
            description: __('Add detailed notes to each rollback for better team coordination and change tracking.', 'wp-rollback')
        },
        {
            icon: list,
            title: __('Advanced Activity Logging', 'wp-rollback'),
            description: __('Complete audit trail of all rollbacks with timestamps, user tracking, and detailed logs.', 'wp-rollback')
        }
    ];

    return (
        <>
            <div className="wpr-premium-features">
                <h3>{ __('Why Upgrade to WP Rollback Pro?', 'wp-rollback') }</h3>
                <div className="wpr-premium-features-grid">
                    {features.map((feature, index) => (
                        <div key={index} className="wpr-premium-feature-card">
                            <div className="wpr-premium-feature-card-body">
                                <Flex align="flex-start" gap={8} justify="flex-start">
                                    <FlexItem>
                                        <div className="wpr-premium-feature-icon">
                                            <Icon icon={feature.icon} size={20} />
                                        </div>
                                    </FlexItem>
                                    <FlexItem>
                                        <h4 className="wpr-premium-feature-title">
                                            {feature.title}
                                        </h4>
                                        <p className="wpr-premium-feature-description">
                                            {feature.description}
                                        </p>
                                    </FlexItem>
                                </Flex>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            <div className="wpr-premium-guarantee">
                <div className="wpr-premium-guarantee-card">
                    <div className="wpr-premium-guarantee-card-body">
                        <Flex align="center" gap={4}>
                            <FlexItem>
                                <Icon icon={shield} size={24} />
                            </FlexItem>
                            <FlexItem>
                                <h4>{ __('30-Day Money-Back Guarantee', 'wp-rollback') }</h4>
                                <p>
                                    { __('Try WP Rollback Pro risk-free. If you\'re not completely satisfied, get your money back within 30 days.', 'wp-rollback') }
                                </p>
                            </FlexItem>
                        </Flex>
                    </div>
                </div>
            </div>

            <div className="wpr-premium-actions">
                <Button
                    variant="primary"
                    size="large"
                    icon={<Icon icon={starFilled} />}
                    onClick={handleUpgrade}
                    className="wpr-premium-cta"
                >
                    { __('Upgrade to WP Rollback Pro', 'wp-rollback') }
                </Button>
                <Button
                    variant="secondary"
                    size="large"
                    onClick={handleCancel}
                >
                    { __('Go Back', 'wp-rollback') }
                </Button>
                <Button
                    variant="link"
                    href="https://wprollback.com/features/?utm_source=free-plugin&utm_medium=rollback-upsell&utm_campaign=premium-rollback"
                    target="_blank"
                >
                    { __('Learn More About Pro Features', 'wp-rollback') }
                </Button>
            </div>
        </>
    );
};

export default PremiumRollbackInlineUpsell; 