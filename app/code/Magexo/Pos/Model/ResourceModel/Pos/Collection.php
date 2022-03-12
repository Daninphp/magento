<?php
namespace Magexo\Pos\Model\ResourceModel\Pos;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magexo\Pos\Model\Pos as Model;
use Magexo\Pos\Model\ResourceModel\Pos as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
