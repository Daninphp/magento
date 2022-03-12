<?php

namespace Codiac\Search\Controller\Adminhtml\Calendar;

use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Codiac\Search\Model\Calendar;

class Add extends \Magento\Framework\App\Action\Action
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

        if (isset($this->_request->getPostValue()['date'])) {
            $result = $this->calendar->insertDate($this->_request->getPostValue()['date']);
            echo json_encode($result);
        }

    }
}
