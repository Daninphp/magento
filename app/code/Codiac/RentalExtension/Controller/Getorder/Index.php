<?php

namespace Codiac\RentalExtension\Controller\Getorder;

use Magento\Framework\App\Action\Context;
use Codiac\RentalExtension\Model\Order as OrderModel;
use Codiac\RentalExtension\Helper\Html as HtmlHelper;
use Magento\Catalog\Model\Session;

class Index extends \Magento\Framework\App\Action\Action
{
    private $orderModel;
    private $htmlHelper;
    private $session;

    public function __construct(Context $context, OrderModel $orderModel, HtmlHelper $htmlHelper, Session $session)
    {
        $this->orderModel = $orderModel;
        $this->htmlHelper = $htmlHelper;
        $this->session = $session;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $postData = $this->getRequest()->getPostValue();
            $orderDetails = $this->orderModel->getOrderData($postData);
            $response = [];
            if (isset($orderDetails['error'])) {
                $response['error'] = $orderDetails['error'];
            }
            else {
                $response['success'] = $this->htmlHelper->getOrderDetails($orderDetails);
            }

            echo json_encode($response);
            exit();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        /** @var \Magento\Framework\Controller\Result\Raw $result */
        /** TODO sacuvati za nesto posle **/
//        echo 'Enter default search for this module';
    }

}
