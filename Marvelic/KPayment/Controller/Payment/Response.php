<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/20/2018
 * Time: 12:12 PM
 */

namespace Marvelic\KPayment\Controller\Payment;

use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction;
use Marvelic\KPayment\Controller\AbstractCheckoutRedirectAction;
use Marvelic\KPayment\Helper\Checkout;
use Marvelic\KPayment\Helper\KbankHash;
use Marvelic\KPayment\Helper\KbankMeta;
use Marvelic\KPayment\Helper\KbankRequest;

class Response extends AbstractCheckoutRedirectAction
{
    const PATH_CART = 'checkout/cart';
    const PATH_SUCCESS = 'checkout/onepage/success';

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $session,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\Session $customer,
        \Marvelic\KPayment\Helper\Checkout $checkoutHelper,
        \Marvelic\KPayment\Helper\KbankRequest $KbankRequest,
        \Marvelic\KPayment\Helper\KbankMeta $KbankMeta,
        \Marvelic\KPayment\Helper\KbankHash $KbankHash,
        \Magento\Framework\App\Config\ScopeConfigInterface $configSettings,
        \Magento\Catalog\Model\Session $catalogSession
    ) {
        parent::__construct($context, $session, $orderFactory, $customer, $checkoutHelper, $KbankRequest, $KbankMeta, $KbankHash, $configSettings, $catalogSession);
        $this->session = $session;
    }

    public function execute()
    {
        if (empty($_REQUEST)) {
            $this->messageManager->addSuccess(__('Someting is wrong with your order!'));
            $this->_redirect('');
            return;
        }
        //Extract the Payment getaway resposne object.
        extract($_REQUEST, EXTR_OVERWRITE); // Extract the response from Kbank.

        $reqHash = $this->session->getHashValue();
        $resHash = $_REQUEST['MD5CHECKSUM'];

        $hashHelper = $this->getHashHelper();
        $configHelper = $this->getConfigSettings();
        $objCustomerData = $this->getCustomerSession();

        $isValidHash = $hashHelper->isValidHashValue($reqHash,$resHash);       
        $order = $this->session->getLastRealOrder();

        //Check whether hash value is valid or not If not valid then redirect to home page when hash value is wrong.
//        if (!$isValidHash) {
//            $order->setState(\Magento\Sales\Model\Order::STATUS_FRAUD);
//            $order->setStatus(\Magento\Sales\Model\Order::STATUS_FRAUD);
//            $order->save();
//
//            $this->messageManager->addSuccess(__('Someting is wrong with your order!'));
//
//            $this->_redirect('');
//            return;
//        }
        $payment_status = $_REQUEST['HOSTRESP'];

        //check payment status according to payment response.
        if (strcasecmp($payment_status, "00") == 0) {
            //IF payment status code is success     
			
            // Update order state and status.
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);

            //Set the complete status when payment is completed.
            $order->save();
            $_REQUEST['order_id'] = $order->getRealOrderId();
            $this->executeSuccessAction($_REQUEST);
            return;

        }  else {
            //If payment status code is cancel/Error/other.
            $this->messageManager->addSuccess(__($this->getHashHelper()->getResponseDesc($payment_status)));
            $this->executeCancelAction();
            return;
        }
    }

    /**
     * @param  \Magento\Sales\Model\Order $order
     *
     * @return \Magento\Sales\Api\Data\InvoiceInterface
     */
    protected function invoice(\Magento\Sales\Model\Order $order) {
        return $order->getInvoiceCollection()->getLastItem();
    }
}