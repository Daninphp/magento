<?php

namespace Codiac\Search\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Exclusion extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('calendar_exclusion_days', 'id');
    }
}
