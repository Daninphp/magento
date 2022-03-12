<?php

namespace Codiac\Search\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Codiac\Search\Plugin\SyncPostCodes;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_syncPostCodes;

    public function __construct(Context $context, SyncPostCodes $syncPostCodes)
    {
        $this->_syncPostCodes =$syncPostCodes;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $result */
        /** TODO sacuvati za nesto posle **/

//        $this->_syncPostCodes->sync();
        echo 'Default Module.';die();
//        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
