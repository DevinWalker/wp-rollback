/**
 * External dependencies.
 */
import { __, _n, sprintf } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import { Button, Dashicon, ExternalLink, Icon } from '@wordpress/components';
import { backup, brush, cloud, code, comment, envelope, list, lock, plugins, shield } from '@wordpress/icons';
import { useRollbackContext } from '@wp-rollback/shared-core/context/RollbackContext';
import VersionsList from '@wp-rollback/shared-core/components/Rollbacks/VersionsList';
import { getVersionSource } from '@wp-rollback/shared-core/utils';
import IllustrationPremiumRollback from './IllustrationPremiumRollback';

const UPGRADE_URL =
    'https://wprollback.com/pricing/?utm_source=free-plugin&utm_medium=rollback-upsell&utm_campaign=premium-rollback';
const FEATURES_URL =
    'https://wprollback.com/features/?utm_source=free-plugin&utm_medium=rollback-upsell&utm_campaign=premium-rollback';

const VAULT_DOCS_URL =
    'https://docs.wprollback.com/plugin-vault?utm_source=free-plugin&utm_medium=rollback-upsell&utm_campaign=plugin-vault';

// Placeholder rows standing in for Plugin Vault versions, which only Pro can list.
// More than fit: the list clips and fades them to fill the card.
const VAULT_PLACEHOLDER_ROWS = [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ];

/**
 * PremiumRollbackInlineUpsell component shows the versions this site already
 * knows about for a premium plugin or theme, locked, beside the Pro upgrade.
 *
 * @return {JSX.Element} The premium rollback inline upsell component
 */
const PremiumRollbackInlineUpsell = () => {
    const { type, rollbackInfo, rollbackVersion, setRollbackVersion, currentVersion, handleCancel } =
        useRollbackContext();

    const assetName = decodeEntities( rollbackInfo?.name || rollbackInfo?.slug || '' );
    const versions = rollbackInfo?.versions || {};
    const versionKeys = Object.keys( versions );
    const localBackups = versionKeys.filter(
        version => version !== currentVersion && getVersionSource( versions[ version ] ) === 'local'
    ).length;

    let installedSummary = '';

    if ( currentVersion && localBackups > 0 ) {
        installedSummary = sprintf(
            // translators: 1: installed version number, 2: number of local backups.
            _n(
                'Version %1$s installed · %2$d local backup',
                'Version %1$s installed · %2$d local backups',
                localBackups,
                'wp-rollback'
            ),
            currentVersion,
            localBackups
        );
    } else if ( currentVersion ) {
        // translators: %s: version number.
        installedSummary = sprintf( __( 'Version %s installed', 'wp-rollback' ), currentVersion );
    }

    const features = [
        {
            icon: cloud,
            title: __( 'Plugin Vault', 'wp-rollback' ),
            description: __(
                'Verified earlier versions of premium plugins and themes you hold a license for.',
                'wp-rollback'
            ),
        },
        {
            icon: backup,
            tone: 'indigo',
            title: __( 'Automatic backups', 'wp-rollback' ),
            description: __( 'The current version is archived before each update, ready to restore.', 'wp-rollback' ),
        },
        {
            icon: comment,
            tone: 'purple',
            title: __( 'Rollback notes', 'wp-rollback' ),
            description: __( 'Record why you rolled back so your team knows what happened.', 'wp-rollback' ),
        },
        {
            icon: list,
            title: __( 'Activity log', 'wp-rollback' ),
            description: __( 'See who rolled back what, and when, across the site.', 'wp-rollback' ),
        },
        {
            icon: envelope,
            tone: 'indigo',
            title: __( 'Status report emails', 'wp-rollback' ),
            description: __( 'A regular summary of rollbacks and backups, sent to your inbox.', 'wp-rollback' ),
        },
        {
            icon: code,
            tone: 'purple',
            title: __( 'WP-CLI and Abilities API', 'wp-rollback' ),
            description: __( 'Run rollbacks from the command line or let AI agents do it.', 'wp-rollback' ),
        },
    ];

    return (
        <div className="wpr-premium-upsell">
            <div className="wpr-premium-upsell__columns">
                <section
                    className="wpr-card wpr-premium-upsell__versions"
                    aria-label={ __( 'Available versions', 'wp-rollback' ) }
                >
                    <div className="wpr-premium-upsell__asset">
                        <div className="wpr-icon-tile">
                            <Icon icon={ type === 'theme' ? brush : plugins } />
                        </div>
                        <div className="wpr-premium-upsell__asset-text">
                            <h2>{ assetName }</h2>
                            { installedSummary && <p>{ installedSummary }</p> }
                        </div>
                        <span className="wpr-premium-upsell__lock-badge">
                            <Icon icon={ lock } size={ 16 } />
                            { __( 'Pro feature', 'wp-rollback' ) }
                        </span>
                    </div>

                    { ( currentVersion || versionKeys.length > 0 ) && (
                        <VersionsList
                            versions={ versions }
                            rollbackVersion={ rollbackVersion }
                            setRollbackVersion={ setRollbackVersion }
                            currentVersion={ currentVersion }
                            disabled={ true }
                        />
                    ) }

                    <div className="wpr-premium-upsell__vault-rows" aria-hidden="true">
                        { VAULT_PLACEHOLDER_ROWS.map( row => (
                            <div key={ row } className="wpr-premium-upsell__vault-row">
                                <span className="wpr-premium-upsell__vault-radio" />
                                <span className="wpr-premium-upsell__vault-bar" />
                                <span className="wpr-version-source wpr-version-source--vault">
                                    <Dashicon icon="cloud" />
                                    { __( 'Vault', 'wp-rollback' ) }
                                </span>
                            </div>
                        ) ) }
                    </div>

                    <div className="wpr-premium-upsell__vault-info">
                        <div className="wpr-premium-upsell__vault-info-icon">
                            <Icon icon={ cloud } />
                        </div>
                        <div>
                            <h3>{ __( 'What is Plugin Vault?', 'wp-rollback' ) }</h3>
                            <p>
                                { sprintf(
                                    // translators: %s: plugin or theme name.
                                    __(
                                        "A shared library of earlier versions of premium plugins and themes, contributed by Pro sites and verified before they're stored and again before they're installed. Pro checks it for %s. You need your own valid license for anything you roll back.",
                                        'wp-rollback'
                                    ),
                                    assetName
                                ) }
                            </p>
                            <ExternalLink href={ VAULT_DOCS_URL }>
                                { __( 'Learn more about Plugin Vault', 'wp-rollback' ) }
                            </ExternalLink>
                        </div>
                    </div>
                </section>

                <aside className="wpr-card wpr-premium-upsell__pro">
                    <div className="wpr-pro-card__header">
                        <span>{ __( 'WP Rollback Pro', 'wp-rollback' ) }</span>
                        <span className="wpr-pro-card__badge">{ __( 'Upgrade', 'wp-rollback' ) }</span>
                    </div>
                    <div className="wpr-premium-upsell__pro-body">
                        <div className="wpr-pro-card__illustration">
                            <IllustrationPremiumRollback />
                        </div>
                        <div>
                            <h2>
                                { sprintf(
                                    // translators: %s: plugin or theme name.
                                    __( 'Roll back %s to an earlier version', 'wp-rollback' ),
                                    assetName
                                ) }
                            </h2>
                            <p>
                                { __(
                                    'Pro installs verified versions from Plugin Vault or your own backups, and saves a fresh backup before every update.',
                                    'wp-rollback'
                                ) }
                            </p>
                        </div>
                        <div className="wpr-premium-upsell__actions">
                            <Button
                                variant="primary"
                                href={ UPGRADE_URL }
                                target="_blank"
                                className="wpr-premium-cta"
                                __next40pxDefaultSize
                            >
                                { __( 'Upgrade to Pro', 'wp-rollback' ) }
                            </Button>
                            <div className="wpr-premium-upsell__links">
                                <Button variant="link" onClick={ handleCancel }>
                                    { __( 'Go back', 'wp-rollback' ) }
                                </Button>
                                <ExternalLink href={ FEATURES_URL }>
                                    { __( 'See all Pro features', 'wp-rollback' ) }
                                </ExternalLink>
                            </div>
                        </div>
                        <div className="wpr-premium-upsell__guarantee">
                            <Icon icon={ shield } size={ 32 } />
                            <div>
                                <strong>{ __( '30-day money-back guarantee', 'wp-rollback' ) }</strong>
                                <span>{ __( 'Try Pro risk-free. Full refund within 30 days.', 'wp-rollback' ) }</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <section className="wpr-card wpr-premium-upsell__features">
                <div className="wpr-premium-upsell__features-header">
                    <h2>{ __( 'What you get with Pro', 'wp-rollback' ) }</h2>
                </div>
                <div className="wpr-premium-upsell__features-grid">
                    { features.map( feature => (
                        <div key={ feature.title } className="wpr-premium-upsell__feature">
                            <div
                                className={ `wpr-premium-upsell__feature-icon${
                                    feature.tone ? ` wpr-premium-upsell__feature-icon--${ feature.tone }` : ''
                                }` }
                            >
                                <Icon icon={ feature.icon } />
                            </div>
                            <div>
                                <h3>{ feature.title }</h3>
                                <p>{ feature.description }</p>
                            </div>
                        </div>
                    ) ) }
                </div>
            </section>
        </div>
    );
};

export default PremiumRollbackInlineUpsell;
