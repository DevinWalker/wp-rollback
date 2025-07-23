import Header from './Header';

/**
 * Layout component that provides common page structure with header
 *
 * @param {Object}      props           Component properties
 * @param {JSX.Element} props.children  Content to render within the layout
 * @param {string}      props.className Additional classes for the content wrapper
 * @return {JSX.Element} The layout component
 */
const Layout = ( { children, className = 'wpr-tools-content' } ) => {
    return (
        <>
            <Header />
            <div className={ className }>{ children }</div>
        </>
    );
};

export default Layout;
