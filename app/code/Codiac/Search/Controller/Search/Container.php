<?php

namespace Codiac\Search\Controller\Search;

use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Codiac\Search\Model\Search as SearchModel;
use Codiac\Search\Helper\Html as Helper;

class Container extends \Magento\Framework\App\Action\Action
{
    private $session;
    private $searchModel;
    private $helper;

    public function __construct(Context $context, Session $session, SearchModel $searchModel, Helper $helper)
    {
        $this->session = $session;
        $this->helper = $helper;
        $this->searchModel = $searchModel;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $postData = $this->getRequest()->getPostValue();
        } catch (\Exception $e){
            $this->_redirect($this->_redirect->getRefererUrl());
        }

        if (!empty($postData)) {
            if ($this->session->getPostQuery()) {
                $this->session->unsPostQuery();
            }
            $containerSizes = $this->searchModel->getContainerSizeCollection($postData);
            $this->session->setPostQuery($postData);
            $response = $this->helper->getContainerTypes($containerSizes);

            echo $response;

        } else {
            $this->_redirect($this->_redirect->getRefererUrl());
        }

    }

}
