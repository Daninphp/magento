<?php

namespace Epay\Pricelog\Model;

use Magento\Framework\Model\AbstractModel;

class Pricelog extends AbstractModel
{
    protected $_eventPrefix = 'epay_pricelog_change';

    protected function _construct()
    {
        $this->_init(\Epay\Pricelog\Model\ResourceModel\Pricelog::class);
    }
}
