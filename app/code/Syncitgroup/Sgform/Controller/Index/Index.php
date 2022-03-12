<?php

namespace Syncitgroup\Sgform\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Syncitgroup\Sgform\Helper\Config as ConfigHelper;

class Index extends \Magento\Framework\App\Action\Action
{
    private $_configHelper;

    public function __construct(Context $context, ConfigHelper $configHelper)
    {
        $this->_configHelper = $configHelper;
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        if ($this->isFormEnabled()) {
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        }

        $this->_redirect('home');
    }

    private function isFormEnabled()
    {
        return $this->_configHelper->isFormEnabled();
    }
}
