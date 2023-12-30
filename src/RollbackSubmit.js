import './admin.scss';
import { __, sprintf } from '@wordpress/i18n';
import { Button, Dashicon, Modal } from '@wordpress/components';
import { render, useEffect, useState } from '@wordpress/element';
import ConfirmModal from './ConfirmModal';

const RollbackSubmit = ( props ) => {

    const { referrer } = wprData;

    const [ isConfirmModalOpen, setIsConfirmModalOpen ] = useState( false );
    const openConfirmModal = () => setIsConfirmModalOpen( true );
    const closeConfirmModal = () => setIsConfirmModalOpen( false );

    return (
        <div className={'wpr-button-wrap'}>
            <Button isPrimary onClick={openConfirmModal}
                    className={'wpr-button-submit'}>{__( 'Rollback', 'wp-rollback' )}</Button>
            <Button isSecondary onClick={() => window.location.href = referrer}
                    className={'wpr-button-cancel'}>{__( 'Cancel', 'wp-rollback' )}</Button>

            {isConfirmModalOpen && (
                <ConfirmModal rollbackInfo={props.rollbackInfo} queryArgs={props.queryArgs} openConfirmModal={openConfirmModal} closeConfirmModal={closeConfirmModal} rollbackVersion={props.rollbackVersion}/>
            )}

        </div>
    )

};

export default RollbackSubmit;
