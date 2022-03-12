<?php
namespace Magexo\Pos\Model\ResourceModel\Item;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magexo\Pos\Model\Item as Model;
use Magexo\Pos\Model\ResourceModel\Item as ResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}

