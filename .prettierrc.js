const defaultConfig = require( '@wordpress/scripts/config/.prettierrc.js' );

// Add customizations to WordPress prettier config.
const config = {
    ...defaultConfig,
    printWidth: 120,
    useTabs: false,
};

module.exports = config;
