import './admin.scss';
import {Spinner, Icon} from '@wordpress/components';
import {
    Fragment,
    render,
    Component,
} from '@wordpress/element';
import {__} from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';
import {useState, useEffect} from '@wordpress/element';
import {dispatch} from '@wordpress/data';
import axios from 'axios';

const AdminPage = () => {

    const [isLoading, setIsLoading] = useState(true);
    const [pluginInfo, setPluginInfo] = useState(false);

    useEffect(() => {

        // ⚙️ Fetch WP.org API to get plugin data.
        fetch(`https://api.wordpress.org/plugins/info/1.0/give.json`)
            .then((response) => response.json())
            .then((data) => {
                setPluginInfo(data);
                setIsLoading(false);
            });


    }, []);

    const getVersions = (e) => {
        e.preventDefault();

        setIsLoading(true);

        // 🟢 Good to go.
        axios
            .post('/?dfb_donation-block-stripe-action=getStripeIntent', {
                amount: chargeAmount,
                firstName: firstName,
                lastName: lastName,
                email: email,
                paymentIntent,
                currency: props.attributes.currencyCode,
                liveMode: props.attributes.liveMode,
                nonce: window.donationFormBlock.nonce,
            })
            .then(function (response) {
                const data = response.data.data;
                // 🧐 Validation.
                if (data.error) {
                    setIsLoading(false);
                    if (data.message) {
                        setErrorMessage(data.message);
                    } else {
                        setErrorFields(data.fields);
                    }
                } else {
                    setStep(2);
                    // 🤗 Proceed with Stripe.
                    const clientSecret = data.clientSecret;
                    setPaymentIntent(data.paymentIntent);
                    const appearance = {
                        theme: 'stripe',
                        variables: {
                            colorPrimary: props.attributes.color,
                        },
                    };
                    elements.current = stripe.elements({clientSecret, appearance});

                    const paymentElement = elements.current.create('payment', {
                        defaultValues: {
                            billingDetails: {
                                email: email,
                            },
                        },
                    });

                    paymentElement.mount(`.donation-form-payment-intent-${props.attributes.formId}`);
                    paymentElement.on('ready', function (event) {
                        setIsLoading(false);
                    });
                }
            })
            .catch(function (error) {
                console.log(error);
                setStep(1);
                setIsLoading(false);
            });
    };


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

    return (
        <div className={'wpr-wrapper'}>
            <div className={'wpr-logo-wrap'}>
                <h1>{__('WP Rollback', 'wp-rollback')}</h1>
                <p className={'wpr-intro-text'}>{__('Please select which plugin version you would like to rollback to from the releases listed below. You currently have version 2.5.11 installed of Give - Donation Plugin.', '')}</p>
            </div>
            <div className="wpr-content-wrap">

                <div className="wpr-content-header">
                    <img src={`https://ps.w.org/${pluginInfo.slug}/assets/icon-128x128.jpg`} width={64} height={64}
                         alt={'WP Rollback'}
                         className={'wpr-plugin-avatar'} />

                    <div className={'wpr-plugin-info'}>
                        <h2 className={'wpr-plugin-name'}>{pluginInfo.name}</h2>
                        <div className={'wpr-pill'}><span
                            className={'wpr-pill-text'}>{__('Current version:', 'wp-rollback')}{' '}
                            <strong>{pluginInfo.version}</strong></span></div>
                    </div>

                    <div className={'wpr-last-updated'}>
                        <h3>Last Updated</h3>
                        <div className={'wpr-updater-wrap'}>
                            <img src={'https://i.imgur.com/XqQZQZb.png'} width={40} height={40} alt={'WP Rollback'}
                                 className={'wpr-avatar-small'} />
                            <div className={'wpr-updater-info'}>
                                <span className={'wpr-update-user'}>Devin Walker</span>
                                <span className={'wpr-plugin-lastupdate'}>updated 3 hours ago</span>
                            </div>
                        </div>
                    </div>
                </div>

                {Object.keys(pluginInfo.versions).sort(function (a, b) {
                    return a - b;
                }).map((version, index) => {
                        return (
                            <div key={index} className={'wpr-version-wrap'}>
                                <div className={'wpr-version-radio-wrap'}>
                                    <label for={'version-' + index}>
                                        <input id={'version-' + index} type={'radio'} name={'version'} value={version} />
                                        <span>{version}</span>
                                    </label>
                                </div>
                            </div>
                        );
                    },
                )}

            </div>
        </div>

    );


};


domReady(function () {
    if (document.getElementById('root-wp-rollback-admin')) {
        render(<AdminPage />, document.getElementById('root-wp-rollback-admin'));
    }
});
