<?php

namespace Smartweb\Auto\Model;

use Magento\Framework\Model\AbstractModel;

class Model extends AbstractModel
{
    protected $_eventPrefix = 'smartweb_auto_car_model';

    protected function _construct()
    {
        $this->_init(\Smartweb\Auto\Model\ResourceModel\Model::class);
    }
}
