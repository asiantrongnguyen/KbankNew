<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/16/2018
 * Time: 5:54 PM
 */

namespace Marvelic\KPayment\Model;

use Magento\Framework\Model\AbstractModel;

class Meta extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Marvelic\KPayment\Model\ResourceModel\Meta');
    }
}