<?php

namespace Magexo\Pos\Model;

use Magento\Framework\Model\AbstractModel;
use Magexo\Pos\Model\ResourceModel\Pos as ResourceModel;

class Pos extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
