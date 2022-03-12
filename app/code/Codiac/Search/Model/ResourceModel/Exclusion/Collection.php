<?php

namespace Codiac\Search\Model\ResourceModel\Exclusion;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Codiac\Search\Model\Exclusion as ExclusionModel;
use Codiac\Search\Model\ResourceModel\Exclusion as ExclusionResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(ExclusionModel::class,ExclusionResource::class);
    }
}
