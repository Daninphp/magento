<?php

namespace Syncitgroup\Sgform\Block;

use Magento\Framework\View\Element\Template;

class Form extends Template
{

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getFormData()
    {
        return new \Magento\Framework\DataObject( [
            [
                'input_type' => 'text',
                'input_name' => 'Name',
                'input_id' => 'user_name'
            ],
            [
                'input_type' => 'text',
                'input_name' => 'Last Name',
                'input_id' => 'user_last_name'
            ],
            [
                'input_type' => 'email',
                'input_name' => 'Email',
                'input_id' => 'email'
            ],
        ]);
    }

    public function getFormAction()
    {
        return $this->getUrl('syncit_form/index/post', ['_secure' => true]);
    }
}
