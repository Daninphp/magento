<?php

namespace Codiac\Checkout\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;

class Index extends \Magento\Framework\App\Action\Action
{

    private $storeManager;

    public function __construct(Context $context, StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $currentStore = $this->storeManager->getStore();
        $baseUrl = $currentStore->getBaseUrl();
        $params = $this->getRequest()->getParams();
        $post = $this->getRequest()->getPostValue();

        echo "<pre>";
        print_r($params);
        print_r($post);
        exit;
        echo 'Default Module.';die();
    }
}
