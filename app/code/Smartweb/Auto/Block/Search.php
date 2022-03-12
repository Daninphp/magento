<?php

namespace Smartweb\Auto\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Eav\Model\Config;
use Smartweb\Auto\Helper\Data;

class Search extends Template
{
    protected $_scopeConfig;

    protected $_eavConfig;


    /**
     * @param Template\Context $context
     * @param array $data
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $eavConfig
     */
    public function __construct(Template\Context $context, array $data = [], ScopeConfigInterface $scopeConfig, Config $eavConfig)
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_eavConfig =   $eavConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCarMarks(): array
    {
        $attribute = $this->_eavConfig->getAttribute('catalog_product', Data::MARKA_ATTR_CODE);
        $options = $attribute->getSource()->getAllOptions();
        $values = [];
        foreach ($options as $option) {
            if (strlen($option['label']) > 1) {
                $values[] = ['id' => $option['value'], 'label' => $option['label']];
            }
        }

        return $values ?? [];
    }

    public function getShippingHours()
    {
        return $this->_scopeConfig->getValue('shipping_system/shipping_hours/shipping_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}