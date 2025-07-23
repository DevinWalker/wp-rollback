/**
 * Register Free Templates
 *
 * @package
 * @since 1.0.0
 */
import { __ } from '@wordpress/i18n';
import { Dashicon } from '@wordpress/components';
import FreeCompleteTemplate from './Templates/FreeCompleteTemplate';

/**
 * Enhances the templates for Free features
 *
 * @param {Object} templates The template configuration object
 * @return {Object} Modified template configuration
 */
const registerFreeTemplates = templates => {
    // Add the complete template for free plugin
    templates.complete = {
        title: __( 'Rollback Complete', 'wp-rollback' ),
        icon: <Dashicon icon="yes-alt" />,
        component: FreeCompleteTemplate,
        buttons: {
            confirm: {
                title: __( 'Return to <type/> Screen', 'wp-rollback' ),
                onClick: type => {
                    // Handle both defined and undefined cases
                    const buttonUrl =
                        typeof type === 'string' && type === 'theme'
                            ? `${ window.location.origin }/wp-admin/themes.php`
                            : `${ window.location.origin }/wp-admin/plugins.php`;

                    window.location.href = buttonUrl;
                },
                isProcessing: false,
            },
            cancel: {
                title: __( 'Upgrade to Pro', 'wp-rollback' ),
                onClick: () => {
                    window.location.href = 'https://wprollback.com/';
                },
            },
        },
    };

    return templates;
};

export default registerFreeTemplates;
