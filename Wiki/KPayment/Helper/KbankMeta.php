<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/19/2018
 * Time: 2:16 PM
 */

namespace Wiki\KPayment\Helper;

use Wiki\KPayment\Model\MetaFactory;
class KbankMeta
{
    protected $objTokenFactory, $objMetaFactory;

    public function __construct(MetaFactory $objMetaFactory) {
        $this->objMetaFactory  = $objMetaFactory;
    }

    //Save the Payment getaway response into Kbank_meta table using the current payment order_id.
    public function savePaymentGetawayResponse($request,$user_id) {

        //Extract the Payment getaway resposne object.
        extract($request, EXTR_OVERWRITE);

        $model = $this->objMetaFactory->create();

        $metaData = $this->getKbankIdByOrderId($request['order_id']);
        $Kbank_id = count($metaData) ? $metaData->getData()[0] : NULL;

        $model->setData('kbank_id' , $Kbank_id['kbank_id']);
        $model->setData('order_id' , array_key_exists('order_id',$request) ? $request['order_id'] : '' );
        $model->setData('user_id' , $user_id);
        $model->setData('trans_code', array_key_exists('trans_code',$request) ? $request['trans_code'] : '' );
        $model->setData('merchant_id', array_key_exists('merchant_id',$request) ? $request['merchant_id'] : '' );
        $model->setData('terminal_id', array_key_exists('terminal_id',$request) ? $request['terminal_id'] : '' );
        $model->setData('shop_no', array_key_exists('shop_no',$request) ? $request['shop_no'] : '' );
        $model->setData('currency_code', array_key_exists('currency_code',$request) ? $request['currency_code'] : '' );
        $model->setData('invoice_no', array_key_exists('invoice_no',$request) ? $request['invoice_no'] : '' );
        $model->setData('date', array_key_exists('date',$request) ? $request['date'] : '' );
        $model->setData('time', array_key_exists('time',$request) ? $request['time'] : '' );
        $model->setData('card_no', array_key_exists('card_no',$request) ? $request['card_no'] : '' );
        $model->setData('expired_date', array_key_exists('expired_date',$request) ? $request['expired_date'] : '' );
        $model->setData('cvv2_cvc2', array_key_exists('cvv2_cvc2',$request) ? $request['cvv2_cvc2'] : '' );
        $model->setData('trans_amount', array_key_exists('trans_amount',$request) ? $request['trans_amount'] : '' );
        $model->setData('response_code', array_key_exists('response_code',$request) ? $request['response_code'] : '' );
        $model->setData('approval_code', array_key_exists('order_id',$request) ? $request['order_id'] : '' );
        $model->setData('card_type', array_key_exists('card_type',$request) ? $request['card_type'] : '' );
        $model->setData('fx_rate', array_key_exists('fx_rate',$request) ? $request['fx_rate'] : '' );
        $model->setData('thb_amount', array_key_exists('thb_amount',$request) ? $request['thb_amount'] : '' );
        $model->setData('customer_email', array_key_exists('customer_email',$request) ? $request['customer_email'] : '' );
        $model->setData('description', array_key_exists('description',$request) ? $request['description'] : '' );
        $model->setData('player_ip_address', array_key_exists('player_ip_address',$request) ? $request['player_ip_address'] : '' );
        $model->setData('warning_light', array_key_exists('warning_light',$request) ? $request['warning_light'] : '' );
        $model->setData('selected_bank', array_key_exists('selected_bank',$request) ? $request['selected_bank'] : '' );
        $model->setData('issuer_bank', array_key_exists('issuer_bank',$request) ? $request['issuer_bank'] : '' );
        $model->setData('selected_country', array_key_exists('selected_country',$request) ? $request['selected_country'] : '' );
        $model->setData('ip_country', array_key_exists('ip_country',$request) ? $request['ip_country'] : '' );
        $model->setData('issuer_country', array_key_exists('issuer_country',$request) ? $request['issuer_country'] : '' );
        $model->setData('eci', array_key_exists('eci',$request) ? $request['eci'] : '' );
        $model->setData('xid', array_key_exists('xid',$request) ? $request['xid'] : '' );
        $model->setData('cavv', array_key_exists('cavv',$request) ? $request['cavv'] : '' );
        $model->save();
    }


    // get the token detail passing the token key
    public function getKbankIdByOrderId($order_id) {
        if(empty($order_id))
            return NULL;

        return $this->objMetaFactory->create()->getCollection()->addFieldToFilter('order_id',$order_id);
    }
}