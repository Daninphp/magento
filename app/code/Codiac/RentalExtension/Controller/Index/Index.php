<?php

namespace Codiac\RentalExtension\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_cart;

    public function __construct(Context $context, Cart $cart) {
        $this->_cart = $cart;
        parent::__construct($context);
    }


    public function execute()
    {
        $this->_cart->truncate();
        $this->_cart->saveQuote();

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
