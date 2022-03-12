<?php

namespace Codiac\Search\Model;

use Magento\Framework\Model\AbstractModel;

class Customer extends AbstractModel
{
    protected $_eventPrefix = 'codiac_search_per_customer_pricing';

    protected function _construct()
    {
        $this->_init(\Codiac\Search\Model\ResourceModel\Customer::class);
    }
}
