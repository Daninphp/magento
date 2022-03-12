<?php

namespace Syncitgroup\Sgform\Model\ResourceModel\SyncitForm;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Syncitgroup\Sgform\Model\ResourceModel\SyncitForm as ResourceModel;
use Syncitgroup\Sgform\Model\SyncitForm as Model;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'syncit_form_collection';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
