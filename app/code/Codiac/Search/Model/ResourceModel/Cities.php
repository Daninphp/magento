<?php

namespace Codiac\Search\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Cities extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('location_cities', 'id');
    }
}
