<?php
/**
 * Supplies basic information to view
 *
 * @copyright   Copyright (c) Euronet (https://www.euronetworldwide.com)
 */
declare(strict_types = 1);

namespace Euronet\DeliveryDate\Block;

use Magento\Catalog\Block\Product\View;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface as Config;

class Dates extends \Magento\Framework\View\Element\Template
{
    /**
     * @var View
     */
    protected $_view;

    /**
     * @var Config
     */
    protected $_config;


    /**
     * Update constructor.
     *
     * @param Template\Context $context
     * @param View $view
     * @param Config $config
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        View $view
    ) {
        $this->_view = $view;
        $this->_config = $config;
        parent::__construct($context);
    }

    /**
     * Checks if delivery date is enabled in config
     *
     * @return string
     */
    private function getGlobalDeliveryAvailability() : string
    {
        return $this->_config->getValue('delivery_date/product_delivery/delivery_bool', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Checks delivery date for given product
     *
     * @return bool
     */
    public function getDeliveryAvailability() : bool
    {
        if ($this->getGlobalDeliveryAvailability() && $this->_view->getProduct()->getShowDeliverydate()) {
            return true;
        }

        return false;
    }

}
