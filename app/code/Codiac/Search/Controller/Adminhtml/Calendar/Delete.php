<?php

namespace Codiac\Search\Controller\Adminhtml\Calendar;

use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Codiac\Search\Model\Calendar;

class Delete extends \Magento\Framework\App\Action\Action
{
    private $session;
    private $calendar;

    public function __construct(Context $context, Session $session, Calendar $calendar)
    {
        $this->session = $session;
        $this->calendar = $calendar;
        parent::__construct($context);
    }

    public function execute()
    {
//        $this->calendar->deleteAllRecords();die();

        if (isset($this->_request->getPostValue()['id'])) {
            $result = $this->calendar->deleteDate($this->_request->getPostValue()['id']);
            echo json_encode($result);
        }
    }
}
