import './admin.scss';
import { __, sprintf } from '@wordpress/i18n';
import { Button, Dashicon } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';
import ExpandableText from './ExpandableText';
import RollbackSubmit from './RollbackSubmit';

const ProRollback = ( props ) => {

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
                <div className="wpr-content-header">

                    <div className={'wpr-plugin-info'}>
                        <h2 className={'wpr-plugin-name'}>
                            <a href={props.rollbackInfo.PluginURI} target={'_blank'}
                               className={'wpr-heading-link'}
                               title={sprintf( __( 'View %s', 'wp-rollback' ), props.rollbackInfo.Name )}
                            >
                                {decodeEntities( props.rollbackInfo.Name )}
                                <Dashicon icon="external"/>
                            </a>

                        </h2>

                        {props.rollbackInfo.Description && (
                            <div className={'wpr-pro-description'}>
                                <p className={'wpr-pro-content-html'}
                                      dangerouslySetInnerHTML={{
                                          __html: props.rollbackInfo.Description,
                                      }}>
                                         </p>
                            </div>
                        )}

                        <div className={'wpr-pill-wrap'}>
                            <div className={'wpr-pill wpr-pill__black'}><span
                                className={'wpr-pill-text'}>{__( 'Installed version:', 'wp-rollback' )}{' '}
                                <strong>{props.rollbackInfo.Version}</strong></span></div>

                            {props.rollbackInfo.type === 'plugin' && (
                                <div className={'wpr-pill wpr-pill__author'}>
                                    <span className={'wpr-pill-text'}>{__( 'Plugin author:', 'wp-rollback' )}{' '}
                                        <span className={'wpr-pill__link'}
                                              dangerouslySetInnerHTML={{
                                                  __html: props.rollbackInfo.Author,
                                              }}>
                                         </span>
                                    </span>
                                </div>
                            )}
                        </div>

                    </div>

                    <div className={'wpr-meta-wrap'}>

                        {props.rollbackInfo.request.type === 'plugin' && (
                            <div className={'wpr-meta-wrap__plugins'}>
                                <div className={'wpr-view-changelog'}>
                                    <Button isSecondary
                                            className={'wpr-version-changelog'}>{__( 'View Website', 'wp-rollback' )}</Button>
                                </div>
                            </div>
                        )}
                    </div>
                </div>


                <div className={'wpr-versions-container'}>
                    <div className={'wpr-versions'}>
                        {props.rollbackInfo.versions.map( ( version, index ) => {
                            return (

                                <div key={index}
                                     className={`wpr-version-wrap ${props.rollbackVersion === version ? 'wpr-active-row' : ''}`}>
                                    <div className={'wpr-version-radio-wrap'}>
                                        <label htmlFor={'version-' + index}>
                                            <input id={'version-' + index} type={'radio'} name={'version'}
                                                   value={version}
                                                   checked={props.rollbackVersion === version}
                                                   onChange={() => props.setIsRollbackVersion( version )} // Add this line
                                            />
                                            <span className={'wpr-version-lineitem'}>{version}</span>
                                            {( props.queryArgs.current_version === version ) && ( version !== 'trunk' ) && (
                                                <span
                                                    className={'wpr-version-lineitem-current'}>{__( 'Currently Installed', 'wp-rollback' )}</span>
                                            )}

                                        </label>
                                    </div>
                                </div>
                            )
                                ;
                        } )}
                    </div>
                </div>

                <RollbackSubmit rollbackInfo={props.rollbackInfo} queryArgs={props.queryArgs} rollbackVersion={props.rollbackVersion} setIsRollbackVersion={props.setIsRollbackVersion} />

            </div>
        </div>
    );

};

export default ProRollback;
