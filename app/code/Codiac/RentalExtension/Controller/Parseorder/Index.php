<?php

namespace Codiac\RentalExtension\Controller\Parseorder;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;



class Index extends \Magento\Framework\App\Action\Action
{
    protected $formKey;
    protected $cart;
    protected $product;
    public function __construct(
        Context $context,
        FormKey $formKey,
        Cart $cart,
        Product $product) {
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->_request->getParams();
        $productId = $params['product_id'];
        $this->cart->addProduct($productId, $params);
        try {
            $this->cart->save();
            echo 'success';
        } catch (\Exception $exception) {
            echo $exception->getMessage();die();
        }
    }

}
