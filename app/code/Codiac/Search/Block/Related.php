<?php

namespace Codiac\Search\Block;

use Codiac\Search\Model\Search as SearchModel;
use Magento\Catalog\Block\Product\View;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Related extends Template
{
    private $searchModel;
    public $view;
    private $product;
    private $storeManager;
    private $_collectionFactory;
    private $_product;

    public function __construct(Template\Context $context, SearchModel $searchModel, View $view, ProductRepositoryInterface $product, StoreManagerInterface $storeManager, CollectionFactory $collectionFactory,array $data = [])
    {
        $this->searchModel = $searchModel;
        $this->view = $view;
        $this->product = $product;
        $this->_collectionFactory = $collectionFactory;
        $this->_product = $this->view->getProduct();
        $this->storeManager = $storeManager->getStore();
        parent::__construct($context, $data);
    }

    public function getRelatedProducts()
    {
        $relatedProducts = $this->_product->getRelatedProducts();

        if ($relatedProducts) {
            foreach ($relatedProducts as $relatedProduct) {
                $_product = $this->product->getById($relatedProduct->getId());
                $relatedProductData[] = [
                    'name' => $_product->getResource()->getAttribute('type')->getFrontend()->getValue($_product),
//                    'img' => $this->storeManager->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .  $_product->getImage(),
//                    'img' => $this->storeManager->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'pub/media/container.png',
                    'url' => $this->storeManager->getBaseUrl() . $_product->getUrlKey() . '.html',
                ];
            }

            return $relatedProductData;
        }
        return [];

    }

    private function getContainerTypes($collection)
    {
        $containerTypes = [];
        foreach ($collection as $item) {
            $productObject = $this->product->getById($item->getId());
            $productType = $productObject->getResource()->getAttribute('type')->getFrontend()->getValue($productObject);
            switch ($productObject->getResource()->getAttribute('type')->getFrontend()->getValue($productObject))
            {
                case $productType == 'Offen': $name = 'Geöffnet'; break;
                case $productType == 'Befahrbar': $name = 'Befahrbar'; break;
                case $productType == 'Verschließbar': $name = 'Geschlossen'; break;
            }

            $containerTypes[] = [
                'id' => $productObject->getId(),
                'type' => $name,
                'img' => $this->storeManager->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'container.png',
                'url' => $this->storeManager->getBaseUrl() . $productObject->getUrlKey() . '.html',
            ];

        }

        return $containerTypes;

    }

    public function getProductTypes()
    {
        $collection = $this->_collectionFactory->create()
            ->addAttributeToFilter('type_id','rental')
//            ->addAttributeToFilter('sku', ['neq' => $this->_product->getSku()])
//            ->addAttributeToFilter('type', ['neq' => $this->_product->getType()])
            ->addAttributeToFilter('size', ['eq' => $this->_product->getSize()])
            ->addAttributeToFilter('status', ['eq' => 1]);

        return $this->getContainerTypes($collection);

    }


}