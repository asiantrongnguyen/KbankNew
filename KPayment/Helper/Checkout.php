<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/16/2018
 * Time: 3:35 PM
 */

namespace Marvelic\KPayment\Helper;

use Magento\Sales\Model\Order;

class Checkout
{
    protected $_checkoutSession;

    public function __construct(\Magento\Checkout\Model\Session $checkoutSession) {
        $this->_checkoutSession = $checkoutSession;
    }

    protected function getCheckoutSession() {
        return $this->_checkoutSession;
    }

    public function cancelCurrentOrder($comment) {
        $order = $this->getCheckoutSession()->getLastRealOrder();
        if ($order->getId() && $order->getState() != Order::STATE_CANCELED) {
            $order->registerCancellation($comment)->save();
            return true;
        }
        return false;
    }

    public function restoreQuote() {
        return $this->getCheckoutSession()->restoreQuote();
    }
}