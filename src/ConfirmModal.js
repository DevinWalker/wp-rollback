import './admin.scss';
import { __, sprintf } from '@wordpress/i18n';
import { Button, Dashicon, Modal } from '@wordpress/components';

const ConfirmModal = ( props ) => {

    const rollbackName = props.rollbackInfo.name ?? props.rollbackInfo.Name;

    return(
        <Modal
            title={__( 'Are you sure you want to proceed?', 'wp-rollback' )}
            onRequestClose={props.closeConfirmModal}
            disabled={( props.rollbackVersion === false )}
            className={'wpr-modal'}
            icon={<Dashicon icon="warning"/>}
        >
            <p className={'wpr-modal-intro'} dangerouslySetInnerHTML={{
                __html: sprintf(
                    // Translators: %1$s: Plugin name, %2$s: Rollback version
                    __( 'You are about to rollback %1$s to version %2$s. Please confirm you would like to proceed.', 'wp-rollback' ),
                    `<strong>${rollbackName}</strong>`,
                    `<strong>${props.rollbackVersion}</strong>`,
                ),
            }}></p>

            <div className="rollback-details">
                <table className="widefat">
                    <tbody>
                    <tr>
                        <td className="row-title">
                            <label
                                htmlFor="tablecell">{props.queryArgs.type === 'plugin' ? __( 'Plugin Name:', 'wp-rollback' ) : __( 'Theme Name:', 'wp-rollback' )}
                            </label>
                        </td>
                        <td><span className="wpr-plugin-name">{rollbackName}</span></td>
                    </tr>
                    <tr className="alternate">
                        <td className="row-title">
                            <label htmlFor="tablecell">{__( 'Installed Version:', 'wp-rollback' )}</label>
                        </td>
                        <td><span
                            className="wpr-installed-version">{props.queryArgs.current_version}</span>
                        </td>
                    </tr>
                    <tr>
                        <td className="row-title">
                            <label htmlFor="tablecell">{__( 'New Version:', 'wp-rollback' )}</label>
                        </td>
                        <td><span className="wpr-new-version">{props.rollbackVersion}</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div className={'wpr-modal-notice notice notice-warning'}
                 dangerouslySetInnerHTML={{ __html: __( '<p><strong>Notice:</strong> We strongly recommend you <strong>create a complete backup</strong> of your WordPress files and database prior to performing a rollback. We are not responsible for any misuse, deletions, white screens, fatal errors, or any other issue resulting from the use of this plugin.</p>', 'wp-rollback' ) }}/>

            <form name="check_for_rollbacks" className="rollback-form" action={wprData.adminUrl}>
                <input type="hidden" name="page" value="wp-rollback"/>
                <input type="hidden" name="wpr_rollback_nonce" value={wprData.rollback_nonce}/>
                <input type="hidden" name="_wpnonce" value={wprData.rollback_nonce}/>

                {props.queryArgs.type === 'plugin' && (
                    <div>
                        <input type="hidden" name="plugin_file" value={props.queryArgs.plugin_file}/>
                        <input type="hidden" name="plugin_version" value={props.rollbackVersion}/>
                        <input type="hidden" name="plugin_slug" value={props.rollbackInfo.slug}/>
                    </div>
                )}
                {props.queryArgs.type === 'theme' && (
                    <div>
                        <input type="hidden" name="theme_file" value={props.queryArgs.theme_file}/>
                        <input type="hidden" name="theme_version" value={props.rollbackVersion}/>
                    </div>
                )}

                <input type="hidden" name="rollback_name" value={props.queryArgs.rollback_name}/>
                <input type="hidden" name="installed_version" value={props.queryArgs.current_version}/>

                <div className={'wpr-modal-button-wrap'}>
                    <Button isPrimary type={'submit'}>{__( 'Rollback', 'wp-rollback' )}</Button>
                    <Button isSecondary onClick={props.closeConfirmModal}
                            className={'wpr-button-cancel'}>{__( 'Cancel', 'wp-rollback' )}</Button>
                </div>
            </form>


        </Modal>
    )

};

export default ConfirmModal;
