<?php
/**
 * Prices Model
 *
 * @copyright   Copyright (c) Codiac
 */
declare(strict_types = 1);

namespace Codiac\Search\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Prices extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('product_kg_pricing', 'id');
    }
}
