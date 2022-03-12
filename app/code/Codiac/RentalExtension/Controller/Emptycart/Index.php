<?php

namespace Codiac\RentalExtension\Controller\Emptycart;

use Magento\Framework\App\Action\Context;
use Codiac\RentalExtension\Model\Order as OrderModel;
use Codiac\RentalExtension\Helper\Html as HtmlHelper;
use Magento\Catalog\Model\Session;
use Magento\Checkout\Model\Cart as CartModel;

class Index extends \Magento\Framework\App\Action\Action
{
    private $orderModel;
    private $htmlHelper;
    private $session;
    private $cartModel;

    public function __construct(Context $context, OrderModel $orderModel, HtmlHelper $htmlHelper, Session $session, CartModel $cartModel)
    {
        $this->orderModel = $orderModel;
        $this->htmlHelper = $htmlHelper;
        $this->session = $session;
        $this->cartModel = $cartModel;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $postData = $this->getRequest()->getPostValue();
            //echo json_encode($response);

        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

    }

}
