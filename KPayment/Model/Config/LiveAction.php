<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/15/2018
 * Time: 5:10 PM
 */

namespace Marvelic\KPayment\Model\Config;


class LiveAction implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'https://rt05.kasikornbank.com/pgpayment/payment.aspx', 'label' => __('VISA, MasterCard')],
            ['value' => 'https://rt05.kasikornbank.com/pggroup/payment.aspx', 'label' => __('VISA, MasterCard, CUP, JCB')],
            ['value' => 'https://rt05.kasikornbank.com/mobilepay/payment.aspx', 'label' => __('Mobile Page')],
        ];
    }
}