<?php

namespace Codiac\Search\Controller\Shipping;

use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Codiac\Search\Model\ProductNotifications;
use Codiac\Search\Helper\Html as Helper;

class Time extends \Magento\Framework\App\Action\Action
{
    private $session;
    private $_productNotifications;
    private $helper;

    public function __construct(Context $context, Session $session, ProductNotifications $productNotifications, Helper $helper)
    {
        $this->session = $session;
        $this->helper = $helper;
        $this->_productNotifications = $productNotifications;
        parent::__construct($context);
    }

    public function execute()
    {

        try {
            $postData = $this->getRequest()->getPostValue();
            $collection = $this->_productNotifications->getShippingHours($postData['data']);
            echo json_encode($collection);

        } catch (\Exception $e){
            $this->_redirect($this->_redirect->getRefererUrl());
        }

    }

}
