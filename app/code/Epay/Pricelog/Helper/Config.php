<?php
declare(strict_types = 1);

namespace Epay\Pricelog\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{

    const IS_MODULE_ENABLED = 'pricelog_data/pricelog_enable_settings/pricelog_enable_bool';

    const IS_GRID_ENABLED = 'pricelog_data/pricelog_enable_settings/pricelog_enable_grid';

    const IS_EMAIL_ENABLED = 'pricelog_data/pricelog_enable_email/pricelog_email_bool';

    const EMAIL_RECIPIENTS = 'pricelog_data/pricelog_enable_email/pricelog_enabled_email_recipient';


    /**
     * @var StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var ScopeConfigInterface
     */
    public $_scopeConfig;


    /**
     * Helper constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->_storeManager = $storeManager->getStore();
        $this->_scopeConfig = $scopeConfig;
    }


    /**
     * @return string|null
     */
    public function isEnabled()
    {
        return $this->_scopeConfig->getValue(self::IS_MODULE_ENABLED,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string|null
     */
    public function isGridEnabled()
    {
        return $this->_scopeConfig->getValue(self::IS_GRID_ENABLED,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string|null
     */
    public function isEmailEnabled()
    {
        return $this->_scopeConfig->getValue(self::IS_EMAIL_ENABLED,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string|null
     */
    public function getEmailRecipients()
    {
        return $this->_scopeConfig->getValue(self::EMAIL_RECIPIENTS,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


}
