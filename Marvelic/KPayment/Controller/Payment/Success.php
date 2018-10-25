<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/19/2018
 * Time: 2:20 PM
 */

namespace Marvelic\KPayment\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Success extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    public function __construct(Context $context, PageFactory $resultPageFactory) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute() {
        return $this->resultPageFactory->create();
    }
}