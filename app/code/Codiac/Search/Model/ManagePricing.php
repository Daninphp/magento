<?php
/**
 * Responsible for CRUD opertions
 *
 * @copyright   Copyright (c) Codiac
 */
declare(strict_types = 1);

namespace Codiac\Search\Model;

use Codiac\Search\Model\ResourceModel\Prices\CollectionFactory as PricesCollection;
use Codiac\Search\Model\PricesFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\ProductTierPriceManagementInterface;

class ManagePricing
{
    /**
     * @var PricesCollection
     */
    protected $_pricesCollection;

    /**
     * @var PricesFactory
     */
    protected $_pricesFactory;

    /**
     * @var CustomerSession
     */
    private $_customerSession;

    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @var Session
     */
    private $_session;

    /**
     * @var ProductRepositoryInterface
     */
    private $_productRepository;

    /**
     * @var ProductTierPriceManagementInterface
     */
    private $_productTierPrice;

    /**
     * Update constructor.
     *
     * @param PricesCollection $pricesCollection
     * @param PricesFactory $pricesFactory
     * @param CustomerSession $_customerSession
     * @param LoggerInterface $logger
     * @param Session $session
     * @param ProductRepositoryInterface $productRepository
     * @param ProductTierPriceManagementInterface $productTierPrice
     */
    public function __construct(
        PricesCollection $pricesCollection,
        PricesFactory $pricesFactory,
        CustomerSession $_customerSession,
        LoggerInterface $logger,
        Session $session,
        ProductRepositoryInterface $productRepository,
        ProductTierPriceManagementInterface $productTierPrice
    )
    {
        $this->_pricesCollection = $pricesCollection;
        $this->_pricesFactory = $pricesFactory;
        $this->_customerSession = $_customerSession;
        $this->_logger = $logger;
        $this->_session = $session;
        $this->_productRepository = $productRepository;
        $this->_productTierPrice = $productTierPrice;
    }

    /**
     * @param int $productId
     * @param string $originalPrice
     *
     * @return string
     */
    public function getProductPrice(int $productId, string $originalPrice) : string
    {
        if ($this->_customerSession->isLoggedIn()) {
            $customer = $this->_customerSession->getCustomer();
            $customerGroup = $customer->getGroupId();
            $customerAddresses = $customer->getAddresses();
            //get customer postcode
            foreach ($customerAddresses as $customerAddress) {
                $customerPostcode = $customerAddress->getPostcode();
            }
            //filter collection by productId and customerGroup
            $collection = $this->_pricesCollection->create()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('postal_code', $customerPostcode)
                ->addFieldToFilter('customer_group_id', $customerGroup);

            $collection->setOrder('discount_priority', 'DESC');

            return $this->pricingFormula($originalPrice, $collection->getFirstItem()->getDiscountType(), $collection->getFirstItem()->getDiscountAmount());

        }

        return $originalPrice;

    }

    /**
     * @param string $productId
     * @param string $customerGroupId
     * @param string $price
     *
     * @return mixed
     */
    private function setTierPrice(string $productId, string $customerGroupId, string $price)
    {
        try {
//            $product = $this->_productRepository->getById($productId)->getTierPrice();
            $sku = $this->_productRepository->getById($productId)->getSku();
            $qty = 1;
            try {
                $this->_productTierPrice->add($sku, $customerGroupId, $price, $qty);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

//            echo "<pre>";print_r($product);die();
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
            return ['status'=> false, 'message' => __("There was problem deleting price, please contact administrator!")];
        }
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function deletePrice(string $id) : array
    {
        try {
            $this->_pricesFactory->create()->load($id)->delete();
            return ['status'=> true, 'message' => __("Price successfully deleted!")];
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
            return ['status'=> false, 'message' => __("There was problem deleting price, please contact administrator!")];
        }
    }

    /**
     * @return array
     */
    public function getRecords() : array
    {
        return $this->_pricesCollection->create()->setOrder('product_id','DESC')->getData();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function updatePrices(array $data) : array
    {
//        $this->setTierPrice($data['id'], $data['customer_group_id'], $data['bruto_price']);
        
        try {
            $model = $this->_pricesFactory->create()->load($data['id']);
            $model->setData($data);
            $model->save();
            return ['status'=> true, 'message' => __("Successfully updated!")];
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
            return ['status'=> false, 'message' => __("There was problem updating, please contact administrator!")];
        }

    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function insertPrices(array $data) : array
    {
        $collection = $this->_pricesCollection->create()
            ->addFieldToFilter('product_id', $data['product_id'])
            ->addFieldToFilter('customer_group_id', $data['customer_group_id'])
            ->addFieldToFilter('category_id', $data['category_id']);

        if (empty($collection->getData())) {
            try {
                $this->_pricesFactory->create()->setData($data)->save();
                return ['status'=> true, 'message' => __("Price successfully added!")];
            } catch (\Exception $e) {
                $this->_logger->error($e->getMessage());
                return ['status'=> false, 'message' => __("There was an error adding the price, please contact administrator!")];
            }
        } else {
            $this->_logger->error('The price has already been set for this product and customer group.');
            return ['status'=> 'error', 'message' => __('Der Preis für dieses Produkt und diese Kundengruppe wurde bereits festgelegt!')];
        }

    }

    /**
     * @param string $originalPrice
     * @param string $productId
     * @param string $customerGroupId
     *
     * @return string
     */
    private function getPricesCollection(string $originalPrice,string $productId, string $customerGroupId, string $priceType) : string
    {
        $collection = $this->_pricesCollection->create()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('customer_group_id', $customerGroupId)
            ->getFirstItem();

        if (!empty($collection->getData())) {
            if ($priceType == 'kg') {
                return $collection->getPerKgPrice();
            } else {
                return $collection->getBrutoPrice();
            }
        } else {
            return $originalPrice;
        }
    }


    /**
     * @param string $productId
     * @param string $originalPrice
     *
     * @return string
     */
    public function getCustomerGroupPrice(string $productId, string $originalPrice) : string
    {
        if (!$this->_session->isLoggedIn()) {
            return $originalPrice;
        }

        $customerGroupId = $this->_session->getCustomer()->getGroupId();

        //check if original price is per kg or Bruto, this just guessing how much max per kg price would be
        if ($originalPrice < 10) {
            return $this->getPricesCollection($originalPrice, $productId, (string)$customerGroupId,'kg');
        } else {
            return $this->getPricesCollection($originalPrice, $productId, (string)$customerGroupId, 'bruto');
        }

    }

}
