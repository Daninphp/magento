<?php

namespace Smartweb\Auto\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Mark extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('car_mark', 'id');
    }
}
