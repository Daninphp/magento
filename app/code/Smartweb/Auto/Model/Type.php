<?php

namespace Smartweb\Auto\Model;

use Magento\Framework\Model\AbstractModel;

class Type extends AbstractModel
{
    protected $_eventPrefix = 'smartweb_auto_car_type';

    protected function _construct()
    {
        $this->_init(\Smartweb\Auto\Model\ResourceModel\Type::class);
    }
}
