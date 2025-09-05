import { useNavigate } from 'react-router-dom';
import { Card, CardBody, Button, Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { plugins, brush } from '@wordpress/icons';
import Layout from '../layout/Layout';

/**
 * Dashboard component that serves as the main landing page for WP Rollback.
 * Provides options to rollback plugins or themes.
 *
 * @return {JSX.Element} The rendered Dashboard component
 */
export const Dashboard = () => {
    const navigate = useNavigate();

    return (
        <Layout>
            <div className="wpr-subheader">
                <h1>{ __( 'Rollback a Plugin or Theme', 'wp-rollback' ) }</h1>
                <p>
                    { __(
                        'With WP Rollback you can go back to a previous WordPress.org plugin or theme version with ease. Which action would you like to perform today?',
                        'wp-rollback'
                    ) }
                </p>
            </div>

            <div className={ 'wpr-rollback-options' }>
                <Card isRounded={ false } elevation={ 2 }>
                    <CardBody>
                        <div className="wpr-icon-heading">
                            <Icon icon={ plugins } />
                            <h2>{ __( 'Plugin Version Rollback', 'wp-rollback' ) }</h2>
                        </div>
                        <p>
                            { __(
                                "Revert any WordPress.org plugin to a previous version with just a few clicks. Choose the plugin and version you'd like to restore.",
                                'wp-rollback'
                            ) }
                        </p>
                        <Button
                            onClick={ () => {
                                navigate( '/plugin-list' );
                            } }
                            className="wpr-plugin-rollback-button"
                            variant="primary"
                        >
                            { __( 'Rollback a Plugin', 'wp-rollback' ) }
                        </Button>
                    </CardBody>
                </Card>
                <Card isRounded={ false } elevation={ 2 }>
                    <CardBody>
                        <div className="wpr-icon-heading">
                            <Icon icon={ brush } />
                            <h2>{ __( 'Theme Version Rollback', 'wp-rollback' ) }</h2>
                        </div>
                        <p>
                            { __(
                                "Revert any WordPress.org plugin to a previous version with just a few clicks. Choose the plugin and version you'd like to restore.",
                                'wp-rollback'
                            ) }
                        </p>
                        <Button
                            onClick={ () => {
                                navigate( '/theme-list' );
                            } }
                            className="wpr-theme-rollback-button"
                            variant="primary"
                        >
                            { __( 'Rollback a Theme', 'wp-rollback' ) }
                        </Button>
                    </CardBody>
                </Card>
            </div>

            <Card isRounded={ false } elevation={ 2 }>
                <CardBody>
                    <h3>{ __( 'The Safest Way to Rollback Premium Plugins & Themes', 'wp-rollback' ) }</h3>
                    <p>
                        { __(
                            'Get complete control over every plugin on your site with automated backups, rollback notes for your team, and support for premium plugins from any marketplace.',
                            'wp-rollback'
                        ) }
                    </p>
                    <Button href="https://wprollback.com/pricing/" variant="primary" className="wpr-upgrade-rollback-button">
                        { __( 'Upgrade to Pro', 'wp-rollback' ) }
                    </Button>
                </CardBody>
            </Card>
        </Layout>
    );
};
