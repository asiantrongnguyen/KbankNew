<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/16/2018
 * Time: 5:54 PM
 */

namespace Wiki\KPayment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Meta extends  AbstractDb
{
    protected function _construct()
    {
        $this->_init('kbank_meta', 'kbank_id');
        $this->_isPkAutoIncrement = false;
    }
}