<?php

namespace Codiac\Search\Block;

use Codiac\Search\Model\Search as SearchModel;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;


class Search extends Template
{
    protected $searchModel;
    protected $_scopeConfig;

    public function __construct(Template\Context $context,array $data = [], SearchModel $searchModel, ScopeConfigInterface $scopeConfig)
    {
        $this->searchModel = $searchModel;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * returns @array of labels for given attribute
     */
    public function getAttributeValues() : array
    {
//        $attributeValues = $this->searchModel->getAttributeOptions('location');
//
//    //Check if first element of an array is empty and shift it
//        if (empty($attributeValues[0])) {
//            array_shift($attributeValues);
//        }

        $collection = $this->searchModel->getFullLocationListTable()->getData();
//echo'<pre>';print_r($collection);die();
        return array_column($collection, 'label');
    }

    public function getShippingHours()
    {
        return $this->_scopeConfig->getValue('shipping_system/shipping_hours/shipping_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}