<?php


namespace Codiac\Search\Model;

use Magento\Framework\Model\AbstractModel;

class Locator extends AbstractModel
{
    protected $_eventPrefix = 'codiac_search_locator';

    protected function _construct()
    {
        $this->_init(\Codiac\Search\Model\ResourceModel\Locator::class);
    }

}
