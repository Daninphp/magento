<?php

namespace Codiac\Search\Block;

use Magento\Catalog\Block\Product\View;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Session;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ProductFactory;

class Slider extends Template
{
    public $view;
    private $collectionFactory;
    private $storeManager;
    private $session;
    private $category;
    private $_productFactory;

    private $mappingSizes = [
        '7' => 'T8',
        '8' => 'T8',
        '10' => 'T10',
        '18' => 'T12',
        '24' => 'T30',
        '38' => 'T40',
    ];

    public function __construct(Template\Context $context, View $view, CollectionFactory $collectionFactory, StoreManagerInterface $storeManager, Session $session, Category $category, ProductFactory $productFactory)
    {
        $this->view = $view;
        $this->category = $category;
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;
        $this->_productFactory = $productFactory;
        $this->storeManager = $storeManager->getStore();
        parent::__construct($context);
    }

    public function getSizeLabel(string $sizeCode)
    {
        $searchString = strtok($sizeCode, ' ');

        return $this->mappingSizes[$searchString];
    }

    private function returnFinsetArray(array $attributes)
    {
        $idArray = array();
        foreach ($attributes as $attribute) {
            $idArray[]['finset'] = [$attribute];
        }

        return $idArray;
    }

    public function getMediaUrl(string $folder = null)
    {
        if ($folder) {
            return $this->storeManager->getBaseUrl($folder);
        }

        return $this->storeManager->getBaseUrl();
    }

    public function getProductCollection()
    {
        $productSize[] = $this->view->getProduct()->getSize();
        $productSize = $this->view->getProduct()->getSize();

        $productType[] = $this->view->getProduct()->getType();
        $productType = $this->returnFinsetArray($productType);

        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('size','catalog_product_entity_varchar')
            ->addAttributeToSelect('name','catalog_product_entity_varchar')
            ->addAttributeToSelect('url_key','catalog_product_entity_varchar')
            ->addAttributeToSelect('image','catalog_product_entity_varchar')
            ->addAttributeToSelect('type_id','catalog_product_entity_varchar')
//            ->addAttributeToFilter('entity_id', array('neq' => $this->view->getProduct()->getId()))
//            ->addAttributeToFilter('size', array('neq' => $productSize))
//            ->addAttributeToFilter('type', ['neq' => $this->view->getProduct()->getType()])
            ->addAttributeToFilter('type_id', 'rental')
            ->setOrder('size', 'ASC');

        try {
            $requestQuery = $this->session->getPostQuery();
            if($requestQuery) {
                $categoryId = $requestQuery['categoryId'];
                $category = $this->category->load($categoryId);

                $collection
                    ->addCategoryFilter($category);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        if (!empty($collection->getData())) {
            //collection->getData() gives proper items, while $collection gives all, this is why product is being loaded again...
            foreach ($collection->getData() as $product) {
                $product = $this->_productFactory->create()->load($product['entity_id']);
                $collectionObject[] = $product;
            }

            return $collectionObject;
        }

        return [];

    }

}