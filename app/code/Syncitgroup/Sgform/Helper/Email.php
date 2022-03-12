<?php

namespace Syncitgroup\Sgform\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Syncitgroup\Sgform\Helper\Config as ConfigHelper;

class Email extends AbstractHelper
{
    const TEMPLATE_ID = 'syncit_form';

    protected $transportBuilder;

    protected $storeManager;

    protected $inlineTranslation;

    protected $_configHelper;

    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state,
        ConfigHelper $configHelper
    )
    {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        $this->_configHelper = $configHelper;
        parent::__construct($context);
    }

    public function sendEmail($templateVars)
    {
        // this is an example and you can change template id,fromEmail,toEmail,etc as per your need.
        $fromEmail = $this->_configHelper->getEmailSender();  // sender Email id
        $fromName = 'Admin';             // sender Name
        $toEmail = $this->_configHelper->getEmailRecipient(); // receiver email id

        try {

            $storeId = $this->storeManager->getStore()->getId();

            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier(self::TEMPLATE_ID, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }
}
