const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config.js');

module.exports = {
    ...defaultConfig,
    ...{
        entry: {
            'admin': path.resolve( process.cwd(), 'src', 'admin.js' ),
            'themes': path.resolve( process.cwd(), 'src', 'themes-wp-rollback.js' ),
        },
    },
};
