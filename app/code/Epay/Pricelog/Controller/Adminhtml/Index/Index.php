<?php

namespace Epay\Pricelog\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Price Changes Log'));

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
