<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/15/2018
 * Time: 5:08 PM
 */

namespace Wiki\KPayment\Model\Config;


class MCard implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'Y', 'label' => __('Want to know the transaction’s card type ')],
            ['value' => 'N', 'label' => __('Do not want')],
        ];
    }
}