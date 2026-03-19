/**
 * Register Free Templates
 *
 * @package
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
                    const buttonUrl =
                        typeof type === 'string' && type === 'theme'
                            ? window.wprData?.themesUrl
                            : window.wprData?.pluginsUrl;

                    window.location.href = buttonUrl;
                },
                isProcessing: false,
            },
        },
    };

    return templates;
};

export default registerFreeTemplates;
