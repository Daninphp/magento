<?php

namespace Codiac\Search\Controller\Adminhtml\Notification;

use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Codiac\Search\Model\ProductNotifications;

class Delete extends \Magento\Framework\App\Action\Action
{
    private $session;
    private $_productNotifications;

    public function __construct(Context $context, Session $session, ProductNotifications $productNotifications)
    {
        $this->session = $session;
        $this->_productNotifications = $productNotifications;
        parent::__construct($context);
    }

    public function execute()
    {
        if (isset($this->_request->getPostValue()['data-product-id'])) {
            $result = $this->_productNotifications->deleteNotification($this->_request->getPostValue()['data-product-id'], $this->_request->getPostValue()['data-day']);
            echo json_encode($result);
        }
    }
}
