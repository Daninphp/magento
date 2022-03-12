<?php
/**
 * Responsible for reading data from \Perficient\Pricing\Model\ResourceModel\Prices\CollectionFactory
 *
 * @copyright   Copyright (c) Perficient (https://www.perficient.com/)
 */
declare(strict_types = 1);

namespace Codiac\Search\Controller\Adminhtml\Prices;

use Magento\Backend\App\Action\Context;
use Codiac\Search\Model\ManagePricing;
use Magento\Framework\Serialize\Serializer\Json;

class Read extends \Magento\Backend\App\Action
{
    /**
     * @var ManagePricing
     */
    protected $_managePricing;

    /**
     * @var Json
     */
    protected $_jsonHelper;

    /**
     * Read constructor.
     *
     * @param Context $context
     * @param ManagePricing $managePricing
     * @param Json $jsonHelper
     */
    public function __construct(Context $context, ManagePricing $managePricing, Json $jsonHelper)
    {
        $this->_managePricing = $managePricing;
        $this->_jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        echo $this->_jsonHelper->serialize($this->_managePricing->getRecords());
    }
}
