<?php

namespace Codiac\Search\Model\ResourceModel\Customer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Codiac\Search\Model\Customer as ItemModel;
use Codiac\Search\Model\ResourceModel\Customer as ItemResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(ItemModel::class,ItemResource::class);
    }
}
