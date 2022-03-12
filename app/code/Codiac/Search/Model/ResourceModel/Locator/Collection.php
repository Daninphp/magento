<?php

namespace Codiac\Search\Model\ResourceModel\Locator;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Codiac\Search\Model\Locator as ItemModel;
use Codiac\Search\Model\ResourceModel\Locator as ItemResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(ItemModel::class,ItemResource::class);
    }
}
