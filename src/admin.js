import './admin.scss';
import {Spinner} from '@wordpress/components';
import {render, useEffect, useState} from '@wordpress/element';
import {__} from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';
import {decodeEntities} from '@wordpress/html-entities';
import {getQueryArgs} from '@wordpress/url';

const AdminPage = () => {

    const [isLoading, setIsLoading] = useState(true);
    const [pluginInfo, setPluginInfo] = useState(false);
    const [imageUrl, setImageUrl] = useState(null);
    const currentPluginInfo = getQueryArgs(window.location.search);

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
                        <Spinner />
                        <p>{__('Loading Plugin Data', 'wp-rollback')}</p>
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

    console.log(pluginInfo);

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
                                       <input id={'version-' + index} type={'radio'} name={'version'} value={version} />
                                       <span className={'wpr-version-lineitem'}>{version}</span>
                                       {(pluginInfo.version === version) && (version !== 'trunk') && (
                                           <span  className={'wpr-version-lineitem-current'}>Currently Installed</span>
                                       )}
                                   </label>
                               </div>
                           </div>
                       ))
                }

                <div className={'wpr-button-wrap'}>
                    <button className={'button button-primary'}>{__('Rollback', 'wp-rollback')}</button>
                </div>

            </div>
        </div>

    );


};


domReady(function () {
    if (document.getElementById('root-wp-rollback-admin')) {
        render(<AdminPage />, document.getElementById('root-wp-rollback-admin'));
    }
});
