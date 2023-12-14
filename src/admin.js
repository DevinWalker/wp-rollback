import './admin.scss';
import {Button, Dashicon, Modal, Spinner} from '@wordpress/components';
import {render, useEffect, useState} from '@wordpress/element';
import {__, sprintf} from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';
import {decodeEntities} from '@wordpress/html-entities';
import {getQueryArgs} from '@wordpress/url';

const AdminPage = () => {

    const [isLoading, setIsLoading] = useState(true);
    const [pluginInfo, setPluginInfo] = useState(false);
    const [imageUrl, setImageUrl] = useState(null);
    const currentPluginInfo = getQueryArgs(window.location.search);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [rollbackVersion, setIsRollbackVersion] = useState(currentPluginInfo.current_version);
    const {nonce, adminUrl, referrer} = wprData;

    console.log(referrer);

    const openModal = () => setIsModalOpen(true);
    const closeModal = () => setIsModalOpen(false);

    useEffect(() => {
        // ⚙️ Fetch WP.org API to get plugin data.
        fetch(`https://api.wordpress.org/plugins/info/1.0/${currentPluginInfo.plugin_slug}.json`)
            .then((response) => response.json())
            .then((data) => {
                setPluginInfo(data);
                setIsLoading(false);
            });
    }, []);

    useEffect(() => {
        if (pluginInfo && pluginInfo.slug) {  // Check if pluginInfo is loaded and has a slug
            checkImage(`https://ps.w.org/${pluginInfo.slug}/assets/icon-128x128.png`, (exists) => {
                if (exists) {
                    setImageUrl(`https://ps.w.org/${pluginInfo.slug}/assets/icon-128x128.png`);
                } else {
                    checkImage(`https://ps.w.org/${pluginInfo.slug}/assets/icon-128x128.jpg`, (exists) => {
                        if (exists) {
                            setImageUrl(`https://ps.w.org/${pluginInfo.slug}/assets/icon-128x128.jpg`);
                        } else {
                            setImageUrl('https://i.imgur.com/XqQZQZb.png');
                        }
                    });
                }
            });
        }
    }, [pluginInfo]);


    function checkImage(url, callback) {
        var img = new Image();
        img.onload = () => callback(true);
        img.onerror = () => callback(false);
        img.src = url;
    }

    if (isLoading) {
        return (
            <div id={`wpr-wrap`} className={`wpr-wrap`}>
                <div className={'wpr-loading-content'}>
                    <div className={'wpr-loading-text'}>
                        <Spinner
                            style={{
                                height: 'calc(4px * 20)',
                                width: 'calc(4px * 20)',
                            }}
                        />
                        <p>{__('Loading...', 'wp-rollback')}</p>
                    </div>
                </div>
            </div>
        );
    }

    // output error message if one is found in the API response
    if (pluginInfo.error) {
        return (
            <div id={`wpr-wrap`} className={`wpr-wrap`}>
                <p>{pluginInfo.error}</p>
            </div>
        );
    }

    function getTimeAgo(dateString) {

        // Convert to 24-hour format and remove 'GMT'
        let adjustedDateString = dateString.replace('am', ' AM').replace('pm', ' PM').replace(' GMT', '');
        adjustedDateString = new Date(adjustedDateString).toLocaleString("en-US", {timeZone: "GMT"});

        const date = new Date(adjustedDateString);
        if (isNaN(date.getTime())) {
            console.error('Invalid date:', adjustedDateString);
            return 'Invalid date';
        }

        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) {
            return `${diffInSeconds} seconds ago`;
        } else if (diffInSeconds < 3600) {
            return `${Math.floor(diffInSeconds / 60)} minutes ago`;
        } else if (diffInSeconds < 86400) {
            return `${Math.floor(diffInSeconds / 3600)} hours ago`;
        } else if (diffInSeconds < 2592000) { // 30 days
            return `${Math.floor(diffInSeconds / 86400)} days ago`;
        } else if (diffInSeconds < 31536000) { // 365 days
            return `${Math.floor(diffInSeconds / 2592000)} months ago`;
        } else {
            return `${Math.floor(diffInSeconds / 31536000)} years ago`;
        }
    }

    return (
        <div className={'wpr-wrapper'}>
            <div className={'wpr-logo-wrap'}>
                <h1>{__('WP Rollback', 'wp-rollback')}</h1>
                <p className={'wpr-intro-text'}>{__('Please select which plugin version you would like to rollback to from the release versions listed below.', '')}</p>
            </div>
            <div className="wpr-content-wrap">

                <div className="wpr-content-header">
                    {imageUrl && <img src={imageUrl} width={64} height={64} className={'wpr-plugin-avatar'}
                                      alt={pluginInfo.name} />}

                    <div className={'wpr-plugin-info'}>
                        <h2 className={'wpr-plugin-name'}>{decodeEntities(pluginInfo.name)}</h2>
                        <div className={'wpr-pill'}><span
                            className={'wpr-pill-text'}>{__('Installed version:', 'wp-rollback')}{' '}
                            <strong>{pluginInfo.version}</strong></span></div>
                    </div>

                    <div className={'wpr-last-updated'}>
                        <h3>Last Updated</h3>
                        <div className={'wpr-updater-wrap'}>
                            <div className={'wpr-updater-info'}>
                                <span className={'wpr-plugin-lastupdate'}>{getTimeAgo(pluginInfo.last_updated)}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div className={'wpr-versions-container'}>
                    {Object.keys(pluginInfo.versions)
                           .filter(version => version !== 'trunk') // remove 'trunk'
                           .sort((a, b) => b.localeCompare(a, undefined, {
                               numeric: true,
                               sensitivity: 'base',
                           })) // reverse the order
                           .map((version, index) => (
                               <div key={index} className={'wpr-version-wrap'}>
                                   <div className={'wpr-version-radio-wrap'}>
                                       <label htmlFor={'version-' + index}>
                                           <input id={'version-' + index} type={'radio'} name={'version'}
                                                  value={version}
                                                  checked={rollbackVersion === version}
                                                  onChange={() => setIsRollbackVersion(version)} // Add this line
                                           />
                                           <span className={'wpr-version-lineitem'}>{version}</span>
                                           {(currentPluginInfo.current_version === version) && (version !== 'trunk') && (
                                               <span
                                                   className={'wpr-version-lineitem-current'}>{__('Currently Installed', 'wp-rollback')}</span>
                                           )}
                                       </label>
                                   </div>
                               </div>
                           ))
                    }
                </div>

                <div className={'wpr-button-wrap'}>
                    <Button isPrimary onClick={openModal}
                            className={'wpr-button-submit'}>{__('Rollback', 'wp-rollback')}</Button>
                    <Button isSecondary onClick={() => window.location.href = referrer}
                            className={'wpr-button-cancel'}>{__('Cancel', 'wp-rollback')}</Button>

                </div>

                {isModalOpen && (

                    <Modal
                        title={`Are you sure you want to proceed?`}
                        onRequestClose={closeModal}
                        disabled={(rollbackVersion === false)}
                        className={'wpr-modal'}
                        icon={<Dashicon icon="warning" />}
                    >

                        <p className={'wpr-modal-intro'} dangerouslySetInnerHTML={{
                            __html: sprintf(
                                // Translators: %1$s: Plugin name, %2$s: Rollback version
                                __('You are about to rollback %1$s to version %2$s. Please confirm you would like to proceed.', 'wp-rollback'),
                                `<strong>${pluginInfo.name}</strong>`,
                                `<strong>${rollbackVersion}</strong>`,
                            ),
                        }}></p>

                        <div className="rollback-details">
                            <table className="widefat">
                                <tbody>
                                <tr>
                                    <td className="row-title">
                                        <label htmlFor="tablecell">{__('Plugin Name:', 'wp-rollback')}</label>
                                    </td>
                                    <td><span className="wpr-plugin-name">{pluginInfo.name}</span></td>
                                </tr>
                                <tr className="alternate">
                                    <td className="row-title">
                                        <label htmlFor="tablecell">{__('Installed Version:', 'wp-rollback')}</label>
                                    </td>
                                    <td><span
                                        className="wpr-installed-version">{currentPluginInfo.current_version}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td className="row-title">
                                        <label htmlFor="tablecell">{__('New Version:', 'wp-rollback')}</label>
                                    </td>
                                    <td><span className="wpr-new-version">{rollbackVersion}</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div className={'wpr-modal-notice notice notice-warning'}
                             dangerouslySetInnerHTML={{__html: __('<p><strong>Notice:</strong> We strongly recommend you <strong>create a complete backup</strong> of your WordPress files and database prior to performing a rollback. We are not responsible for any misuse, deletions, white screens, fatal errors, or any other issue resulting from the use of this plugin.</p>', 'wp-rollback')}} />


                        <form name="check_for_rollbacks" className="rollback-form" action={adminUrl}>
                            <input type="hidden" name="page" value="wp-rollback" />
                            <input type="hidden" name="wpr_rollback_nonce" value={nonce} />
                            <input type="hidden" name="_wpnonce" value={nonce} />
                            <input type="hidden" name="plugin_file" value={currentPluginInfo.plugin_file} />
                            <input type="hidden" name="plugin_version" value={rollbackVersion} />
                            <input type="hidden" name="rollback_name" value={currentPluginInfo.rollback_name} />
                            <input type="hidden" name="installed_version" value={currentPluginInfo.current_version} />

                            <input type="hidden" name="plugin_slug" value={pluginInfo.slug} />
                            <div className={'wpr-modal-button-wrap'}>
                                <Button isPrimary type={'submit'}>{__('Rollback', 'wp-rollback')}</Button>
                                <Button isSecondary onClick={closeModal} className={'wpr-button-cancel'}>{__('Cancel', 'wp-rollback')}</Button>
                            </div>
                        </form>


                    </Modal>
                )}

            </div>
        </div>

    );


};


domReady(function () {
    if (document.getElementById('root-wp-rollback-admin')) {
        render(<AdminPage />, document.getElementById('root-wp-rollback-admin'));
    }
});
