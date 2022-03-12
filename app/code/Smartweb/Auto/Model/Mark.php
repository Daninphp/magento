<?php

namespace Smartweb\Auto\Model;

use Magento\Framework\Model\AbstractModel;

class Mark extends AbstractModel
{
    protected $_eventPrefix = 'smartweb_auto_car_mark';

    protected function _construct()
    {
        $this->_init(\Smartweb\Auto\Model\ResourceModel\Mark::class);
    }
}
