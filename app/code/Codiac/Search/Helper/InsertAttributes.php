<?php

namespace Codiac\Search\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Entity\Attribute;

class InsertAttributes
{

    protected $productRepository;
    protected $collectionFactory;
    protected $attribute;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CollectionFactory $collectionFactory,
        Attribute $attribute
    ) {
        $this->productRepository = $productRepository;
        $this->collectionFactory = $collectionFactory;
        $this->attribute = $attribute;
    }

    public function init()
    {
        $productCollection = $this->collectionFactory->create();

        $regionsArray = [1,2,3,7];

        $alphabet = range('a','z');

        foreach ($alphabet as $letter) {
            $attributeCode = 'city_' . $letter;

            try {
                $cities = $this->attribute->loadByCode('catalog_product', $attributeCode)->getSource()->getAllOptions();
            } catch (\Exception $e) {
                echo $e->getMessage();
                continue;
            }

            foreach ($cities as $city) {
                if(strlen($city['label']) > 2) {
                    $firstCharracter = mb_substr($city['label'], 0, 1, "UTF-8");
                    if(in_array($firstCharracter, $regionsArray)){
                        $attributeOptionIds[] = $city['value'];
                    }
                }
            }

            foreach ($productCollection as $product) {
                try {
                    $product->setStoreId(0);
                    $product->setData($attributeCode, $attributeOptionIds);
                    $product->save();
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    continue;
                }
            }

        }
    }
}