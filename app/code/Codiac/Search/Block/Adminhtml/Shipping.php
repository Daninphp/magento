<?php

namespace Codiac\Search\Block\Adminhtml;

use Magento\Catalog\Block\Product\View;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Session;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;

class Shipping extends Template
{
    private $view;
    private $session;
    private $logger;
    private $config;
    public $storeManager;
    public $assetRepository;


    public function __construct(
        Template\Context $context,
        View $view,
        Session $session,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config,
        Repository $assetRepository
    ) {
        $this->view = $view;
        $this->session = $session;
        $this->storeManager = $storeManager->getStore();
        $this->logger = $logger;
        $this->config = $config;
        $this->assetRepository = $assetRepository;
        parent::__construct($context);
    }

    public function getIsShippingEnabled()
    {
        return $this->config->getValue('shipping_system/shipping_general/shipping_value', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getShippingHours()
    {
        return $this->config->getValue('shipping_system/shipping_hours/shipping_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}