<?php

namespace Codiac\RentalExtension\Controller\Test;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;
use Codiac\RentalExtension\Model\Ordercreate;



class Index extends \Magento\Framework\App\Action\Action
{
    protected $formKey;
    protected $cart;
    protected $product;
    protected $orderCreate;

    public function __construct(
        Context $context,
        FormKey $formKey,
        Cart $cart,
        Ordercreate $orderCreate,
        Product $product) {
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        $this->orderCreate = $orderCreate;
        parent::__construct($context);
    }
    public function execute()
    {
        $productId = 8; //yourproduct id like 2
        $params = array(
            'form_key' => $this->formKey->getFormKey(),
            'rent_from' => '02/03 00:00',
            'rentto' => '02/06 00:00',
            'product' => $productId,
            'qty'   => 1,
            'additional_options' => [
                'options' => [
                    '3' => '500.00_17_3_fixed'
                ],
                'rental_price' => '500.00',
                'rental_from' => '1612306800',
                'rental_to' => '1612396800',
                'rental_start' => '1612306800000',
                'rental_hours' => '25',
                'has_time' => '1',
            ],
            'options' => [
                '1' => ['2']
            ]
        );

        $this->orderCreate($params);echo 'sent to order create';
        $product = $this->product->load($productId);
        $this->cart->addProduct($product, $params);
        $this->cart->save();
    }

}
