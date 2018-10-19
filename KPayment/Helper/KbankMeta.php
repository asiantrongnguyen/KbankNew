<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/19/2018
 * Time: 2:16 PM
 */

namespace Marvelic\KPayment\Helper;

use Marvelic\KPayment\Model\TokenFactory;
use Marvelic\KPayment\Model\MetaFactory;
class KbankMeta
{
    protected $objTokenFactory, $objMetaFactory;

    public function __construct(TokenFactory $objTokenFactory, MetaFactory $objMetaFactory) {
        $this->objTokenFactory = $objTokenFactory;
        $this->objMetaFactory  = $objMetaFactory;
    }

    //Save the Payment getaway response into p2c2p_meta table using the current payment order_id.
    public function savePaymentGetawayResponse($request,$user_id) {

        //Extract the Payment getaway resposne object.
        extract($request, EXTR_OVERWRITE);

        $model = $this->objMetaFactory->create();

        $metaData = $this->getP2c2pIdByOrderId($order_id);
        $p2c2p_id = count($metaData) ? $metaData->getData()[0] : NULL;

        $model->setData('p2c2p_id' , $p2c2p_id['p2c2p_id']);
        $model->setData('order_id' , array_key_exists('order_id',$request) ? $order_id : '' );
        $model->setData('user_id' , $user_id);
        $model->setData('version' , array_key_exists('version',$request) ? $version : '' );
        $model->setData('request_timestamp' , array_key_exists('request_timestamp',$request) ? $request_timestamp : '' );
        $model->setData('merchant_id' , array_key_exists('merchant_id',$request) ? $merchant_id : '' );
        $model->setData('invoice_no' , array_key_exists('invoice_no',$request) ? $invoice_no : '' );
        $model->setData('currency' , array_key_exists('currency',$request) ? $currency : '' );
        $model->setData('amount' , array_key_exists('amount',$request) ? $amount : '' );
        $model->setData('transaction_ref' , array_key_exists('transaction_ref',$request) ? $transaction_ref : '' );
        $model->setData('approval_code' , array_key_exists('approval_code',$request) ? $approval_code : '' );
        $model->setData('eci' , array_key_exists('eci',$request) ? $request['eci'] : '' );
        $model->setData('transaction_datetime' , array_key_exists('transaction_datetime',$request) ? $transaction_datetime : '' );
        $model->setData('payment_channel' , array_key_exists('payment_channel',$request) ? $payment_channel : '' );
        $model->setData('payment_status' , array_key_exists('payment_status',$request) ? $payment_status : '' );
        $model->setData('channel_response_code' , array_key_exists('channel_response_code',$request) ? $channel_response_code : '' );
        $model->setData('channel_response_desc' , array_key_exists('channel_response_desc',$request) ? $channel_response_desc : '' );
        $model->setData('masked_pan' , array_key_exists('masked_pan',$request) ? $masked_pan : '' );
        $model->setData('stored_card_unique_id' , array_key_exists('stored_card_unique_id',$request) ? $stored_card_unique_id : '' );
        $model->setData('backend_invoice' , array_key_exists('backend_invoice',$request) ? $backend_invoice : '' );
        $model->setData('paid_channel' ,  array_key_exists('paid_channel',$request) ? $paid_channel : '' );
        $model->setData('paid_agent' , array_key_exists('paid_agent',$request) ? $paid_agent : '' );
        $model->setData('recurring_unique_id' , array_key_exists('recurring_unique_id',$request) ? $recurring_unique_id : '' );
        $model->setData('user_defined_1' , array_key_exists('user_defined_1',$request) ? $user_defined_1 : '' );
        $model->setData('user_defined_2' , array_key_exists('user_defined_2',$request) ? $user_defined_2 : '' );
        $model->setData('user_defined_3' , array_key_exists('user_defined_3',$request) ? $user_defined_3 : '' );
        $model->setData('user_defined_4' , array_key_exists('user_defined_4',$request) ? $user_defined_4 : '' );
        $model->setData('user_defined_5' , array_key_exists('user_defined_5',$request) ? $user_defined_5 : '' );
        $model->setData('browser_info' , array_key_exists('browser_info',$request) ? $browser_info : '' );
        $model->setData('ippPeriod' , array_key_exists('ippPeriod',$request) ? $ippPeriod : '' );
        $model->setData('ippInterestType' , array_key_exists('ippInterestType',$request) ? $ippInterestType : '' );
        $model->setData('ippInterestRate' , array_key_exists('ippInterestRate',$request) ? $ippInterestRate : '' );
        $model->setData('ippMerchantAbsorbRate' , array_key_exists('ippMerchantAbsorbRate',$request) ? $ippMerchantAbsorbRate : '' );

        $model->save();
    }

    // save the logged in user token into p2c2p_token table for stored card payment getaway.
    public function saveUserToken($arrayTokenData) {
        $model = $this->objTokenFactory->create();

        $model->setData($arrayTokenData);
        $model->save();
    }

    // get the logged in customer token data from p2c2p_token table.
    public function getUserToken($intUserId) {
        if(empty($intUserId))
            return NULL;

        return $this->objTokenFactory->create()->getCollection()->addFieldToFilter('user_id',$intUserId);
    }

    // get the token detail passing the token key
    public function getTokenByTokenId($intTokenId) {
        if(!isset($intTokenId))
            return;

        return $this->objTokenFactory->create()->load($intTokenId);
    }

    // get the token detail passing the token key
    public function getP2c2pIdByOrderId($order_id) {
        if(empty($order_id))
            return NULL;

        return $this->objMetaFactory->create()->getCollection()->addFieldToFilter('order_id',$order_id);
    }
}