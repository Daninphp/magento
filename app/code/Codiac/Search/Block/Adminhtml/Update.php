<?php
/**
 * Supplies basic information to view
 *
 * Returns:
 * Product collection
 * Customer groups
 *
 * @copyright   Copyright (c) Codiac
 *
 */
declare(strict_types = 1);

namespace Codiac\Search\Block\Adminhtml;

use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroupCollection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Module\Dir\Reader;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollection;

class Update extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectionFactory
     */
    protected $_productCollection;

    /**
     * @var CustomerGroupCollection
     */
    protected $_customerGroupCollection;

    /**
     * @var Reader
     */
    protected $_reader;

    /**
     * @var CategoryCollection
     */
    protected $_categoryCollection;

    /**
     * Update constructor.
     *
     * @param Template\Context $context
     * @param View $view
     * @param CollectionFactory $productCollection
     * @param CustomerGroupCollection $customerGroupCollection
     * @param Reader $reader
     * @param CategoryCollection $categoryCollection
     */
    public function __construct(
        Template\Context $context,
        View $view,
        CollectionFactory $productCollection,
        CustomerGroupCollection $customerGroupCollection,
        Reader $reader,
        CategoryCollection $categoryCollection
    ) {
        $this->view = $view;
        $this->_productCollection = $productCollection;
        $this->_customerGroupCollection = $customerGroupCollection;
        $this->_reader = $reader;
        $this->_categoryCollection = $categoryCollection;
        parent::__construct($context);
    }
    

    /**
     * returns pricing collection
     *
     * @return object
     */
    public function getCustomerGroups() : object
    {
        return $this->_customerGroupCollection;
    }

    /**
     * returns categories collection
     *
     * @return object
     */
    public function getCategories() : object
    {
        return $this->_categoryCollection->create()->addAttributeToFilter('entity_id', array('gt' => 2))->addAttributeToSelect('*')->setOrder('name', 'ASC');
    }

    /**
     * returns product collection
     *
     * @return object
     */
    public function getProductCollection() : object
    {
        return $this->_productCollection->create()
            ->addAttributeToSelect('name', 'catalog_product_entity_varchar')
            ->addAttributeToSelect('status', 'catalog_product_entity_varchar')
//            ->addAttributeToSelect('size', 'catalog_product_entity_varchar')  //if needed to sort by size of container
            ->addAttributeToFilter('type_id','rental')
            ->addAttributeToFilter('status','1')
            ->setOrder('entity_id', 'ASC');
    }

}
