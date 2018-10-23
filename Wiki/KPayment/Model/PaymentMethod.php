<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/16/2018
 * Time: 3:42 PM
 */

namespace Wiki\KPayment\Model;


class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = 'kpayment';
    protected $_isInitializeNeeded = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;

    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $catalogSession = $objectManager->create('\Magento\Catalog\Model\Session');

        if(isset($data)) {
            if(!empty($data->getData()['additional_data'])) {
                $catalogSession->setTokenValue($data->getData()['additional_data']['test1']);
            }
        }

        return $this;
    }
}