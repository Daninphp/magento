<?php

namespace Smartweb\Auto\Controller\Search;

use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Smartweb\Auto\Helper\Data as HelperData;
use Smartweb\Auto\Model\Data;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $session;

    protected $_serializer;

    protected $_dataModel;


    /**
     * @param Context $context
     * @param Session $session
     * @param SerializerInterface $serializer
     * @param Data $dataModel
     */
    public function __construct(Context $context, Session $session, SerializerInterface $serializer, Data $dataModel)
    {
        $this->session      = $session;
        $this->_dataModel   = $dataModel;
        $this->_serializer  = $serializer;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['markId'])) {
                $this->session->setMarkId($postData['markId']);
                $data = $this->_dataModel->getModels(HelperData::MODEL_ATTR_CODE);
                echo $this->_serializer->serialize($data);
            } else if ($postData['modelId']) {
                $this->session->setModelId($postData['modelId']);
                $data = $this->_dataModel->getModels(HelperData::TYPE_ATTR_CODE);
                echo $this->_serializer->serialize($data);
            } else if ($postData['typeId']) {
                $this->session->setTypeId($postData['typeId']);
                $data = $this->_dataModel->getModels(HelperData::BULB_TYPE_CODE);
                echo $this->_serializer->serialize($data);
            } else {
                $markaAutomobila    = $this->session->getMarkId();
                $modelAutomobila    = $this->session->getModelId();
                $tipAutomobila      = $this->session->getTypeId();
                $vrstaSijalice      = $postData['bulbId'];

                echo $this->_dataModel->findResults($markaAutomobila, $modelAutomobila, $tipAutomobila, $vrstaSijalice);
            }
            
        } catch (\Exception $e){
            $this->_redirect($this->_redirect->getRefererUrl());
        }

    }
}
