<?php

namespace Epay\Pricelog\Model\ResourceModel\Pricelog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Epay\Pricelog\Model\Pricelog as ItemModel;
use Epay\Pricelog\Model\ResourceModel\Pricelog as ItemResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(ItemModel::class,ItemResource::class);
    }
}
