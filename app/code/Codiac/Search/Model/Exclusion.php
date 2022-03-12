<?php

namespace Codiac\Search\Model;

use Magento\Framework\Model\AbstractModel;

class Exclusion extends AbstractModel
{
    protected $_eventPrefix = 'codiac_search_exclusion_days';

    protected function _construct()
    {
        $this->_init(\Codiac\Search\Model\ResourceModel\Exclusion::class);
    }
}
