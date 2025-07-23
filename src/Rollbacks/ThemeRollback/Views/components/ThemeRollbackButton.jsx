import { useUIText } from '@wp-rollback/shared-core/context/UITextContext';

/**
 * Theme rollback button component
 *
 * @param {Object} props           Component properties
 * @param {string} props.theme     Theme slug
 * @param {boolean} props.hasRollback Whether the theme has rollback available
 * @return {JSX.Element} The theme rollback button component
 */
const ThemeRollbackButton = ( { theme, hasRollback } ) => {
    const { rollbackLabel, notRollbackable } = useUIText();

    if ( ! hasRollback ) {
        return (
            <span
                className="no-rollback"
                style={ {
                    position: 'absolute',
                    left: '23px',
                    bottom: '16px',
                    fontSize: '12px',
                    fontStyle: 'italic',
                    color: 'rgb(181, 181, 181)',
                } }
            >
                { notRollbackable }
            </span>
        );
    }

    return (
        <a href={ `tools.php?page=wp-rollback#/rollback/theme/${ theme }` } className="button wpr-theme-rollback">
            { rollbackLabel }
        </a>
    );
};

export default ThemeRollbackButton;
