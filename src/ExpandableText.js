import {Dashicon} from '@wordpress/components';
import {useState} from '@wordpress/element';

const ExpandableText = ({text}) => {
    const [isExpanded, setIsExpanded] = useState(false);

    const toggleExpanded = () => {
        setIsExpanded(!isExpanded);
    };

    return (
        <div className="wpr-theme-description">
            <p>
                {isExpanded ? text : `${text.substring(0, 200)}...`}

                <span className={'wpr-expand-text'} onClick={toggleExpanded}>
                   {isExpanded ? (
                       <span>
                        <Dashicon icon={'arrow-up'} />
                           {' read more'}
                    </span>
                   ) : (
                       <span>
                        <Dashicon icon={'arrow-down'} />
                           {' read less'}
                    </span>
                   )}
            </span>

            </p>

        </div>
    );
};

export default ExpandableText;
