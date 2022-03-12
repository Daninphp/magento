<?php
/**
 * Responsible for order expiration emails
 *
 * @copyright   Copyright (c) Codiac
 */
declare(strict_types = 1);
namespace Codiac\Search\Model;

use Magento\Catalog\Model\Session;
use Codiac\Search\Helper\Order\EmailNotification as EmailHelper;
use Codiac\Search\Plugin\SyncPostCodes;
use Psr\Log\LoggerInterface;

class Cron
{
    const RUNNING = 'Sending Emails';

    /**
     * @var Session
     */
    protected $_session;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var EmailHelper
     */
    protected $_emailHelper;

    /**
     * @var SyncPostCodes
     */
    protected $_syncPostCodes;

    /**
     * Helper constructor.
     *
     * @param Session $session
     * @param LoggerInterface $logger
     * @param EmailHelper $emailHelper
     * @param SyncPostCodes $syncPostCodes
     */
    public function __construct(
        Session $session,
        LoggerInterface $logger,
        SyncPostCodes $syncPostCodes,
        EmailHelper $emailHelper

    ) {
        $this->_logger = $logger;
        $this->_session = $session;
        $this->_syncPostCodes = $syncPostCodes;
        $this->_emailHelper = $emailHelper;
    }

    public function sendEmails()
    {
        $check = $this->_session->getEmailNotifications();
        if(isset($check)){
            $this->_logger
                ->notice(__('Email order notification cron already running!'));
        } else {
            $this->_session->setEmailNotifications(self::RUNNING);
            $this->_emailHelper->_init();
            $this->_session->unsEmailNotifications();
        }
    }

    public function updatePostalCodes()
    {
        $check = $this->_session->getCheckPostalCodes();
        if(isset($check)){
            $this->_logger
                ->notice(__('Postal codes check cron already running!'));
        } else {
            $this->_session->setCheckPostalCodes(self::RUNNING);
            $this->_syncPostCodes->sync();
            $this->_session->unsCheckPostalCodes();
        }
    }
}
