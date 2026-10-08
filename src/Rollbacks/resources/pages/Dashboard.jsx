import { useNavigate } from 'react-router-dom';
import { Button, ExternalLink, Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { plugins, brush, check, shield } from '@wordpress/icons';
import Layout from '../layout/Layout';
import IllustrationPremiumRollback from '../components/IllustrationPremiumRollback';

/**
 * Dashboard component that serves as the main landing page for WP Rollback.
 * Provides options to roll back plugins or themes.
 *
 * @return {JSX.Element} The rendered Dashboard component
 */
export const Dashboard = () => {
    const navigate = useNavigate();

    const comparisonRows = [
        {
            title: __( 'WordPress.org plugins and themes', 'wp-rollback' ),
            description: __( 'Any earlier version', 'wp-rollback' ),
            free: true,
        },
        {
            title: __( 'Premium plugins and themes', 'wp-rollback' ),
            description: __( 'Verified versions from Plugin Vault', 'wp-rollback' ),
            free: false,
        },
        {
            title: __( 'Automatic backups', 'wp-rollback' ),
            description: __( 'The current version is archived before each update', 'wp-rollback' ),
            free: false,
        },
        {
            title: __( 'Rollback notes', 'wp-rollback' ),
            description: __( 'Tell your team why you rolled back', 'wp-rollback' ),
            free: false,
        },
        {
            title: __( 'Activity log', 'wp-rollback' ),
            description: __( 'Who rolled back what, and when', 'wp-rollback' ),
            free: false,
        },
        {
            title: __( 'Abilities API', 'wp-rollback' ),
            description: __( 'AI agents can run rollbacks on WordPress 6.9+', 'wp-rollback' ),
            free: false,
        },
    ];

    return (
        <Layout>
            <div className="wpr-subheader">
                <h1>{ __( 'Roll back a plugin or theme', 'wp-rollback' ) }</h1>
                <p>
                    { __(
                        'Return anything installed from WordPress.org to an earlier version in a few clicks.',
                        'wp-rollback'
                    ) }
                </p>
            </div>

            <div className="wpr-dashboard">
                <div className="wpr-dashboard__options">
                    <div className="wpr-card wpr-dashboard__option">
                        <div className="wpr-dashboard__option-heading">
                            <div className="wpr-icon-tile">
                                <Icon icon={ plugins } />
                            </div>
                            <div>
                                <h2>{ __( 'Plugins', 'wp-rollback' ) }</h2>
                                <p>{ __( 'Pick a plugin, then choose the version to restore.', 'wp-rollback' ) }</p>
                            </div>
                        </div>
                        <Button
                            onClick={ () => {
                                navigate( '/plugin-list' );
                            } }
                            className="wpr-plugin-rollback-button"
                            variant="primary"
                            __next40pxDefaultSize
                        >
                            { __( 'Roll back a plugin', 'wp-rollback' ) }
                        </Button>
                    </div>
                    <div className="wpr-card wpr-dashboard__option">
                        <div className="wpr-dashboard__option-heading">
                            <div className="wpr-icon-tile">
                                <Icon icon={ brush } />
                            </div>
                            <div>
                                <h2>{ __( 'Themes', 'wp-rollback' ) }</h2>
                                <p>{ __( 'Pick a theme, then choose the version to restore.', 'wp-rollback' ) }</p>
                            </div>
                        </div>
                        <Button
                            onClick={ () => {
                                navigate( '/theme-list' );
                            } }
                            className="wpr-theme-rollback-button"
                            variant="primary"
                            __next40pxDefaultSize
                        >
                            { __( 'Roll back a theme', 'wp-rollback' ) }
                        </Button>
                    </div>
                </div>

                <div className="wpr-card wpr-dashboard__pro">
                    <div className="wpr-pro-card__header">
                        <span>{ __( 'WP Rollback Pro', 'wp-rollback' ) }</span>
                        <span className="wpr-pro-card__badge">{ __( 'Upgrade', 'wp-rollback' ) }</span>
                    </div>
                    <div className="wpr-dashboard__pro-body">
                        <div className="wpr-dashboard__pro-pitch">
                            <div className="wpr-pro-card__illustration">
                                <IllustrationPremiumRollback />
                            </div>
                            <h2>{ __( 'Roll back premium plugins and themes too', 'wp-rollback' ) }</h2>
                            <p>
                                { __(
                                    'Free covers WordPress.org. Pro covers everything else you hold a license for, and saves a backup before every update.',
                                    'wp-rollback'
                                ) }
                            </p>
                            <div className="wpr-dashboard__pro-actions">
                                <Button
                                    href="https://wprollback.com/pricing/?utm_source=free-plugin&utm_medium=dashboard&utm_campaign=upgrade"
                                    target="_blank"
                                    variant="primary"
                                    className="wpr-upgrade-rollback-button"
                                    __next40pxDefaultSize
                                >
                                    { __( 'Upgrade to Pro', 'wp-rollback' ) }
                                </Button>
                                <ExternalLink href="https://wprollback.com/features/?utm_source=free-plugin&utm_medium=dashboard&utm_campaign=upgrade">
                                    { __( 'See all Pro features', 'wp-rollback' ) }
                                </ExternalLink>
                            </div>
                            <div className="wpr-dashboard__pro-guarantee">
                                <Icon icon={ shield } size={ 18 } />
                                <span>{ __( '30-day money-back guarantee', 'wp-rollback' ) }</span>
                            </div>
                        </div>

                        <table className="wpr-dashboard__compare">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <span className="screen-reader-text">{ __( 'Feature', 'wp-rollback' ) }</span>
                                    </th>
                                    <th scope="col">{ __( 'Free', 'wp-rollback' ) }</th>
                                    <th scope="col" className="wpr-dashboard__compare-pro">
                                        { __( 'Pro', 'wp-rollback' ) }
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                { comparisonRows.map( row => (
                                    <tr key={ row.title }>
                                        <th scope="row">
                                            <span className="wpr-dashboard__compare-title">{ row.title }</span>
                                            <span className="wpr-dashboard__compare-description">
                                                { row.description }
                                            </span>
                                        </th>
                                        <td>
                                            { row.free ? (
                                                <>
                                                    <Icon icon={ check } size={ 20 } />
                                                    <span className="screen-reader-text">
                                                        { __( 'Included', 'wp-rollback' ) }
                                                    </span>
                                                </>
                                            ) : (
                                                <>
                                                    <span className="wpr-dashboard__compare-dash" aria-hidden="true" />
                                                    <span className="screen-reader-text">
                                                        { __( 'Not included', 'wp-rollback' ) }
                                                    </span>
                                                </>
                                            ) }
                                        </td>
                                        <td className="wpr-dashboard__compare-pro">
                                            <Icon icon={ check } size={ 20 } />
                                            <span className="screen-reader-text">
                                                { __( 'Included', 'wp-rollback' ) }
                                            </span>
                                        </td>
                                    </tr>
                                ) ) }
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </Layout>
    );
};
