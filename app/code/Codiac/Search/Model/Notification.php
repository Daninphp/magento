<?php

namespace Codiac\Search\Model;

use Magento\Framework\Model\AbstractModel;

class Notification extends AbstractModel
{
    protected $_eventPrefix = 'codiac_search_product_notification';

    protected function _construct()
    {
        $this->_init(\Codiac\Search\Model\ResourceModel\Notification::class);
    }
}
