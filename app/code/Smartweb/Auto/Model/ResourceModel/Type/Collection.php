<?php

namespace Smartweb\Auto\Model\ResourceModel\Type;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Smartweb\Auto\Model\Type as ItemModel;
use Smartweb\Auto\Model\ResourceModel\Type as ItemResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(ItemModel::class,ItemResource::class);
    }
}
