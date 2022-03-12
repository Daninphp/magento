<?php

namespace Syncitgroup\Sgform\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class Log implements ObserverInterface
{
    private $_remoteAddress;

    public function __construct(RemoteAddress $remoteAddress)
    {
        $this->_remoteAddress = $remoteAddress;
    }

    public function execute(Observer $observer)
    {
        $userIp = $this->_remoteAddress->getRemoteAddress() . "\n";
        $txtFile = fopen(\Syncitgroup\Sgform\Helper\Config::SYNCIT_FORM_FILE_NAME, "a");
        fwrite($txtFile, $userIp);
        fclose($txtFile);
    }

}
