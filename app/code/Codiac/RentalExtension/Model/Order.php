<?php

namespace Codiac\RentalExtension\Model;

use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface as Logger;
use \Magento\CatalogInventory\Api\StockStateInterface;
use Magenest\RentalSystem\Model\RentalPriceFactory;
use Codiac\Search\Model\CustomerPricing\Customer;


class Order
{
    private $logger;
    private $orderRepository;
    private $searchCriteriaBuilder;
    protected $_rentalPriceFactory;
    protected $_customer;

    public function __construct(
        Logger $logger,
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RentalPriceFactory $rentalPriceFactory,
        Customer $customer,
        StockStateInterface $stockState
    )
    {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_rentalPriceFactory = $rentalPriceFactory;
        $this->_customer = $customer;
        $this->stockState = $stockState;
    }

    public function getOrderData(array $postValues)
    {
        try {
            $orderId = $postValues['orderId'];

            if (strlen($orderId) != 9) {
                throw new \Exception('Die Bestellnummer muss 9 Zeichen enthalten!');
            }

            $orderEmail = trim(strtolower($postValues['orderEmail']));

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('increment_id', $orderId, 'eq')->create();

            $orderList = $this->orderRepository->getList($searchCriteria)->getItems();

            if (empty($orderList)) {
                throw new \Exception('Die Bestellung ist ungültig!');
            }

            $order = reset($orderList);
            $today = date("Y-m-d");
            $isExtensional = false;

            $customerEmail = $order->getCustomerEmail();

            if ($orderEmail !== $customerEmail) {
                throw new \Exception('Bestellnummer oder E-Mail-Adresse sind nicht korrekt. Bitte versuchen Sie es erneut!');
            }

            $orderItems = $order->getAllItems();

            foreach ($orderItems as $orderItem) {
                $orderItemData = $orderItem->getData();
                //$categoryId = $orderItemData['product_options']['info_buyRequest']['category_id']; // if needed for further development
                $orderItemData['product_options']['info_buyRequest']['original_price'] = $this->_customer->getCustomerSpecificPrice($orderItemData['product_id']);
                $orderItemData['product_additional_price']  = $this->_rentalPriceFactory->create()->loadByProductId($orderItemData['product_id'])->getAdditionalPrice();
                $orderItemData['base_period'] = $this->getProductBasePeriod($this->_rentalPriceFactory->create()->loadByProductId($orderItemData['product_id'])->getBasePeriod());
                $orderItemData['stock_qty'] = $this->stockState->getStockQty($orderItemData['product_id']);
                $rentalToDate = date("Y-m-d", $orderItemData['product_options']['info_buyRequest']['additional_options']['rental_to']);
                $isExtensional = $orderItemData['is_extensional'] = $today <= $rentalToDate;
                $orderItemsArray[] = $orderItemData;
            }

            if (!$isExtensional) {
                throw new \Exception('Die Bestellung ist abgelaufen!');
            }

            return [
                'is_extensional' => $isExtensional,
                'order_email' => $customerEmail,
                'order_items_array' => $orderItemsArray,
                ];

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            return ['error' => $exception->getMessage()];
        }
    }

    /**
     * @param $productId
     * @param string $price
     *
     * @return bool
     */

    private function getProductBasePeriod($basePeriod)
    {
        switch ($basePeriod) {
            case (strpos($basePeriod, 'w') !== false):
                return  (7 * (int)str_replace('w','', $basePeriod));
                break;
            case (strpos($basePeriod, 'd') !== false):
                return  (int)(str_replace('d','', $basePeriod));
                break;
            case (strpos($basePeriod, 'h') !== false):
                return  ((int)str_replace('h','', $basePeriod) / 24);
                break;
        }

        return 0;

    }

}
