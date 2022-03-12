<?php

namespace Smartweb\Auto\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Model extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('car_model', 'id');
    }
}
