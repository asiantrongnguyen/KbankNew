<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/16/2018
 * Time: 5:56 PM
 */

namespace Marvelic\KPayment\Model\ResourceModel\Meta;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Marvelic\KPayment\Model\Meta', 'Marvelic\KPayment\Model\ResourceModel\Meta');
    }
}