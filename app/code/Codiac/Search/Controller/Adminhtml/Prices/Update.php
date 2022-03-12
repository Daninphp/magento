<?php
/**
 * @copyright   Copyright (c) Codiac
 *
 */
declare(strict_types = 1);

namespace Codiac\Search\Controller\Adminhtml\Prices;

use Magento\Backend\App\Action\Context;
use Codiac\Search\Model\ManagePricing;
use Magento\Framework\Serialize\Serializer\Json;

class Update extends \Magento\Backend\App\Action
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
     * Update constructor.
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
        $data = $this->_request->getPostValue();

        //remove form_key from array and pass it to model
        unset($data['form_key']);

        if (!empty($data)) {
            echo $this->_jsonHelper->serialize($this->_managePricing->updatePrices($data));
        }
    }
}
