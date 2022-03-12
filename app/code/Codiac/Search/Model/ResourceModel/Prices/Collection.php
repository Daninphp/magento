<?php
/**
 * Prices Collection
 *
 * @copyright   Copyright (c) Codiac
 */
declare(strict_types = 1);

namespace Codiac\Search\Model\ResourceModel\Prices;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Codiac\Search\Model\Prices as ItemModel;
use Codiac\Search\Model\ResourceModel\Prices as ItemResource;

class Collection extends AbstractCollection
{
    /**
     * @type string
     */
    protected $_idFieldName = 'id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ItemModel::class,ItemResource::class);
    }
}
