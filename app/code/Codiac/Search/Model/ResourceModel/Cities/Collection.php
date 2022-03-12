<?php

namespace Codiac\Search\Model\ResourceModel\Cities;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Codiac\Search\Model\Cities as ItemModel;
use Codiac\Search\Model\ResourceModel\Cities as ItemResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(ItemModel::class,ItemResource::class);
    }
}
