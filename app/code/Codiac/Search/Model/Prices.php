<?php
/**
 * Prices Model
 *
 * @copyright   Copyright (c) Codiac
 *
 */
declare(strict_types = 1);

namespace Codiac\Search\Model;

use Magento\Framework\Model\AbstractModel;

class Prices extends AbstractModel
{
    /**
     * @type string
     */
    protected $_eventPrefix = 'codiac_per_kg_pricing';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Codiac\Search\Model\ResourceModel\Prices::class);
    }
}
