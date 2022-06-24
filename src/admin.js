import './admin.scss';
import {Icon} from '@wordpress/components';
import {
    Fragment,
    render,
    Component,
} from '@wordpress/element';
import {__} from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';


const AdminPage = () => {

    return (
        <p>Hello</p>
    )


}


domReady(function () {
    if (document.getElementById('root-wp-rollback-admin')) {
        render(<AdminPage/>, document.getElementById('root-wp-rollback-admin'));
    }
});
