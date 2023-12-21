import {Dashicon} from '@wordpress/components';
import {useState} from '@wordpress/element';

const ExpandableText = ({ text }) => {
    const [isExpanded, setIsExpanded] = useState(false);

    const toggleExpanded = () => {
        setIsExpanded(!isExpanded);
    };

    // Check if the text length is greater than or equal to 200
    const isLongText = text.length >= 200;

    return (
        <div className="wpr-theme-description">
            <p>
                {isExpanded || !isLongText ? text : `${text.substring(0, 200)}...`}

                {isLongText && (
                    <span className={'wpr-expand-text'} onClick={toggleExpanded}>
                        {isExpanded ? (
                            <span>
                                <Dashicon icon={'arrow-up'} />
                                {' read less'}
                            </span>
                        ) : (
                            <span>
                                <Dashicon icon={'arrow-down'} />
                                {' read more'}
                            </span>
                        )}
                    </span>
                )}
            </p>
        </div>
    );
};


export default ExpandableText;
