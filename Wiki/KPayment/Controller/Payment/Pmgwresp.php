<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/20/2018
 * Time: 11:41 PM
 */

namespace Wiki\KPayment\Controller\Payment;

use Magento\Catalog\Model\Session as catalogSession;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\OrderFactory;
use Wiki\KPayment\Controller\AbstractCheckoutRedirectAction;
use Wiki\KPayment\Helper\Checkout;
use Wiki\KPayment\Helper\KbankHash;
use Wiki\KPayment\Helper\KbankMeta;
use Wiki\KPayment\Helper\KbankRequest;
use Psr\Log\LoggerInterface;

class Pmgwresp extends AbstractCheckoutRedirectAction
{
    protected $_logger;

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
        catalogSession $catalogSession,
        LoggerInterface $logger)
    {
        parent::__construct($context, $checkoutSession, $orderFactory, $customer, $checkoutHelper, $KbankRequest, $KbankMeta, $KbankHash, $configSettings, $catalogSession);
        $this->_logger = $logger;
    }

    public function execute()
    {
        $this->_logger->info('request', ['value' => $_REQUEST]); // write log
        $hashHelper = $this->getHashHelper();
        $configHelper = $this->getConfigSettings();
        $objCustomerData = $this->getCustomerSession();
        $checkoutData = $this->getCheckoutSession();
        $order = $checkoutData->getLastRealOrder();

        //If payment getway response is empty then redirect to home page directory.
        if (empty($_REQUEST)) {
            $this->_redirect('');
            return;
        }

        $fraud = strlen($_REQUEST['PMGWRESP2']);

        //If order is empty then redirect to home page. Because order is not avaialbe.
        if(empty($order)) {
            $this->_redirect('');
            return;
        }

        //check response code
        if ($fraud != 816) {
            $this->_logger->info('False hash'); // write log

            $order->setState(\Magento\Sales\Model\Order::STATUS_FRAUD);
            $order->setStatus(\Magento\Sales\Model\Order::STATUS_FRAUD);
            $order->save();

            $this->_redirect('');
            return;
        } else {
            $this->_logger->info('Pass hash'); // write log
        }

        $info = $this->getHashHelper()->getPmgwresp($_REQUEST['PMGWRESP2']);
        $info['order_id'] = $order->getId();
        $metaDataHelper = $this->getMetaDataHelper();

        $metaDataHelper->savePaymentGetawayResponse($info, $order->getCustomerId());

        //check payment status according to payment response.
        if (strcasecmp($info['response_code'], "00") == 0) {
            //IF payment status code is success

            $payment = $order->getPayment();
            $payment->setTransactionId($info['invoice_no']);
            $payment->setLastTransId($info['invoice_no']);

            //Set the complete status when payment is completed.
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);

            $invoice = $this->invoice($order);
            $invoice->setTransactionId($info['invoice_no']);

            // Add transaction.
            $payment->addTransactionCommentsToOrder(
                $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE),
                __(
                    'Amount of %1 has been paid via Kbank payment',
                    $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal())
                )
            );
            $order->save();

            $payment_id = $payment->getId();
            $order_id = $order->getRealOrderId();
            $this->_logger->info('dataId', ['paymentId' => $payment_id, 'orderId' => $order_id]);

            $detailData = [
                \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => [
                    'Order Id' => $order_id,
                    'TransCode' => $info['trans_code'],
                    'Merchant ID' => $info['merchant_id'],
                    'Terminal ID' => $info['terminal_id'],
                    'Shop No' => $info['shop_no'],
                    'Currency Code' => $info['currency_code'],
                    'Invoice No' => $info['invoice_no'],
                    'Date' => $info['date'],
                    'Time' => $info['time'],
                    'Card No' => $info['card_no'],
                    'Expired Date' => $info['expired_date'],
                    'CVV2/ CVC2' => $info['cvv2_cvc2'],
                    'TransAmount' => $info['trans_amount'],
                    'Response Code' => '00 - ' . __('Payment Successful'),
                    'Approval Code' => $info['approval_code'],
                    'Card Type' => $info['card_type'],
                    'FX Rate' => $info['fx_rate'],
                    'THB Amount' => $info['thb_amount'],
                    'Customer Email' => $info['customer_email'],
                    'Description' => $info['description'],
                    'Payer IP Address' => $info['player_ip_address'],
                    'Warning Light' => $info['warning_light'],
                    'Selected Bank' => $info['selected_bank'],
                    'Issuer Bank' => $info['issuer_bank'],
                    'Selected Country' => $info['selected_country'],
                    'IP Country' => $info['ip_country'],
                    'Issuer Country ' => $info['issuer_country'],
                    'ECI' => $info['eci'],
                    'XID' => $info['xid'],
                    'CAVV' => $info['cavv'],
                ]
            ];

            $detailJson = json_encode($detailData);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('sales_payment_transaction');
            $sql = "Update " . $tableName . " Set additional_information = '" . $detailJson . "' where order_id = " . $order_id . " and payment_id = '" . $payment_id . "'";
            $connection->query($sql);

            return;
        } else {
            //If payment status code is cancel/Error/other.
            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
            $order->save();
            return;
        }
    }

    /**
     * @param  \Magento\Sales\Model\Order $order
     *
     * @return \Magento\Sales\Api\Data\InvoiceInterface
     */
    protected function invoice(\Magento\Sales\Model\Order $order)
    {
        return $order->getInvoiceCollection()->getLastItem();
    }


}