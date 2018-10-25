<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/19/2018
 * Time: 2:36 PM
 */

namespace Wiki\KPayment\Controller\Payment;


class Request extends \Wiki\KPayment\Controller\AbstractCheckoutRedirectAction
{
    public function execute()
    {
        //Get current order detail from OrderFactory object.
        $orderId = $this->getCheckoutSession()->getLastRealOrderId();

        if(empty($orderId)) {
            $this->_redirect('');
            return;
        }

        $order = $this->getOrderDetailByOrderId($orderId);

        //Redirect to home page with error
        if(!isset($order)) {
            $this->_redirect('');
            return;
        }

        $objCatalogSessionHelper = $this->getCatalogSession();
        $tokenId = $objCatalogSessionHelper->getTokenValue();
        $strTokenKey = '';

        if(isset($tokenId)) {
            $objMetaHelper = $this->getMetaDataHelper();
            $strTokenKey = $objMetaHelper->getTokenByTokenId($tokenId)->getData('stored_card_unique_id');
        }


        $customerSession = $this->getCustomerSession();
        //Get the selected product name from the OrderFactory object.

        $product_name = '';
        foreach($order->getAllItems() as $item) {
            $product_name .= $item->getName() . ', ';
        }

        $product_name = (strlen($product_name) > 0) ? substr($product_name, 0, strlen($product_name) - 2) : "";
        $product_name .= '.';
        $product_name = mb_strimwidth($product_name, 0, 255, '...');

        //Check whether customer is logged in or not into current merchant website.
        if($customerSession->isLoggedIn()) {
            $cust_email = $customerSession->getCustomer()->getEmail();
        } else {
            $billingAddress = $order->getBillingAddress();
            $cust_email = $billingAddress->getEmail();
        }

        //Create basic form array.
        $fun2c2p_args = array(
            'description'   => $product_name,
            'order_id'              => $this->getCheckoutSession()->getLastRealOrderId(),
            'invoice_no'            => $this->getCheckoutSession()->getLastRealOrderId(),
            'amount'                => round($order->getGrandTotal(),2),
            'customer_email'        => $cust_email,
            'store_id'	=> !empty($strTokenKey) ? $strTokenKey : '',
        );

        echo $this->getKbankRequest($fun2c2p_args,$customerSession->isLoggedIn());
    }
}