<?php

namespace Magexo\Pos\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Pos extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('pos_entity', 'id');
    }
}
