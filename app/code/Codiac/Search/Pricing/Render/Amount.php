<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codiac\Search\Pricing\Render;

use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\Render\Amount as MagentoAmount;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\View\Element\Template;
use Codiac\Search\Model\CustomerPricing\Customer;
use Magento\Framework\Registry;
use Magento\Framework\App\Response\Http;
use Codiac\Search\Block\Product\ExtraInfo;

/**
 * Price amount renderer
 *
 * @method string getAdjustmentCssClasses()
 * @method string getDisplayLabel()
 * @method string getPriceId()
 * @method bool getIncludeContainer()
 * @method bool getSkipAdjustments()
 */
class Amount extends MagentoAmount
{
    protected $_customer;
    protected $_registry;
    protected $_http;
    protected $_extraInfo;

    public function __construct(Template\Context $context, AmountInterface $amount, PriceCurrencyInterface $priceCurrency, RendererPool $rendererPool, SaleableInterface $saleableItem = null, PriceInterface $price = null, array $data = [], Customer $customer, Registry $registry, Http $http, ExtraInfo $extraInfo)
    {
        $this->_customer = $customer;
        $this->_registry = $registry;
        $this->_http = $http;
        $this->_extraInfo = $extraInfo;
        parent::__construct($context, $amount, $priceCurrency, $rendererPool, $saleableItem, $price, $data);
    }

    public function rootRedirect()
    {
        $this->_http->setRedirect($this->getBaseUrl());
    }

    public function checkCategorySession()
    {
        if ($this->_customer->_baseSession->getCategoryId()) {
            return true;
        }

        return false;
    }

    public function getCustomPrice()
    {
        if (!$this->_customer->_customerSession->isLoggedIn()) {
            return $this->_customer->getCustomerSpecificPrice($this->_registry->registry('current_product')->getId());
        }
    }

    public function getCustomerTaxRate()
    {
        return $this->_customer->getTaxRate();
    }

    public function getPriceInfo()
    {
        return $this->_extraInfo->getPriceInfo();
    }

}
