<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/20/2018
 * Time: 11:41 PM
 */

namespace Marvelic\KPayment\Controller\Payment;

use Magento\Framework\App\Action\Action;

class Pmgwresp extends Action
{
    protected $_logger;
    protected $_orderFactory;
    protected $objCheckoutHelper;
    protected $_configSettings;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

    protected $invoiceSender;

    protected $objectManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Marvelic\KPayment\Helper\Checkout $checkoutHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $configSettings,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Marvelic\KPayment\Helper\KbankHash $kbankHash,
        \Marvelic\KPayment\Helper\KbankMeta $kbankMeta
    )
    {
        parent::__construct($context);
        $this->_logger = $logger;
        $this->_orderFactory = $orderFactory;
        $this->objCheckoutHelper = $checkoutHelper;
        $this->_configSettings = $configSettings->getValue('payment/kpayment');
        $this->_orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->invoiceSender = $this->_objectManager->get('Magento\Sales\Model\Order\Email\Sender\InvoiceSender');
        $this->objectManager = $objectManager;
        $this->getHashHelper = $kbankHash;
        $this->getMetaHelper = $kbankMeta;
    }

    public function execute()
    {
        $this->_logger->info('request', ['value' => $_REQUEST]); // write log

		//If payment getway response is empty then redirect to home page directory.
		if (empty($_REQUEST)) {
            $this->_redirect('');
            return;
        }

        $info = $this->getHashHelper->getPmgwresp($_REQUEST['PMGWRESP2']);
        $orderId = substr($info['invoice_no'], 3);
        $order = $this->getOrderDetailByOrderId($orderId);
        $this->getMetaHelper->savePaymentGetawayResponse($info, $orderId, $order->getCustomerId());
        //If order is empty then redirect to home page. Because order is not avaialbe.
		if (empty($order)) {
            $this->_redirect('');
            return;
        }
        if (strcasecmp($info['response_code'], "00") == 0) {
			try {
				if ($info['warning_light'] != "R") {
					//IF payment status code is success
					$payment = $order->getPayment();

					$payment->setTransactionId($orderId);
					$payment->setLastTransId($orderId);
					//Set the complete status when payment is completed.
					$order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
					$order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);

					$invoice = $this->invoice($order);
					$invoice->setTransactionId($orderId);

					// Add transaction.
					$payment->addTransactionCommentsToOrder(
						$payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH),
						__(
							'Amount of %1 has been paid via Kbank payment',
							$order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal())
						)
					);
					// $order->save();
				} else {
					//IF payment status code is success
					$payment = $order->getPayment();

					$payment->setTransactionId($orderId);
					$payment->setLastTransId($orderId);
					//Set the complete status when payment is completed.
					$order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
					$order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);

					$invoice = $this->invoice($order);
					$invoice->setTransactionId($orderId);

					// Add fraud comment.
					$payment->addTransactionCommentsToOrder(
						$payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE),
						__(
							'Please check this transaction with KBank. Coz. Fraud status is %1(Red)',
							$info['warning_light']
						)
					);
				}
				$payment_id = $payment->getId();

				$order_id = $order->getId();
				$this->_logger->info('dataId', ['paymentId' => $payment_id, 'orderId' => $order_id]);

				$detailData = [
					\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => [
						'Order Id' => $orderId,
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

				// create invoice

				if($this->_configSettings['auto_invoice'] == 1){
					if ($order->canInvoice()) {
						$invoice = $this->_invoiceService->prepareInvoice($order);
						$invoice->register();
						$invoice->save();
						$transactionSave = $this->_transaction->addObject(
							$invoice
						)->addObject(
							$invoice->getOrder()
						);
						$transactionSave->save();
						$this->invoiceSender->send($invoice);
						//send notification code
						$order->addStatusHistoryComment(
							__('Notified customer about invoice #%1.', $invoice->getId())
						)
							->setIsCustomerNotified(true)
							->save();
					}
				}else {
          $order->save();
        }

			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->_logger->info('Exeption', ["value"=>$e->getMessage()]);
			}
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

    // Get Magento OrderFactory object.
    protected function getOrderFactory()
    {
        return $this->_orderFactory;
    }

    // Get Magento Order object.
    protected function getOrderDetailByOrderId($orderId)
    {

        $order = $this->getOrderFactory()->create()->loadByIncrementId($orderId);

        if (!$order->getId()) {
            return null;
        }

        return $order;
    }

    //Get the cehckout object. It is reponsible for hold the current users cart detail's
    protected function getCheckoutHelper()
    {
        return $this->objCheckoutHelper;
    }

    //This function is redirect to cart after customer is cancel the payment.
    protected function executeCancelAction()
    {
        $this->getCheckoutHelper()->cancelCurrentOrder('');
        $this->getCheckoutHelper()->restoreQuote();
    }

    /** @return string */
    protected function getKbankIp()
    {
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $a */
        $remoteAddress = $om->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
        return $remoteAddress->getRemoteAddress();
    }

    /* @return boolean */
    protected function checkMechant($merchant)
    {
        $confMerchantId = '';
        if ($this->_configSettings['mode'] == 'live') {
            $confMerchantId = $this->_configSettings['merchant'];
        } else {
            $confMerchantId = $this->_configSettings['merchant_test'];
        }
        if (strcasecmp($confMerchantId, $merchant) == 0) {
            return true;
        }
        return false;
    }

    /*@return boolean */

    protected function checkIp($ip_request, $ip_response)
    {
        if (strcasecmp($ip_request, str_replace('X', '', $ip_response)) == 0) {
            return true;
        }
        return false;
    }

    protected function checkAmount($req, $res)
    {
        $amount = round($req, 2);
        if (strcasecmp($this->getAmount($amount), $res) == 0) {
            return true;
        }
        return false;
    }

    /*Get Amount with format length */
    protected function getAmount(string $parameter)
    {
        $result = $parameter;
        while (strlen($result) < 12) {
            $result = "0" . $result;
        }
        return $result;
    }
}
