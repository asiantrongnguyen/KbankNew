<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/19/2018
 * Time: 2:14 PM
 */

namespace Marvelic\KPayment\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;
use Magento\Catalog\Model\Session as catalogSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session as Customer;
use Marvelic\KPayment\Controller\AbstractCheckoutAction;
use Marvelic\KPayment\Helper\Checkout;
use Marvelic\KPayment\Helper\KbankRequest;
use Marvelic\KPayment\Helper\KbankMeta;
use Marvelic\KPayment\Helper\KbankHash;

abstract class AbstractCheckoutRedirectAction extends AbstractCheckoutAction
{
    protected $objCheckoutHelper, $objCustomer;
    protected $objKbankRequestHelper, $objKbankMetaHelper;
    protected $objKbankHashHelper, $objConfigSettings;
    protected $objCatalogSession;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        Customer $customer,
        Checkout $checkoutHelper,
        KbankRequest $KbankRequest,
        KbankMeta $KbankMeta,
        KbankHash $KbankHash,
        ScopeConfigInterface $configSettings,
        catalogSession $catalogSession)
    {
        parent::__construct($context, $checkoutSession, $orderFactory);
        $this->objCheckoutHelper = $checkoutHelper;
        $this->objCustomer = $customer;
        $this->objKbankRequestHelper = $KbankRequest;
        $this->objKbankMetaHelper = $KbankMeta;
        $this->objKbankHashHelper = $KbankHash;
        $this->objConfigSettings = $configSettings->getValue('payment/kpayment');
        $this->objCatalogSession = $catalogSession;
    }

    //This object is hold the custom filed data for payment method like selected store Card's, other setting, etc.
    protected function getCatalogSession()
    {
        return $this->objCatalogSession;
    }

    //Get the Magento configuration setting object that hold global setting for Merchant configuration
    protected function getConfigSettings()
    {
        return $this->objConfigSettings;
    }

    //Get the Kbank plugin Hash helper class object to check hash value is valid or not. Also generate the hash for any request.
    protected function getHashHelper()
    {
        return $this->objKbankHashHelper;
    }

    //Get the Meta helper object. It is responsible for storing the data into database. like Kbank_meta, Kbank_token table.
    protected function getMetaDataHelper()
    {
        return $this->objKbankMetaHelper;
    }

    //Get the Kbank request helper class. It is responsible for construct the current user request for 2c2p Payment Gateway.
    protected function getKbankRequest($paramter, $isloggedIn)
    {
        return $this->objKbankRequestHelper->Kbank_construct_request($paramter, $isloggedIn);
    }

    //This is magento object to get the customer object.
    protected function getCustomerSession()
    {
        return $this->objCustomer;
    }

    //Get the Kbank cehckout object. It is reponsible for hold the current users cart detail's
    protected function getCheckoutHelper()
    {
        return $this->objCheckoutHelper;
    }

    //This function is used to redirect to customer message action method after make successfully payment Kbank payment type.
    protected function executeSuccessAction($request)
    {
        if ($this->getCheckoutSession()->getLastRealOrderId()) {
            $this->_forward('success', 'payment', 'kpayment', $request);
        }
    }

    //This function is redirect to cart after customer is cancel the payment.
    protected function executeCancelAction()
    {
        $this->getCheckoutHelper()->cancelCurrentOrder('');
        $this->getCheckoutHelper()->restoreQuote();
        $this->redirectToCheckoutCart();
    }
}