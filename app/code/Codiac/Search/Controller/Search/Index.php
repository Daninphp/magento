<?php

namespace Codiac\Search\Controller\Search;

use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Codiac\Search\Model\Search as SearchModel;
use Codiac\Search\Helper\Html as Helper;
use Codiac\Search\Helper\InsertAttributes;
use Codiac\Search\Helper\Order\EmailNotification;

class Index extends \Magento\Framework\App\Action\Action
{
    private $session;
    private $searchModel;
    private $helper;
    private $insertAttributes;
    private $emailNotification;

    public function __construct(Context $context, Session $session, SearchModel $searchModel, Helper $helper, InsertAttributes $insertAttributes, EmailNotification $emailNotification)
    {
        $this->session = $session;
        $this->helper = $helper;
        $this->insertAttributes = $insertAttributes;
        $this->searchModel = $searchModel;
        $this->emailNotification = $emailNotification;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $result */
//        $this->emailNotification->_init();die();

//        $this->insertAttributes->init();echo 'done';die();

        try {
            $searchString = trim(strtolower($this->getRequest()->getPostValue()['searchString']));

            if($this->session->getSearchString()) {
               $this->session->unsSearchString();
            }
            $this->session->setSearchString($searchString);
            
        } catch (\Exception $e){
            $this->_redirect($this->_redirect->getRefererUrl());
        }

        if (strlen($searchString) > 3) {
//            $this->session->setSearchString($searchString);

            $categoryData = $this->searchModel->getProductCollection($searchString);

            $html = $this->helper->getHtml($categoryData);

            echo $html;
            exit(); // Without this, when user is logged in, there is "Header already sent" error
        } else {
            echo json_encode('Bitte geben Sie mehr als 3 Ziffern ein!');
        }

    }
}
