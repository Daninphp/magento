<?php

namespace Codiac\Search\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Locator extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('locations_full_list', 'id');
    }

}
