<?php
/**
 *
 * @copyright   Copyright (c) Codiac
 *
 */
declare(strict_types = 1);

namespace Codiac\Search\Controller\Adminhtml\CustomerPrices;

use Magento\Backend\App\Action\Context;
use Codiac\Search\Model\CustomerPricing\Customer;
use Magento\Framework\Serialize\Serializer\Json;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var Customer
     */
    protected $_managePricing;

    /**
     * @var Json
     */
    protected $_jsonHelper;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param Customer $managePricing
     * @param Json $jsonHelper
     */
    public function __construct(Context $context, Customer $managePricing, Json $jsonHelper)
    {
        $this->_managePricing = $managePricing;
        $this->_jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        if (isset($this->_request->getPostValue()['id'])) {
            echo $this->_jsonHelper->serialize($this->_managePricing->deletePrice($this->_request->getPostValue()['id']));
        }
    }
}
