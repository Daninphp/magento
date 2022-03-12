<?php

namespace Codiac\Search\Plugin;

use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate;
use Magento\Framework\App\ResourceConnection;
use Codiac\Search\Model\ResourceModel\Locator\CollectionFactory as LocatorCollection;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Response\Http;

class SyncPostCodes
{
    /**
     * @var \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate
     */
    protected $_tableRate;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Codiac\Search\Model\ResourceModel\Locator\CollectionFactory
     */
    protected $_locatorCollection;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Http
     */
    protected $_http;

    /**
     * SyncPostCodes constructor.
     * @param Tablerate $tablerate
     * @param ResourceConnection $resource
     * @param LocatorCollection $locatorCollection
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param Http $http
     */
    public function __construct(Tablerate $tablerate, ResourceConnection $resource, LocatorCollection $locatorCollection, LoggerInterface $logger, StoreManagerInterface $storeManager, Http $http)
    {
        $this->_resource = $resource;
        $this->_locatorCollection = $locatorCollection;
        $this->_logger = $logger;
        $this->_tableRate = $tablerate;
        $this->_storeManager = $storeManager;
        $this->_http = $http;
    }



    public function sync()
    {
        $availableZipCodes = $this->getAvailableZipCodes();
        $fullLocationList = $this->_locatorCollection->create();
        //update available postal codes for search on landing page from available shipping postal codes/admin
        foreach ($fullLocationList as $location) {
            if (in_array($location->getPostalCode(), $availableZipCodes)){
                $location->setActive(1);
                $location->save();
                $this->_logger->log(\Psr\Log\LogLevel::NOTICE,date('d/m/yy') . ' Postal Code ' . $location->getPostalCode() . ' was enabled!');
            } else {
                $location->setActive(0);
                $location->save();
            }
        }
    }

    private function getTable()
    {
        return $this->_tableRate->getTable('shipping_tablerate');
    }

    protected function getAvailableZipCodes()
    {
        $connection = $this->_resource->getConnection();
        $entityTable = $this->getTable();

        $data = $connection->query(
            "SELECT dest_zip FROM {$entityTable} ;"
        );

        return array_column($data->fetchAll(), 'dest_zip');
    }

    public function getShippingPricePerPostCode($postCode)
    {
        if($postCode) {
            $connection = $this->_resource->getConnection();
            $entityTable = $this->getTable();
            $postCode = strtok($postCode, ' ');
            $postCode = "%" . $postCode . "%";

            $data = $connection->query(
                "SELECT * FROM {$entityTable} WHERE dest_zip LIKE '{$postCode}';"
            );

            $price = str_replace(',', '.', $data->fetchAll()[0]['price']);
            return number_format($price, 2);
        }

        $this->_http->setRedirect($this->_storeManager->getStore()->getBaseUrl());
    }

}