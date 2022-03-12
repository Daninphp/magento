<?php

namespace Syncitgroup\Sgform\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SyncitForm extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'syncit_form_resource_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('syncit_form', 'id');
        $this->_useIsObjectNew = true;
    }
}
