import { useState } from '@wordpress/element';
import { Dashicon, Popover } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

function TrunkPopover() {
    const [ isPopoverVisible, setIsPopoverVisible ] = useState( false );

    const showPopover = () => {
        setIsPopoverVisible(true);
    };

    const hidePopover = () => {
        setIsPopoverVisible(false);
    };

    return (
        <div className={'wpr-popover-wrap'}>
            <Dashicon icon={'info'} onMouseEnter={showPopover}  onMouseLeave={hidePopover}  />
            {isPopoverVisible && (
                <Popover position={'top'} className={'wpr-popover'} variant={'unstyled'} onClose={hidePopover} noArrow={false}>
                    {__( 'Trunk is where the most current revisions of the code should be stored, often representing the development version of the software. This is particularly relevant for developers and testers who are working with the very latest code changes that have not yet been released to the public.', 'wp-rollback' )}
                </Popover>
            )}
        </div>
    );
}

export default TrunkPopover;
