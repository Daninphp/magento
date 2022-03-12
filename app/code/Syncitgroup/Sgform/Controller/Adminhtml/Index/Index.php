<?php

namespace Syncitgroup\Sgform\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Form Data Log'));
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
