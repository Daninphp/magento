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
use Magento\Framework\View\Element\Template;
use Magento\Framework\Module\Dir\Reader;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollection;

class Customer extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectionFactory
     */
    protected $_productCollection;

    /**
     * @var Reader
     */
    protected $_reader;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customer;

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
     * @param Reader $reader
     * @param CustomerFactory $customerFactory
     * @param CustomerRepositoryInterface $customer
     * @param CategoryCollection $categoryCollection
     */
    public function __construct(
        Template\Context $context,
        View $view,
        CollectionFactory $productCollection,
        Reader $reader,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customer,
        CategoryCollection $categoryCollection
    ) {
        $this->view = $view;
        $this->_productCollection = $productCollection;
        $this->_reader = $reader;
        $this->_customerFactory = $customerFactory;
        $this->_customer = $customer;
        $this->_categoryCollection = $categoryCollection;
        parent::__construct($context);
    }
    

    /**
     * returns pricing collection
     *
     * @return object
     */
    public function getCustomers() : object
    {
        return $this->_customerFactory->create()->getCollection();
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
        return $this->_productCollection->create()->addAttributeToSelect('name', 'catalog_product_entity_varchar')->addAttributeToSelect('price', 'catalog_product_entity_varchar')->addAttributeToFilter('type_id','rental');
    }

}
