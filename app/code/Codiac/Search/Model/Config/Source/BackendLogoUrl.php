<?php

namespace Codiac\Search\Model\Config\Source;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class BackendLogoUrl  implements \Magento\Framework\Option\ArrayInterface
{

    protected $_scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
    }

    public function toOptionArray()
    {
        $result  = [
            [
                'value' => $this->_scopeConfig->getValue('design/header/logo_src',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'label' => __($this->_scopeConfig->getValue('design/header/logo_src',\Magento\Store\Model\ScopeInterface::SCOPE_STORE)),
            ],
        ];
        return $result;
    }
}