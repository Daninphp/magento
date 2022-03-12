<?php
/**
 * Responsible for CRUD opertions
 *
 * @copyright   Copyright (c) Codiac
 */
declare(strict_types = 1);

namespace Codiac\Search\Model\CustomerPricing;

use Codiac\Search\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use Codiac\Search\Model\ResourceModel\Prices\CollectionFactory as PricesCollection;
use Codiac\Search\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;
use MGS\Amp\Model\Layer\Filter\Price;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\ProductTierPriceManagementInterface;
use Magento\Catalog\Model\Session as BaseSession;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Tax\Model\Calculation\Rate;

class Customer
{
    /**
     * @var CustomerCollection
     */
    protected $_customerCollection;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var CustomerSession
     */
    public $_customerSession;

    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @var Session
     */
    private $_session;

    /**
     * @var BaseSession
     */
    public $_baseSession;

    /**
     * @var ProductRepositoryInterface
     */
    private $_productRepository;

    /**
     * @var ProductTierPriceManagementInterface
     */
    private $_productTierPrice;

    /**
     * @var PricesCollection
     */
    private $_pricesCollection;

    /**
     * @var PricesCollection
     */
    protected $_taxCalculation;

    /**
     * @var ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var Tablerate
     */
    protected $_tableRate;

    /**
     * @var GroupRepositoryInterface
     */
    protected $_groupRepositoryInterface;

    /**
     * @var Rate
     */
    protected $_rate;

    /**
     * Update constructor.
     *
     * @param CustomerCollection $customerCollection
     * @param CustomerFactory $customerFactory
     * @param CustomerSession $_customerSession
     * @param LoggerInterface $logger
     * @param Session $session
     * @param ProductRepositoryInterface $productRepository
     * @param ProductTierPriceManagementInterface $productTierPrice
     * @param PricesCollection $pricesCollection
     * @param BaseSession $baseSession
     * @param TaxCalculationInterface $taxCalculation
     * @param ResourceConnection $resourceConnection
     * @param Tablerate $tableRate
     * @param GroupRepositoryInterface $groupRepositoryInterface
     * @param Rate $rate
     */
    public function __construct(
        CustomerCollection $customerCollection,
        CustomerFactory $customerFactory,
        CustomerSession $_customerSession,
        LoggerInterface $logger,
        Session $session,
        ProductRepositoryInterface $productRepository,
        ProductTierPriceManagementInterface $productTierPrice,
        PricesCollection $pricesCollection,
        BaseSession $baseSession,
        ResourceConnection $resourceConnection,
        Tablerate $tablerate,
        TaxCalculationInterface $taxCalculation,
        Rate $rate,
        GroupRepositoryInterface $groupRepositoryInterface
    )
    {
        $this->_customerCollection = $customerCollection;
        $this->_customerFactory = $customerFactory;
        $this->_customerSession = $_customerSession;
        $this->_logger = $logger;
        $this->_session = $session;
        $this->_productRepository = $productRepository;
        $this->_productTierPrice = $productTierPrice;
        $this->_pricesCollection = $pricesCollection;
        $this->_baseSession = $baseSession;
        $this->_taxCalculation = $taxCalculation;
        $this->_resourceConnection = $resourceConnection;
        $this->_groupRepositoryInterface = $groupRepositoryInterface;
        $this->_rate = $rate;
        $this->_tableRate = $tablerate;
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
            $collection = $this->_customerCollection->create()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('postal_code', $customerPostcode)
                ->addFieldToFilter('customer_group_id', $customerGroup);

            $collection->setOrder('discount_priority', 'DESC');

            return $this->pricingFormula($originalPrice, $collection->getFirstItem()->getDiscountType(), $collection->getFirstItem()->getDiscountAmount());

        }

        return $originalPrice;

    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function deletePrice(string $id) : array
    {
        try {
            $this->_customerFactory->create()->load($id)->delete();
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
        return $this->_customerCollection->create()->setOrder('product_id','DESC')->getData();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function updatePrices(array $data) : array
    {
        try {
            $model = $this->_customerFactory->create()->load($data['id']);
            $model->setData($data);
            $model->save();
            return ['status'=> true, 'message' => __("Successfully updated!")];
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
            return ['status'=> false, 'message' => __("There was problem updating, please contact administrator!")];
        }

    }

    /**
     * @param string $productId
     * @param string $cateogryId
     *
     * @return string|int
     */
    public function getCategoryPrice(string $productId, string $cateogryId)
    {
        $customerGroupId = $this->_session->getCustomer()->getGroupId();
        $priceMultiplier = 1;

        if (in_array($customerGroupId, ['1','0'])) {
            $customerGroupId = 0;
            $priceMultiplier = 1.1;
        }

        $price = $this->_pricesCollection->create()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('customer_group_id', $customerGroupId)
                ->addFieldToFilter('category_id', $cateogryId)
                ->getFirstItem()
                ->getBrutoPrice();

        if (!$price && $customerGroupId == '3') {
            $price = $this->_pricesCollection->create()
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('customer_group_id', 0)
                    ->addFieldToFilter('category_id', $cateogryId)
                    ->getFirstItem()
                    ->getBrutoPrice();
        }

        return  $price * $priceMultiplier;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function insertPrices(array $data) : array
    {
        $collection = $this->_customerCollection->create()
            ->addFieldToFilter('product_id', $data['product_id'])
            ->addFieldToFilter('category_id', $data['category_id'])
            ->addFieldToFilter('customer_id', $data['customer_id']);

        if (empty($collection->getData())) {
            //get original base price for selected category
            $basePrice = $this->getCategoryPrice($data['product_id'], $data['category_id']);

            $discountPrice = $basePrice - ($basePrice * $data['discount']/100);

            $data['original_price'] = $basePrice;
            $data['price_after_discount'] = $discountPrice;

            try {
                $this->_customerFactory->create()->setData($data)->save();
                return ['status'=> true, 'message' => __("Discount successfully added!")];
            } catch (\Exception $e) {
                $this->_logger->error($e->getMessage());
                return ['status'=> false, 'message' => __("There was an error adding the price, please contact administrator!")];
            }
        } else {
            $this->_logger->error('The price has already been set for this product and customer group.');
            return ['status'=> 'error', 'message' => __('Der Preis für dieses Produkt und diese Kundengruppe wurde bereits festgelegt!')];
        }

    }

    public function getTaxRate()
    {
//        return (string)($this->_taxCalculation->getCalculatedRate(2, $this->_session->getCustomerId()));//not working, someone changed something in admin????

        $group = $this->_groupRepositoryInterface->getById($this->_session->getCustomer()->getGroupId());
        $customerTaxClassId = $group->getTaxClassId();

        $connection = $this->_resourceConnection->getConnection();
        $entityTable = $this->_tableRate->getTable('tax_calculation');

        $data = $connection->query(
            "SELECT tax_calculation_rate_id FROM {$entityTable} WHERE customer_tax_class_id =  {$customerTaxClassId};"
        );

        $taxCalculationRateId = $data->fetchColumn(0);

        return $this->_rate->load($taxCalculationRateId, 'tax_calculation_rate_id')->getRate();
    }

    /**
     * @param string $productId
     *
     * @return mixed
     */
    public function getCustomerSpecificPrice(string $productId)
    {
        if ($this->_session->isLoggedIn()) {
            $taxRate = $this->getTaxRate();
            $taxRateFormated = 1 . '.' . $taxRate[0];

            $customerGroupId = $this->_session->getCustomer()->getGroupId() == '1' ? 0 : $this->_session->getCustomer()->getGroupId(); //B2C customer group (1) has same tax rate as not logged in client(10%)

            $collection = $this->_customerCollection->create()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('category_id', $this->_baseSession->getCategoryId())
                ->addFieldToFilter('customer_id', $this->_session->getCustomerId())
                ->getFirstItem();

            if (!empty($collection->getData())) {
                return round($collection['price_after_discount'], 2);
            } else {
                $collection = $this->_pricesCollection->create()
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('customer_group_id', $customerGroupId)
                    ->addFieldToFilter('category_id', $this->_baseSession->getCategoryId())
                    ->getFirstItem();

                if (!empty($collection->getData())) {
                    return round($collection['bruto_price'] * $taxRateFormated, 2);
                } else {
                    $collection = $this->_pricesCollection->create()
                        ->addFieldToFilter('product_id', $productId)
                        ->addFieldToFilter('customer_group_id', 0)
                        ->addFieldToFilter('category_id', $this->_baseSession->getCategoryId())
                        ->getFirstItem();

                    if (!empty($collection->getData())) {
                        return round($collection['bruto_price'] * $taxRateFormated, 2);
                    }
                }
            }
        } else {
            $collection = $this->_pricesCollection->create()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('customer_group_id', 0)
                ->addFieldToFilter('category_id', $this->_baseSession->getCategoryId())
                ->getFirstItem();

            if (!empty($collection->getData())) {
                return round($collection['bruto_price'] * 1.1, 2);
            }
        }

        return null;

    }


}
