<?php

namespace Magexo\Pos\Model;

use Magento\Framework\Model\AbstractModel;
use Magexo\Pos\Model\ResourceModel\Item as ResourceModel;

class Item extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
