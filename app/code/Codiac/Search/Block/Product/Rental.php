<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codiac\Search\Block\Product;

use Magento\Framework\View\Element\Template;
use Magenest\RentalSystem\Model\RentalFactory;
use Magenest\RentalSystem\Model\RentalPriceFactory;
use Magenest\RentalSystem\Model\RentalOptionFactory;
use Magenest\RentalSystem\Model\RentalOptionTypeFactory;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Api\TaxCalculationInterface;
use Magenest\RentalSystem\Block\Product\Rental as OriginalClass;

/**
 * Class Rental
 * @package Magenest\RentalSystem\Block\Product
 */
class Rental extends OriginalClass
{

    /**
     * @var string
     */
    protected $_template = 'Magenest_RentalSystem::catalog/product/rental.phtml';

    /**
     * @var RentalFactory
     */
    protected $_rentalFactory;

    /**
     * @var RentalPriceFactory
     */
    protected $_rentalPriceFactory;

    /**
     * @var RentalOptionFactory
     */
    protected $_rentalOptionFactory;

    /**
     * @var RentalOptionTypeFactory
     */
    protected $_rentalOptionTypeFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_currency;

    /**
     * @var \Magenest\RentalSystem\Model\Rental
     */
    protected $_rental;

    /**
     * @var FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var PriceHelper
     */
    protected $_price;

    /**
     * @var TaxCalculationInterface
     */
    protected $_taxCalculation;

    protected $_editOptions;

    /**
     * Rental constructor.
     *
     * @param RentalFactory $rentalFactory
     * @param RentalPriceFactory $rentalPriceFactory
     * @param RentalOptionFactory $rentalOptionFactory
     * @param RentalOptionTypeFactory $rentalOptionTypeFactory
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param FormatInterface $_localeFormat
     * @param PriceHelper $_price
     * @param TaxCalculationInterface $taxCalculation
     * @param array $data
     */
    public function __construct(
        RentalFactory $rentalFactory,
        RentalPriceFactory $rentalPriceFactory,
        RentalOptionFactory $rentalOptionFactory,
        RentalOptionTypeFactory $rentalOptionTypeFactory,
        \Magento\Catalog\Block\Product\Context $context,
        FormatInterface $_localeFormat,
        PriceHelper $_price,
        TaxCalculationInterface $taxCalculation,
        array $data = []
    ) {
        $this->_rentalFactory           = $rentalFactory;
        $this->_rentalPriceFactory      = $rentalPriceFactory;
        $this->_rentalOptionFactory     = $rentalOptionFactory;
        $this->_rentalOptionTypeFactory = $rentalOptionTypeFactory;
        $this->_coreRegistry            = $context->getRegistry();
        $this->_localeFormat            = $_localeFormat;
        $this->_price                   = $_price;
        $this->_taxCalculation          = $taxCalculation;
        parent::__construct($rentalFactory, $rentalPriceFactory,$rentalOptionFactory,$rentalOptionTypeFactory,$context,$_localeFormat,$_price,$taxCalculation, []);
    }



    /**
     * @param $optionId
     *
     * @return array
     */
    public function getOptionTypes($optionId)
    {
        echo"enter";die();
        $data  = [];
        $i     = 0;
        $types = $this->_rentalOptionTypeFactory->create()->getCollection()
            ->addFilter('product_id', $this->getCurrentProductId())
            ->addFilter('option_id', $optionId);
        foreach ($types as $type) {
            /** @var \Magenest\RentalSystem\Model\RentalOptionType $type */
            if ($type->getPrice() < 20) {
                $price = $this->getCustomerGroupPrice();
                $type->setPrice($price);
            }
//            echo"<pre>";print_r($type->getPrice());
            $data[$i] = $type->getData();
            $i++;
        }

        return $data;
    }

}
