<?php

namespace Codiac\Search\Model\ResourceModel\Notification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Codiac\Search\Model\Notification as ItemModel;
use Codiac\Search\Model\ResourceModel\Notification as ItemResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(ItemModel::class,ItemResource::class);
    }
}
