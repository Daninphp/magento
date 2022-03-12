<?php

namespace Epay\Pricelog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Pricelog extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('epay_pricelog', 'id');
    }
}
