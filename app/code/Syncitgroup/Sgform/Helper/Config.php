<?php

namespace Syncitgroup\Sgform\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const SYNCIT_FORM_ID = 'syncit_form';

    const SYNCIT_EVENT = 'syncit_group_form';

    const SYNCIT_FORM_FILE_NAME = 'syncit_group.txt';

    const XML_FORM_ENABLED = 'form/form/form_enabled';

    const XML_EMAIL_RECIPIENT = 'form/form/email_recipient';

    protected $_config;

    /**
     * @param ScopeConfigInterface $config
     */
    public function __construct(ScopeConfigInterface $config)
    {
        $this->_config = $config;
    }

    /**
     * @return bool
     */
    public function isFormEnabled()
    {
        return $this->_config->isSetFlag(self::XML_FORM_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getEmailRecipient()
    {
        return $this->_config->getValue(self::XML_EMAIL_RECIPIENT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getEmailSender()
    {
        return $this->_config->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }
}
