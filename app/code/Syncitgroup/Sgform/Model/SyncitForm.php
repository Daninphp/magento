<?php

namespace Syncitgroup\Sgform\Model;

use Magento\Framework\Model\AbstractModel;
use Syncitgroup\Sgform\Model\ResourceModel\SyncitForm as ResourceModel;

class SyncitForm extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'syncit_form_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
