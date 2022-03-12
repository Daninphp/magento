<?php

namespace Smartweb\Auto\Model;

use Magento\Eav\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Smartweb\Auto\Helper\Data as Helper;
use Smartweb\Auto\Helper\Html as HtmlHelper;

class Data
{
    protected $_eavConfig;

    protected $_collectionFactory;

    protected $_htmlHelper;

    public function __construct(Config $config, CollectionFactory $collectionFactory, HtmlHelper $htmlHelper)
    {
        $this->_eavConfig = $config;
        $this->_htmlHelper = $htmlHelper;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @param string $modelName
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getModels(string $modelName): array
    {
        $attribute = $this->_eavConfig->getAttribute('catalog_product', $modelName);
        $options = $attribute->getSource()->getAllOptions();
        $values = [];
        foreach ($options as $option) {
            if (strlen($option['label']) > 1) {
                $values[] = ['id' => $option['value'], 'label' => $option['label']];
            }
        }

        return $values ?? [];
    }

    /**
     * @param $markaAutomobila
     * @param $modelAutomobila
     * @param $tipAutomobila
     * @param $vrstaSijalice
     * @return string
     */
    public function findResults($markaAutomobila, $modelAutomobila, $tipAutomobila, $vrstaSijalice): string
    {
        $collection = $this->_collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(Helper::MARKA_ATTR_CODE, $markaAutomobila)
            ->addAttributeToFilter(Helper::MODEL_ATTR_CODE, $modelAutomobila)
            ->addAttributeToFilter(Helper::TYPE_ATTR_CODE, $tipAutomobila)
            ->addAttributeToFilter(Helper::BULB_TYPE_CODE, $vrstaSijalice);

        if ($collection->getSize() > 0) {
            return $this->_htmlHelper->generateHtml($collection);
        }

        return '<h3 style="text-align: center"><strong>' . __('No results found for your search, please try again!') . '</strong></h3>';
    }
}