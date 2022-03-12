<?php

namespace Codiac\Search\Model;

use Magento\Framework\Model\AbstractModel;

class Cities extends AbstractModel
{
    protected $_eventPrefix = 'codiac_search_cities';

    protected function _construct()
    {
        $this->_init(\Codiac\Search\Model\ResourceModel\Cities::class);
    }
}
