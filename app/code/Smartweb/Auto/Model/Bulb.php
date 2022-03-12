<?php

namespace Smartweb\Auto\Model;

use Magento\Framework\Model\AbstractModel;

class Bulb extends AbstractModel
{
    protected $_eventPrefix = 'smartweb_auto_car_bulb';

    protected function _construct()
    {
        $this->_init(\Smartweb\Auto\Model\ResourceModel\Bulb::class);
    }
}
