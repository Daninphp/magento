<?php

namespace Codiac\Search\Model;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Codiac\Search\Model\ResourceModel\Cities\CollectionFactory as CitiesCollection;
use Codiac\Search\Model\ResourceModel\Locator\CollectionFactory as LocatorCollection;
use Codiac\Search\Model\LocatorFactory;
use Magento\Catalog\Model\Entity\Attribute;
use Magento\Catalog\Model\Category;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Session;
use Codiac\Search\Model\CustomerPricing\Customer;

class Search
{

    private $collectionFactory;
    private $productFactory;
    private $attributesList;
    private $category;
    private $storeManager;
    private $citiesCollection;
    private $locatorCollection;
    private $locatorFactory;
    private $session;
    protected $_customer;

    //return separate attributes from main 'location' attribute
    private $returnSeparateAttributes = false;
    // znaci ide prvo "Mulde T8 offen" pa "Mulde T8 befahrbar" pa "Mulden T8 mit Deckel" i onda T12, T30, T40

    private $_typeOrder = [
        'offen' => 0,
        'befahrbar' => 1,
        'verschließbar' => 2,
    ];

    protected $connection;
    protected $resource;

    public function __construct(
        CollectionFactory $collectionFactory,
        ProductFactory $productFactory,
        Attribute $attributesList,
        Session $session,
        Category $category,
        StoreManagerInterface $storeManager,
        CitiesCollection $citiesCollection,
        LocatorFactory $locatorFactory,
        LocatorCollection $locatorCollection,
        Customer $customer,
        ResourceConnection $resource)
    {
        $this->collectionFactory = $collectionFactory;
        $this->productFactory = $productFactory;
        $this->citiesCollection = $citiesCollection;
        $this->locatorCollection = $locatorCollection;
        $this->storeManager = $storeManager;
        $this->locatorFactory = $locatorFactory;
        $this->category = $category;
        $this->session = $session;
        $this->attributesList = $attributesList;
        $this->_customer = $customer;

        $this->connection = $resource->getConnection();
        $this->resource = $resource;
    }

    private function getSeparateAttributeLocations(array $attributeList)
    {
        array_shift($attributeList);
        $attributeList = array_column($attributeList, 'label');

        $sortingArray = [];
        foreach ($attributeList as $attribute) {
            $stringElements = explode(' ', $attribute);
            $postalCode = $stringElements[0];
            array_shift($stringElements);
            $cityName = implode(' ', $stringElements);

            $sortingArray[] = [
                'cityName' => $cityName,
                'cityCode' => $postalCode
            ];
        }

        sort($sortingArray);
        foreach ($sortingArray as $item) {
            echo $item['cityCode'] . ' ' . $item['cityName'] . '<br>';
        }

        echo "End of list";die();

    }

    /**
     * need just one-time for inserting to location_cities table
     * source: https://magento.stackexchange.com/questions/219987/insert-multiple-rows-without-calling-model-save-inside-loop
     * */
    private function insertMultiple(string $table, array $data)
    {
        try {
            $tableName = $this->resource->getTableName($table);
            $this->connection->truncateTable($tableName);
            return $this->connection->insertMultiple($tableName, $data);
        } catch (\Exception $e) {
            var_dump($e->getMessage()); exit();
        }
    }

    private function addAllValuesToCitiesTable()
    {
        $alphabet = range('a','z');
        foreach ($alphabet as $letter) {
            $attributeCode = 'city_' . $letter;

            try {
                $cities = $this->attributesList->loadByCode('catalog_product', $attributeCode)->getSource()->getAllOptions();
            } catch (\Exception $e) {
                echo $e->getMessage();
                continue;
            }

            foreach ($cities as $city) {
                if(strlen($city['label']) > 2 ) {
                    $data[] = [
                        'label' => $city['label'],
                        'attribute_code' => $attributeCode,
                        'attribute_id_value' => $city['value']
                    ];
                }
            }
        }

        $this->insertMultiple('location_cities', $data);

    }


    public function getAttributeOptions(string $attributeCode)
    {
        $attributeList = $this->attributesList->loadByCode('catalog_product', $attributeCode)->getSource()->getAllOptions();

        if($this->returnSeparateAttributes) {
            $this->getSeparateAttributeLocations($this->attributesList->loadByCode('catalog_product', 'location')->getSource()->getAllOptions());
        }

        return $attributeList;
    }

    /**
     * searches array values like mysql "like"
     * returns
     * @param string $searchString
     * @param array $attributesList
     * @return array
     */
    private function getArraySearch(string $searchString, array $attributesList) : array
    {
        $input = preg_quote($searchString, '~');
        $data = array_column($attributesList, 'label');
        $dataLower = array_map('strtolower', $data);
        return preg_grep('~' . $input . '~', $dataLower);
    }

    private function getCategoryDetails(array $categoryIds, string $attributeCode, string $attributeId, $containerSizes)
    {
        $categoryData = [];

        foreach ($categoryIds[0] as $categoryId) {
            $categoryObject = $this->category->load($categoryId);
            $categoryData[] = [
                'id' => $categoryId,
                'description' => $categoryObject->getDescription(),
                'name' => utf8_decode($categoryObject->getName()),
                'url' => $attributeCode . '=' . $attributeId,
                'image' => $categoryObject->getImageUrl(),
            ];
        }

        return [$categoryData, $containerSizes];
    }

    private function returnFinsetArray(array $attributes)
    {
        $idArray = array();
        foreach ($attributes as $attribute) {
            $idArray[]['finset'] = [$attribute];
        }

        return $idArray;
    }

    private function copyFromOriginalTableToNew()
    {

        $originalTable = $this->citiesCollection->create();
        $newTable = $this->locatorCollection->create();
        foreach ($newTable as $item) {
            foreach ($originalTable  as $originalValue) {
                if ($item->getPostalCode() == substr($originalValue->getLabel(), 0, 4)) {
                    $sql = 'UPDATE locations_full_list SET attribute_code ="'.$originalValue->getAttributeCode().'", 
                    attribute_id_value ="'.$originalValue->getAttributeIdValue().'" WHERE id= '.$item->getId().'
                    ';
                    $this->connection->query($sql);
//                    $itemObject = $this->locatorFactory->create()->load($item->getId());
                    /*$originalValue->setAttributeCode($originalValue->getAttributeCode());
                    $originalValue->setAttributeIdValue($originalValue->getAttributeIdValue());
                    $originalValue->save();*/
                }
            }
        }

    }

    public function getFullLocationListTable()
    {
        return $this->locatorCollection->create()->addFieldToFilter('active', 1);
    }


    public function setLatitudeLongitude($lat, $lng)
    {
        if ($this->session->getLatitude() && $this->session->getLongtitude()) {
                $this->session->unsLatitude();
                $this->session->unsLongtitude();
                $this->session->setLatitude($lat);
                $this->session->setLongitude($lng);
        } else {
            $this->session->setLatitude($lat);
            $this->session->setLongitude($lng);
        }

    }

    public function setCityName($cityName)
    {
        if ($this->session->getCityName()) {
                $this->session->unsCityName();
                $this->session->setCityName($cityName);
        } else {
            $this->session->setCityName($cityName);
        }

    }

    public function getProductCollection($searchString)
    {

//        $this->copyFromOriginalTableToNew();die();
//        $this->getAttributeOptions('location'); // UNCOMMENT WHEN NEEDED ATTRIBUTES?
//        $this->addAllValuesToCitiesTable(); echo 'done inserting to table';die();// UNCOMMENT WHEN NEEDED, CREATE CRON JUST FOR THIS?

        $searchString = '%' . $searchString . '%';

        //here how to get data from 'location_cities' table
        $attributeData = $this->locatorCollection->create()->addFieldToFilter('label', array('like' => $searchString))->getFirstItem();

        //set ltd and lgn in session to be used on product page for nearest street location
        $this->setLatitudeLongitude($attributeData->getLatitude(), $attributeData->getLongitude());
        $this->setCityName($attributeData->getCity());

        $attributeCode = $attributeData->getAttributeCode();
        $attributeId[] = $attributeData->getAttributeIdValue();

        $idArray = $this->returnFinsetArray($attributeId);

        if (!empty($attributeData->getData())) {
            $collection = $this->collectionFactory->create()
                ->addAttributeToSelect($attributeCode, 'catalog_product_entity_varchar')
                ->addAttributeToFilter('type_id','rental')
                ->addAttributeToFilter($attributeCode, $idArray);

            $productCategoryIds = [];

            foreach ($collection as $product) {
                $productCategoryIds[] = $product->getCategoryIds();
            }

            $productCategoryIds = array_map("unserialize", array_unique(array_map("serialize", $productCategoryIds)));

            if(!empty($productCategoryIds)){
//                $conteinerTypes = $this->getAttributeOptions('type');
                $containerSizes = $this->getAttributeOptions('size');
                return $this->getCategoryDetails($productCategoryIds, $attributeCode, $attributeId[0], $containerSizes);
            }

            $errorMessage = __('Für diese Postleitzahl wurden keine Container gefunden. Bitte versuchen Sie es erneut!');
            return ['error' => $errorMessage];

        } else {
            $errorMessage = __('Keine Postleitzahl gefunden, bitte versuchen Sie es erneut!');
            return ['error' => $errorMessage];
        }
    }

    public function setLongitudeLatitude($postalCode)
    {
        $collection = $this->locatorCollection->create();
    }

    public function getContainerSizeCollection($postData)
    {
        if ($this->session->getSizeAttributeId()) {
            $this->session->unsSizeAttributeId();
        }

        $categoryId = $postData['categoryId'];
        $category = $this->category->load($categoryId);

        if ($this->session->getCategoryName()) {
            $this->session->unsCategoryName();
        }

        $this->session->setCategoryName($category->getName());

        if ($this->session->getCategoryId()) {
            $this->session->unsCategoryId();
        }

        $this->session->setCategoryId($categoryId);

        $collection = $this->collectionFactory->create()
                ->addCategoryFilter($category);

        $collection->getSelect()->joinInner(
            ['pricing' => $collection->getTable('product_kg_pricing')],
            'entity_id = pricing.product_id and pricing.category_id = ' . $categoryId .'',
            ['*']);

        $containerType = [];

        if (!empty($collection->getData())) {
            foreach ($collection->getData() as $product) {
                //collection->getData() gives proper items, while $collection gives all, this is why product is being loaded again...
                $product = $this->productFactory->create()->load($product['entity_id']);
                $images = [];
                foreach ($product->getMediaGalleryImages() as $key => $image) {
                    $images[] = $image->getUrl();
                }

                $type = $product->getResource()->getAttribute('type')->getFrontend()->getValue($product);
                $orderType = $this->_typeOrder[strtolower($type)];

                $size = $product->getResource()->getAttribute('size')->getFrontend()->getValue($product);
                $orderConcat = round($this->_customer->getCategoryPrice($product->getId(), $categoryId) ?? $product->getFinalPrice()) . $orderType;
                $order = (int)$orderConcat;

                $containerType[] = [
                    'id' => $product->getId(),
                    'order' => $order,
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price' => $this->_customer->getCategoryPrice($product->getId(), $categoryId) ?? $product->getFinalPrice(),
                    'img' => $product->getImage(),
                    'label' => $type,
                    'size' => $size,
                    'url_key' => $product->getUrlKey(),
                    'images' => $images,
                ];

            }

            usort($containerType, function($a, $b) {
                return $a['order'] <=> $b['order'];
            });

            return [$containerType, $postData];
        }

        return ['error' => '<p>Keine Containeroptionen für Ihre Suche gefunden, versuchen Sie es erneut!</p>'];
    }

    public function getTypeFilterCollection($postData)
    {
        $filterAttributeType = $postData['attribute'];
        $filterAttributeTypeId[] = $postData['id'];
        $filterAttributeTypeId = $this->returnFinsetArray($filterAttributeTypeId);

        $availableFilters = explode('=', $postData['dataSource']);

        $categoryId = $availableFilters[1];
        $category = $this->category->load($categoryId);

        $locationAttributeCode = $availableFilters[2];
        $locationAttributeId[] = $availableFilters[3];
        $locationAttributeId = $this->returnFinsetArray($locationAttributeId);

        $sizeAttributeCode = $availableFilters[4];
        $sizeAttributeId[] = $availableFilters[5];
        $sizeAttributeId = $this->returnFinsetArray($sizeAttributeId);


        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect($locationAttributeCode, 'catalog_product_entity_varchar')
            ->addAttributeToSelect($filterAttributeType, 'catalog_product_entity_varchar')
            ->addAttributeToSelect($sizeAttributeCode, 'catalog_product_entity_varchar')
            ->addAttributeToFilter($locationAttributeCode, $locationAttributeId)
            ->addAttributeToFilter($filterAttributeType, $filterAttributeTypeId)
            ->addAttributeToFilter($sizeAttributeCode, $sizeAttributeId)
            ->addAttributeToFilter('type_id','rental')
            ->addCategoryFilter($category);


        if (!empty($collection->getData())) {
            $storeRootPath = $this->storeManager->getStore()->getBaseUrl();
            $productId = $collection->getFirstItem()->getId();
            $productUrl = $this->productFactory->create()->load($productId)->getUrlKey() . '.html';

            $productUrlPath = $storeRootPath . $productUrl;

            return $productUrlPath;
        }

        return ['error' => '<p>Es wurden keine Container für Ihre Suche gefunden. Versuchen Sie es erneut!</p>'];
    }

}
