<?php

namespace Codiac\Search\Block\Adminhtml;

use Magento\Catalog\Block\Product\View;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Session;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Notification extends Template
{
    private $notificationDays = [
        '1' => "Montag",
        '2' => "Dienstag",
        '3' => "Mittwoch",
        '4' => "Donnerstag",
        '5' => "Freitag",
        '6' => "Samstag",
    ];

    private $view;
    private $session;
    private $logger;
    private $config;
    public $storeManager;
    public $assetRepository;
    private $_productCollection;


    public function __construct(
        Template\Context $context,
        View $view,
        Session $session,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config,
        Repository $assetRepository,
        CollectionFactory $productCollection
    ) {
        $this->view = $view;
        $this->session = $session;
        $this->storeManager = $storeManager->getStore();
        $this->logger = $logger;
        $this->config = $config;
        $this->assetRepository = $assetRepository;
        $this->_productCollection = $productCollection;
        parent::__construct($context);
    }

    public function getProductCollection()
    {
        return $this->_productCollection->create()->addAttributeToFilter('type_id','rental')->addAttributeToSelect('name', 'catalog_product_entity_varchar');
    }

    public function getNotificationDays()
    {
        return $this->notificationDays;
    }

    public function getDeliveryHours() {
        $hours2 = $hours = range(0, 23);
        $hoursDoubled = array_merge($hours, $hours2);
        asort($hoursDoubled);

        $i = 0;
        $deliveryHours = array_map(function($hour) use(&$i) {
            $left = str_pad($hour, 2, "0", STR_PAD_LEFT);
            if ($i % 2 == 0) {
                $left .= ':00';
            } else {
                $left .= ':30';
            }
            $i++;
            return $left;
        }, $hoursDoubled);

        $deliveryHours = array_values($deliveryHours);

        return $deliveryHours;
    }

}