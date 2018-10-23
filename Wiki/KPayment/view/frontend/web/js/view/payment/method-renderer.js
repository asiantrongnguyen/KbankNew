define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'kpayment',
                component: 'Wiki_KPayment/js/view/payment/method-renderer/KPayment'
            }
        );
        return Component.extend({});
    }
);