<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/15/2018
 * Time: 5:09 PM
 */

namespace Marvelic\KPayment\Model\Config;


class Mode implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => "live", 'label' => __('Live Mode')],
            ['value' => "test", 'label' => __('Test Mode')],
        ];
    }
}