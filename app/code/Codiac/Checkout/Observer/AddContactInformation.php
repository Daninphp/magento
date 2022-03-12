<?php

namespace Codiac\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class AddContactInformation implements ObserverInterface
{

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $quote = $observer->getQuote();

        // Do whatever you want here

        return $this;
    }
}