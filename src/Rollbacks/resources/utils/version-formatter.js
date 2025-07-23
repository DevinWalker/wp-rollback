/**
 * Version formatter utility that uses the shared core
 */

import { formatVersion } from 'wp-rollback-shared-core';

/**
 * Format a version number for display, using the shared core utility
 *
 * @param {string} version The version to format
 * @return {string} The formatted version
 */
export const displayVersion = version => {
    // Use shared core utility
    return formatVersion( version );
};

/**
 * Format a version with plugin name
 *
 * @param {string} version The version to format
 * @param {string} name Plugin name
 * @return {string} The formatted version with name
 */
export const displayVersionWithName = ( version, name ) => {
    return `${ name } ${ formatVersion( version ) }`;
};
