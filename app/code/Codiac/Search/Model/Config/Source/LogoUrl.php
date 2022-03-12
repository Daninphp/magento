<?php

namespace Codiac\Search\Model\Config\Source;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class LogoUrl  extends \Magento\Config\Block\System\Config\Form\Field
{

    protected $_scopeConfig;
//
//    public function __construct(\Magento\Framework\View\Element\Template\Context $context, ScopeConfigInterface $scopeConfig) {
//        $this->_scopeConfig = $scopeConfig;
//    }
//
//    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
//    {
////        return '<input type="text" value="' . $this->_scopeConfig->getValue('design/header/logo_src',\Magento\Store\Model\ScopeInterface::SCOPE_STORE) . '" width="50"/>';
//
//        return $this->_scopeConfig->getValue('design/header/logo_src',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
//    }
//
//}



    /**
     * Color constructor.
     *
     * @param Context $context
     * @param array $data
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    )
    {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $element->setValue($this->_scopeConfig->getValue('design/header/logo_src',\Magento\Store\Model\ScopeInterface::SCOPE_STORE))->getElementHtml();
    }
}