<?php
/**
 * Copyright © 2021 Euronet. All rights reserved.
 */
declare(strict_types = 1);

namespace Euronet\DeliveryDate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\ProductRepository;

class Cart implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * Cart constructor.
     *
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductRepository $productRepository
     */
    public function __construct(
        LoggerInterface $logger,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        ProductRepository $productRepository
    )
    {
        $this->_logger = $logger;
        $this->_request = $request;
        $this->_scopeConfig = $scopeConfig;
        $this->_productRepository = $productRepository;
    }

    public function execute(Observer $observer)
    {
        try {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            $item = $observer->getEvent()->getQuoteItem();
            $data = $this->_request->getParams();

            if (!empty($data['deliverydate'])) {

                $item->setDeliveryDate(date('Y-m-d', strtotime($data['deliverydate'])));
                $item->save();

                $additionalOptions[] = [
                    'label' => __("Delivery Date"),
                    'value' => $data['deliverydate']
                ];

                $item->addOption(array(
                    'code' => 'additional_options',
                    'value' => json_encode($additionalOptions)
                ));
            }
        } catch (\Exception $exception) {
            $this->_logger->critical($exception);
        }
    }
}
