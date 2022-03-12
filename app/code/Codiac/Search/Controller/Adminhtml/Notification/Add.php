<?php

namespace Codiac\Search\Controller\Adminhtml\Notification;

use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Codiac\Search\Model\ProductNotifications;

class Add extends \Magento\Framework\App\Action\Action
{
    private $session;
    private $_productNotification;

    public function __construct(Context $context, Session $session, ProductNotifications $productNotification)
    {
        $this->session = $session;
        $this->_productNotification = $productNotification;
        parent::__construct($context);
    }

    public function execute()
    {
        if (isset($this->_request->getPostValue()['data'])) {
            $result = $this->_productNotification->insertNotification($this->_request->getPostValue()['data']);
            echo json_encode($result);
        }
    }
}
