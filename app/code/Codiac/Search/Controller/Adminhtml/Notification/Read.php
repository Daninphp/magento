<?php

namespace Codiac\Search\Controller\Adminhtml\Notification;

use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Codiac\Search\Model\ProductNotifications;

class Read extends \Magento\Framework\App\Action\Action
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
        echo json_encode($this->_productNotifications->getNotificationCollection());
    }
}
