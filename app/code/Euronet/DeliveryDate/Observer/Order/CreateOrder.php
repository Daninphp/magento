<?php
/**
 * Copyright © 2021 Euronet. All rights reserved.
 */
declare(strict_types = 1);

namespace Euronet\DeliveryDate\Observer\Order;

use Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;

class CreateOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * CreateOrder constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * insert delivery date to sales_order_item table
     * checkout_onepage_controller_success_action event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer) : void
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        $orderItems = $order->getItems();

        foreach ($orderItems as $item) {
            try {
                $item->setDeliveryDate($item['product_options']['info_buyRequest']['deliverydate']);
                $item->save();
            } catch (\Exception $e) {
                $this->_logger->error($e->getMessage());
            }
        }
    }

}
