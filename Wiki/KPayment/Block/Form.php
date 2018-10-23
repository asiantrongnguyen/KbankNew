<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/16/2018
 * Time: 3:29 PM
 */

namespace Wiki\KPayment\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Magento\Customer\Model\Session as customerSession;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\StoreManagerInterface;
use Wiki\KPayment\Helper\KbankHash;

class Form extends \Magento\Framework\View\Element\Template
{
    protected $objOrder;
    protected $objCustomerSession;
    protected $objStoreManagerInterface;

    public function __construct(Context $context, Order $order, customerSession $customerSession, StoreManagerInterface $storeManagerInterface,KbankHash $KbankHash) {

        parent::__construct($context);
        $this->objOrder = $order;
        $this->objCustomerSession = $customerSession;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->kbankHash = $KbankHash;

    }

    public function getResponseParams() {
        return $this->getRequest()->getParams();
    }

    public function getOrderDetails($orderId) {
        return $this->objOrder->loadByIncrementId($orderId);
    }

    public function getCustomerDetail() {
        return $this->objCustomerSession;
    }

    public function getBaseCurrencyCode() {
        return $this->storeManagerInterface->getStore()->getCurrentCurrency()->getCode();
    }
    public function getDescription($code){
        return $this->kbankHash->getResponseDesc($code);
    }
}