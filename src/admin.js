import './admin.scss';
import { Button, Dashicon, Modal, Spinner } from '@wordpress/components';
import { render, useEffect, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';
import { decodeEntities } from '@wordpress/html-entities';
import { getQueryArgs } from '@wordpress/url';
import ExpandableText from './ExpandableText';

const AdminPage = () => {

    const [ isLoading, setIsLoading ] = useState( true );
    const [ rollbackInfo, setRollbackInfo ] = useState( false );
    const [ imageUrl, setImageUrl ] = useState( null );
    const queryArgs = getQueryArgs( window.location.search );
    const [ isConfirmModalOpen, setIsConfirmModalOpen ] = useState( false );
    const [ isChangelogModalOpen, setIsChangelogModalOpen ] = useState( false );
    const [ rollbackVersion, setIsRollbackVersion ] = useState( queryArgs.current_version );
    const { adminUrl, referrer } = wprData;

    const openConfirmModal = () => setIsConfirmModalOpen( true );
    const openChangelogModal = () => setIsChangelogModalOpen( true );
    const closeConfirmModal = () => setIsConfirmModalOpen( false );
    const closeChangelogModal = () => setIsChangelogModalOpen( false );

    useEffect( () => {
        let restUrl = `/wp-json/wp-rollback/v1/fetch-info/?type=${queryArgs.type}&slug=${queryArgs.type === 'theme' ? queryArgs.theme_file : queryArgs.plugin_slug}`;

        fetch( restUrl )
            .then( ( response ) => response.json() )
            .then( ( data ) => {
                setRollbackInfo( data );
                setIsLoading( false );
            } )
            .catch( ( error ) => {
                console.error( 'Error fetching data:', error );
            } );
    }, [] );

    useEffect( () => {
        if ( rollbackInfo && rollbackInfo.slug ) {  // Check if rollbackInfo is loaded and has a slug
            checkImage( `https://ps.w.org/${rollbackInfo.slug}/assets/icon-128x128.png`, ( exists ) => {
                if ( exists ) {
                    setImageUrl( `https://ps.w.org/${rollbackInfo.slug}/assets/icon-128x128.png` );
                } else {
                    checkImage( `https://ps.w.org/${rollbackInfo.slug}/assets/icon-128x128.jpg`, ( exists ) => {
                        if ( exists ) {
                            setImageUrl( `https://ps.w.org/${rollbackInfo.slug}/assets/icon-128x128.jpg` );
                        } else {
                            checkImage( `https://ps.w.org/${rollbackInfo.slug}/assets/icon-128x128.gif`, ( exists ) => {
                                if ( exists ) {
                                    setImageUrl( `https://ps.w.org/${rollbackInfo.slug}/assets/icon-128x128.gif` );
                                } else {
                                    setImageUrl( wprData.avatarFallback );
                                }
                            } );
                        }
                    } );
                }
            } );
        }
    }, [ rollbackInfo ] );

    // @TODO: Refactor to remove this function because the API should return false if the image doesn't exist.
    function checkImage( url, callback ) {
        var img = new Image();
        img.onload = () => callback( true );
        img.onerror = () => callback( false );
        img.src = url;
    }

    if ( isLoading ) {
        return (
            <div id={`wpr-wrap`} className={`wpr-wrap`}>
                <div className={'wpr-loading-content'}>
                    <div className={'wpr-loading-text'}>
                        <Spinner
                            style={{
                                height: 'calc(4px * 20)',
                                width : 'calc(4px * 20)',
                            }}
                        />
                        <p>{__( 'Loading...', 'wp-rollback' )}</p>
                    </div>
                </div>
            </div>
        );
    }

    // output error message if one is found in the API response
    if ( rollbackInfo.message ) {
        return (
            <div id={`wpr-wrap`} className={`wpr-wrap`}>
                <div className={`wpr-api-error`}>
                    <h1>{rollbackInfo.code}</h1>
                    <p>{rollbackInfo.message}</p>
                </div>
            </div>
        );
    }

    function getTimeAgo( dateString ) {

        // Convert to 24-hour format and remove 'GMT'
        let adjustedDateString = dateString.replace( 'am', ' AM' ).replace( 'pm', ' PM' ).replace( ' GMT', '' );
        adjustedDateString = new Date( adjustedDateString ).toLocaleString( 'en-US', { timeZone: 'GMT' } );

        const date = new Date( adjustedDateString );
        if ( isNaN( date.getTime() ) ) {
            console.error( 'Invalid date:', adjustedDateString );
            return 'Invalid date';
        }

        const now = new Date();
        const diffInSeconds = Math.floor( ( now - date ) / 1000 );

        if ( diffInSeconds < 60 ) {
            return `${diffInSeconds} seconds ago`;
        } else if ( diffInSeconds < 3600 ) {
            return `${Math.floor( diffInSeconds / 60 )} minutes ago`;
        } else if ( diffInSeconds < 86400 ) {
            return `${Math.floor( diffInSeconds / 3600 )} hours ago`;
        } else if ( diffInSeconds < 2592000 ) { // 30 days
            return `${Math.floor( diffInSeconds / 86400 )} days ago`;
        } else if ( diffInSeconds < 31536000 ) { // 365 days
            return `${Math.floor( diffInSeconds / 2592000 )} months ago`;
        } else {
            return `${Math.floor( diffInSeconds / 31536000 )} years ago`;
        }
    }

    console.log(rollbackInfo);
    console.log(queryArgs);

    return (
        <div className={'wpr-wrapper'}>
            <div className={'wpr-logo-wrap'}>
                <div className={'wpr-logo'}>
                    <h1>{__( 'WP Rollback', 'wp-rollback' )}</h1>
                    <a href={'https://wprollback.com/'} target={'_blank'}><img src={wprData.logo} width={250} height={'auto'} alt={'WP Rollback'}/></a>
                </div>

                <p className={'wpr-intro-text'}>{__( 'Select which version you would like to rollback to from the releases listed below.', '' )}</p>
            </div>
            <div className="wpr-content-wrap">
                {rollbackInfo.banners && queryArgs.type === 'plugin' && ( rollbackInfo.banners.high || rollbackInfo.banners.low ) && (
                    <div className="wpr-content-banner">
                        <img
                            src={( false !== rollbackInfo.banners.high ? rollbackInfo.banners.high : rollbackInfo.banners.low )}
                            width={800} height={'auto'}
                            className={'wpr-plugin-banner'}
                            alt={rollbackInfo.name}/>
                    </div>
                )}

                <div className="wpr-content-header">

                    {rollbackInfo.screenshot_url && queryArgs.type === 'theme' && (
                        <div className="wpr-content-banner wpr-content-banner__theme">
                            <img src={rollbackInfo.screenshot_url} width={240} height={180}
                                 className={'wpr-theme-screenshot'}
                                 alt={rollbackInfo.name}/>
                        </div>
                    )}

                    {imageUrl && queryArgs.type === 'plugin' && (
                        <div className={'wpr-plugin-avatar-wrap'}>
                            <img src={imageUrl} width={96} height={96} className={'wpr-plugin-avatar'}
                                 alt={rollbackInfo.name}/>
                        </div>

                    )}

                    <div className={'wpr-plugin-info'}>
                        <h2 className={'wpr-plugin-name'}>
                            {queryArgs.type === 'plugin' && (
                                <a href={`https://wordpress.org/plugins/${rollbackInfo.slug}/`} target={'_blank'}
                                   className={'wpr-heading-link'}
                                   alt={sprintf( __( 'View %s on WordPress.org', 'wp-rollback' ), rollbackInfo.name )}
                                >
                                    {decodeEntities( rollbackInfo.name )}
                                    <Dashicon icon="external"/>
                                </a>
                            )}
                            {queryArgs.type === 'theme' && (
                                <a href={rollbackInfo.homepage} target={'_blank'}
                                   className={'wpr-heading-link'}
                                   alt={sprintf( __( 'View %s on WordPress.org', 'wp-rollback' ), rollbackInfo.name )}>
                                    {decodeEntities( rollbackInfo.name )}
                                    <Dashicon icon="external"/>
                                </a>
                            )}

                        </h2>

                        {queryArgs.type === 'theme' && rollbackInfo.sections.description && (
                            <div className={'wpr-theme-description'}>
                                <ExpandableText text={rollbackInfo.sections.description}/>
                            </div>
                        )}

                        <div className={'wpr-pill-wrap'}>
                            <div className={'wpr-pill wpr-pill__black'}><span
                                className={'wpr-pill-text'}>{__( 'Installed version:', 'wp-rollback' )}{' '}
                                <strong>{queryArgs.current_version}</strong></span></div>

                            {queryArgs.type === 'plugin' && (
                                <div className={'wpr-pill wpr-pill__author'}>
                                    <span className={'wpr-pill-text'}>{__( 'Plugin author:', 'wp-rollback' )}{' '}
                                        <span className={'wpr-pill__link'}
                                              dangerouslySetInnerHTML={{
                                                  __html: rollbackInfo.author,
                                              }}>
                                         </span>
                                    </span>
                                </div>
                            )}
                        </div>

                    </div>

                    <div className={'wpr-meta-wrap'}>
                        {queryArgs.type === 'theme' && (
                            <div className={'wpr-meta-item wpr-meta-item__author-wrap'}>
                                <h3>{__( 'Theme Author', 'wp-rollback' )}</h3>
                                <div className={'wpr-theme-author-inner'}>
                                    <img src={rollbackInfo.author.avatar} width={64} height={64}/>
                                    <div className={'wpr-theme-author-info'}><a
                                        href={rollbackInfo.author.author_url}
                                        target={'_blank'}>{rollbackInfo.author.display_name}</a>
                                    </div>
                                </div>
                            </div>
                        )}

                        {queryArgs.type === 'plugin' && (
                            <div className={'wpr-meta-wrap__plugins'}>
                                <div className={'wpr-view-changelog'}>
                                    <Button isSecondary onClick={openChangelogModal}
                                            className={'wpr-version-changelog'}>{__( 'View Changelog', 'wp-rollback' )}</Button>
                                </div>
                                <h3>Last Updated</h3>
                                <div className={'wpr-updater-info'}>
                                    <Dashicon icon="clock"/>
                                    <span
                                        className={'wpr-plugin-lastupdate'}>{getTimeAgo( rollbackInfo.last_updated )}</span>
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                <div className={'wpr-versions-container'}>
                    {Object.keys( rollbackInfo.versions )
                        .filter( version => version !== 'trunk' ) // remove 'trunk'
                        .sort( ( a, b ) => b.localeCompare( a, undefined, {
                            numeric    : true,
                            sensitivity: 'base',
                        } ) ) // reverse the order
                        .map( ( version, index ) => (
                            <div key={index}
                                 className={`wpr-version-wrap ${rollbackVersion === version ? 'wpr-active-row' : ''}`}>
                                <div className={'wpr-version-radio-wrap'}>
                                    <label htmlFor={'version-' + index}>
                                        <input id={'version-' + index} type={'radio'} name={'version'}
                                               value={version}
                                               checked={rollbackVersion === version}
                                               onChange={() => setIsRollbackVersion( version )} // Add this line
                                        />
                                        <span className={'wpr-version-lineitem'}>{version}</span>
                                        {( queryArgs.current_version === version ) && ( version !== 'trunk' ) && (
                                            <span
                                                className={'wpr-version-lineitem-current'}>{__( 'Currently Installed', 'wp-rollback' )}</span>
                                        )}

                                    </label>
                                </div>
                            </div>
                        ) )
                    }
                </div>

                <div className={'wpr-button-wrap'}>
                    <Button isPrimary onClick={openConfirmModal}
                            className={'wpr-button-submit'}>{__( 'Rollback', 'wp-rollback' )}</Button>
                    <Button isSecondary onClick={() => window.location.href = referrer}
                            className={'wpr-button-cancel'}>{__( 'Cancel', 'wp-rollback' )}</Button>
                </div>

                {isChangelogModalOpen && (
                    <Modal
                        title={__( 'Plugin Changelog', 'wp-rollback' )}
                        onRequestClose={closeChangelogModal}
                        disabled={( rollbackVersion === false )}
                        className={'wpr-modal wpr-modal__changelog'}
                        icon={<Dashicon icon="hammer"/>}
                    >
                        <div className={'wpr-modal-intro'} dangerouslySetInnerHTML={{
                            __html: rollbackInfo.sections.changelog,
                        }}></div>
                    </Modal>
                )}

                {isConfirmModalOpen && (
                    <Modal
                        title={__( 'Are you sure you want to proceed?', 'wp-rollback' )}
                        onRequestClose={closeConfirmModal}
                        disabled={( rollbackVersion === false )}
                        className={'wpr-modal'}
                        icon={<Dashicon icon="warning"/>}
                    >
                        <p className={'wpr-modal-intro'} dangerouslySetInnerHTML={{
                            __html: sprintf(
                                // Translators: %1$s: Plugin name, %2$s: Rollback version
                                __( 'You are about to rollback %1$s to version %2$s. Please confirm you would like to proceed.', 'wp-rollback' ),
                                `<strong>${rollbackInfo.name}</strong>`,
                                `<strong>${rollbackVersion}</strong>`,
                            ),
                        }}></p>

                        <div className="rollback-details">
                            <table className="widefat">
                                <tbody>
                                <tr>
                                    <td className="row-title">
                                        <label
                                            htmlFor="tablecell">{queryArgs.type === 'plugin' ? __( 'Plugin Name:', 'wp-rollback' ) : __( 'Theme Name:', 'wp-rollback' )}
                                        </label>
                                    </td>
                                    <td><span className="wpr-plugin-name">{rollbackInfo.name}</span></td>
                                </tr>
                                <tr className="alternate">
                                    <td className="row-title">
                                        <label htmlFor="tablecell">{__( 'Installed Version:', 'wp-rollback' )}</label>
                                    </td>
                                    <td><span
                                        className="wpr-installed-version">{queryArgs.current_version}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td className="row-title">
                                        <label htmlFor="tablecell">{__( 'New Version:', 'wp-rollback' )}</label>
                                    </td>
                                    <td><span className="wpr-new-version">{rollbackVersion}</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div className={'wpr-modal-notice notice notice-warning'}
                             dangerouslySetInnerHTML={{ __html: __( '<p><strong>Notice:</strong> We strongly recommend you <strong>create a complete backup</strong> of your WordPress files and database prior to performing a rollback. We are not responsible for any misuse, deletions, white screens, fatal errors, or any other issue resulting from the use of this plugin.</p>', 'wp-rollback' ) }}/>

                        <form name="check_for_rollbacks" className="rollback-form" action={adminUrl}>
                            <input type="hidden" name="page" value="wp-rollback"/>
                            <input type="hidden" name="wpr_rollback_nonce" value={wprData.rollback_nonce}/>
                            <input type="hidden" name="_wpnonce" value={wprData.rollback_nonce}/>

                            {queryArgs.type === 'plugin' && (
                                <div>
                                    <input type="hidden" name="plugin_file" value={queryArgs.plugin_file}/>
                                    <input type="hidden" name="plugin_version" value={rollbackVersion}/>
                                    <input type="hidden" name="plugin_slug" value={rollbackInfo.slug}/>
                                </div>
                            )}
                            {queryArgs.type === 'theme' && (
                                <div>
                                    <input type="hidden" name="theme_file" value={queryArgs.theme_file}/>
                                    <input type="hidden" name="theme_version" value={rollbackVersion}/>
                                </div>
                            )}

                            <input type="hidden" name="rollback_name" value={queryArgs.rollback_name}/>
                            <input type="hidden" name="installed_version" value={queryArgs.current_version}/>

                            <div className={'wpr-modal-button-wrap'}>
                                <Button isPrimary type={'submit'}>{__( 'Rollback', 'wp-rollback' )}</Button>
                                <Button isSecondary onClick={closeConfirmModal}
                                        className={'wpr-button-cancel'}>{__( 'Cancel', 'wp-rollback' )}</Button>
                            </div>
                        </form>


                    </Modal>
                )}

            </div>
        </div>

    );

};

domReady( function() {
    if ( document.getElementById( 'root-wp-rollback-admin' ) ) {
        render( <AdminPage/>, document.getElementById( 'root-wp-rollback-admin' ) );
    }
} );
