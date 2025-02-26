(() => {
    "use strict";

    const { decodeEntities } = window.wp.htmlEntities;
    const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
    const { getSetting } = window.wc.wcSettings;
    const { Fragment, createElement } = window.React;
    const { jsx } = window.ReactJSXRuntime;

    const settings = getSetting('noda_pay_data', {});

    const label = decodeEntities(settings.title);

    const Content = () => {
        return jsx(Fragment, {
            children: jsx("div", {
                className: "noda-payment-content",
                children: decodeEntities(settings.description)
            })
        });
    };

    const Label = (props) => {
        const { PaymentMethodLabel } = props.components;
        return createElement(PaymentMethodLabel, {
            text: label
        });
    };

    registerPaymentMethod({
        name: "noda_pay",
        label: jsx(Label, {}),
        content: jsx(Content, {}),
        edit: jsx(Content, {}),
        canMakePayment: () => true,
        ariaLabel: label,
        supports: {
            features: settings.supports
        }
    });
})();
