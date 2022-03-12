<?php

namespace Codiac\RentalExtension\Block;

use Codiac\Search\Model\Search as SearchModel;
use Magento\Catalog\Block\Product\View;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Customer\Model\Session;

class Renting extends Template
{
    private $searchModel;
    private $view;
    private $product;
    private $storeManager;
    private $formKey;
    private $_orderRepository;
    private $_searchCriteriaBuilder;
    private $_customerSession;

    public function __construct(
        Template\Context $context,array $data = [],
        SearchModel $searchModel, View $view,
        Product $product,
        StoreManagerInterface $storeManager,
        FormKey $formKey,
        OrderRepository $orderRepository,
        Session $customerSession,
        SearchCriteriaBuilder $searchCriteriaBuilder)
    {
        $this->searchModel = $searchModel;
        $this->view = $view;
        $this->product = $product;
        $this->formKey = $formKey;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_customerSession = $customerSession;
        $this->storeManager = $storeManager->getStore();
        parent::__construct($context, $data);
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getCustomerId()
    {
        return $this->_customerSession->getCustomer()->getId();
    }

    public function getCustomerEmail()
    {
        return $this->_customerSession->getCustomer()->getEmail();
    }

    public function getActiveOrders()
    {

        $orderIds = [];
        if ($this->_customerSession->isLoggedIn()) {
            $today = date("Y-m-d");
            $customerId = $this->getCustomerId();
            $searchCriteria = $this->_searchCriteriaBuilder->addFilter('customer_id', $customerId, 'eq')->create();
            $orderList = $this->_orderRepository->getList($searchCriteria)->getItems();

            foreach ($orderList as $item) {
                foreach ($item->getItems() as $order) {
                    $rentalToDate = date("Y-m-d", $order['product_options']['info_buyRequest']['additional_options']['rental_to']);
                    $isExtensional = $orderItemData['is_extensional'] = $today <= $rentalToDate;
                    if ($isExtensional) {
//                        $id = str_pad($order->getId(), 9, "0", STR_PAD_LEFT);
                        $id = $item->getIncrementId();
                        $orderIds[] = $id;
                    }
                }

            }
        }

        return array_unique($orderIds);
    }

}