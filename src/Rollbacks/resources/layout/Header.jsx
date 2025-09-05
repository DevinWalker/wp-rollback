import { Link } from 'react-router-dom';
import { ExternalLink, Button, Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { starFilled } from '@wordpress/icons';
import LogoFree from '../components/LogoFree';

const Header = () => {
    return (
        <div className="wpr-header">
            <div className="wpr-header-logo">
                <Link to="/" className="wpr-header-logo">
                    <LogoFree style={ { width: 162, height: 'auto' } } />
                </Link>
            </div>
            <div className="wpr-header-content"></div>
            <div className="wpr-header-actions">
                <ExternalLink href="https://docs.wprollback.com/?utm_source=free-plugin&utm_medium=header&utm_campaign=documentation">
                    { __( 'Documentation', 'wp-rollback' ) }
                </ExternalLink>
                <ExternalLink href="https://wordpress.org/support/plugin/wp-rollback/">{ __( 'Support', 'wp-rollback' ) }</ExternalLink>
                <Button
                    href="https://wprollback.com/pricing/"
                    target="_blank"
                    icon={ <Icon icon={ starFilled } /> }
                    iconSize={ 16 }
                    variant={ 'primary' }
                >
                    { __( 'Upgrade to Pro', 'wp-rollback' ) }
                </Button>
            </div>
        </div>
    );
};

export default Header;
