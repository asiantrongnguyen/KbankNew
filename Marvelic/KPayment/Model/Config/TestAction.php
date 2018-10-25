<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/16/2018
 * Time: 12:02 PM
 */

namespace Marvelic\KPayment\Model\Config;


class TestAction implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'https://uatkpgw.kasikornbank.com/pgpayment/payment.aspx', 'label' => __('VISA, MasterCard')],
            ['value' => 'https://uatkpgw.kasikornbank.com/pggroup/payment.aspx', 'label' => __('VISA, MasterCard, CUP, JCB')],
            ['value' => 'https://uatkpgw.kasikornbank.com/mobilepay/payment.aspx', 'label' => __('Mobile Page')],
        ];
    }
}