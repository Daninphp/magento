<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codiac\MaxConfigurablePrice\Pricing;

class ConfigurablePrice
{
    protected $_moduleManager;
    protected $_jsonEncoder;
    protected $_registry;


    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        array $data = []
    )
    {
        $this->_moduleManager = $moduleManager;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_registry = $registry;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->_configurableType = $configurableType;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->stockState = $stockState;
    }

    /**
     * @param \Magento\Framework\Pricing\SaleableInterface|\Magento\Catalog\Model\Product $product
     * @return float
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundResolvePrice($subject, \Closure $proceed,\Magento\Framework\Pricing\SaleableInterface $product)
    {
        $price = null;
        //get parent product id
        $parentId = $product['entity_id'];
        $childObj = $this->getChildProductObj($parentId);
        foreach($childObj as $childs){
            $productPrice = $childs->getPrice();
            $price = $price ? max($price, $productPrice) : $productPrice;
        }
        return $price;
        //return (float)$proceed($product['entity_id']);
    }

    public function getProductInfo($id){
        //get product obj using api repository...
        if(is_numeric($id)){
            return $this->productRepository->getById($id);
        }else{
            return;
        }
    }

    public function getChildProductObj($id){
        $product = $this->getProductInfo($id);
        //if quote with not proper id then return null and exit;
        if(!isset($product)){
            return;
        }

        if ($product->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return [];
        }

        $storeId = 1;//$this->_storeManager->getStore()->getId();
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter($storeId, $product);
        $childrenList = [];

        foreach ($productTypeInstance->getUsedProducts($product) as $child) {
            $attributes = [];
            $isSaleable = $child->isSaleable();

            //get only in stock product info
            if($isSaleable){
                foreach ($child->getAttributes() as $attribute) {
                    $attrCode = $attribute->getAttributeCode();
                    $value = $child->getDataUsingMethod($attrCode) ?: $child->getData($attrCode);
                    if (null !== $value && $attrCode != 'entity_id') {
                        $attributes[$attrCode] = $value;
                    }
                }

                $attributes['store_id'] = $child->getStoreId();
                $attributes['id'] = $child->getId();
                /** @var \Magento\Catalog\Api\Data\ProductInterface $productDataObject */
                $productDataObject = $this->productFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $productDataObject,
                    $attributes,
                    '\Magento\Catalog\Api\Data\ProductInterface'
                );
                $childrenList[] = $productDataObject;
            }
        }

        $childConfigData = array();
        foreach($childrenList as $child){
            $childConfigData[] = $child;
        }

        return $childConfigData;
    }

}