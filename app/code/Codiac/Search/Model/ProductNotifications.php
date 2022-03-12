<?php

namespace Codiac\Search\Model;

use Codiac\Search\Model\ResourceModel\Notification\CollectionFactory as NotificationCollection;
use Codiac\Search\Model\NotificationFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;

class ProductNotifications
{
    private $_notificationFactory;
    private $_notificationCollection;
    private $storeManager;
    private $logger;
    private $connection;
    private $resource;


    public function __construct(
        NotificationCollection $notificationCollection,
        ResourceConnection $resource,
        LoggerInterface $logger,
        NotificationFactory $notificationFactory,
        StoreManagerInterface $storeManager
    )
    {
        $this->_notificationCollection = $notificationCollection;
        $this->logger = $logger;
        $this->_notificationFactory = $notificationFactory;
        $this->storeManager = $storeManager;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
    }

    private function getTableModel()
    {
        return $this->_notificationFactory->create();
    }

    private function returnFinsetArray(array $attributes)
    {
        $idArray = array();
        foreach ($attributes as $attribute) {
            $idArray[]['finset'] = [$attribute];
        }

        return $idArray;
    }

    public function insertNotification(array $data = null)
    {
       $shippingHours =  $this->returnFinsetArray($data['shipping_hours']);

        $collection = $this->_notificationCollection->create()
            ->addFieldToFilter('product_id', $data['product_id'])
            ->addFieldToFilter('days_id', $data['days_id'])
            ->addFieldToFilter('shipping_hours', $shippingHours);

        if (empty($collection->getData())) {
            try {
                foreach ($data['shipping_hours'] as $shipping_hour) {
                    $model = $this->getTableModel();
                    $model->setProductId($data['product_id']);
                    $model->setProductName($data['product_name']);
                    $model->setDaysId($data['days_id']);
                    $model->setDaysName($data['days_name']);
                    $model->setShippingHours($shipping_hour);
                    $model->save();
                }

                return ['status'=> 'success', 'message' => "Benachrichtigung hinzugefügt!"];
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                return ['status'=> 'error', 'message' => __("Es ist kein Fehler aufgetreten. Bitte wenden Sie sich an den Administrator!")];
            }
        } else {
            $this->logger->error('Diese Zeitkombination wurde bereits für dieses Produkt und diesen Tag festgelegt.');
            return ['status'=> 'error', 'message' => __('Diese Zeitkombination wurde bereits für dieses Produkt und diesen Tag festgelegt.')];
        }

    }

    public function getNotificationCollection()
    {
        $collection = $this->_notificationCollection->create()->setOrder('product_id', 'ASC')->setOrder('days_id', 'ASC')->getData();
        $uniqueValuesArray = [];
        foreach ($collection as $item) {
            if (!empty($uniqueValuesArray)){
                if (isset($uniqueValuesArray[$item['product_id'] . $item['days_id']])) {
                   $uniqueValuesArray[$item['product_id'] . $item['days_id']]['shipping_hours'] .= ';' . $item ['shipping_hours'];
                } else {
                    $uniqueValuesArray[$item['product_id']  . $item['days_id']] = $item;
                }
            } else {
                $uniqueValuesArray[$item['product_id']  . $item['days_id']] = $item;
            }
        }

        return array_values($uniqueValuesArray);
    }

    public function deleteNotification(string $productId, string $dayId)
    {
        try {
            $collection = $this->_notificationCollection->create()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('days_id', $dayId);

            foreach ($collection as $item) {
                $item->delete();
            }
            return ['status'=> 'success','message' => __("Benachrichtigung gelöscht!")];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return ['status'=> 'error','message' => __("Es ist kein Fehler aufgetreten. Bitte wenden Sie sich an den Administrator!")];
        }
    }

    private function getHours(string $productId, string $day)
    {
        return $this->_notificationCollection->create()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('days_id', $day)
            ->addFieldToSelect('shipping_hours')
//            ->setOrder('shipping_hours', 'ASC')
            ->getData();

    }

    public function getShippingHours(array $data)
    {
        $startHourCollection = $this->getHours($data['productId'], $data['startDay']);
        $endHourCollection = $this->getHours($data['productId'], $data['endDay']);

        return [array_values($startHourCollection), array_values($endHourCollection)];
    }

    public function deleteAllRecords()
    {
        $sql = "TRUNCATE calendar_exclusion_days";
        $this->connection->query($sql);
    }

}
