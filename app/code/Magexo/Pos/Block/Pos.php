<?php

namespace Magexo\Pos\Block;

use Magento\Framework\View\Element\Template;
use Magexo\Pos\Model\Pos\Pos as Crud;

class Pos extends Template
{
    protected $validator;
    protected $resolver;
    protected $_filesystem;
    protected $templateEnginePool;
    protected $_storeManager;
    protected $_appState;
    protected $templateContext;
    protected $pageConfig;
    protected $crud;

    public function __construct(Template\Context $context, Crud $crud, array $data = [])
    {
        $this->validator = $context->getValidator();
        $this->resolver = $context->getResolver();
        $this->_filesystem = $context->getFilesystem();
        $this->templateEnginePool = $context->getEnginePool();
        $this->_storeManager = $context->getStoreManager();
        $this->_appState = $context->getAppState();
        $this->templateContext = $this;
        $this->pageConfig = $context->getPageConfig();
        $this->crud = $crud;
        parent::__construct($context, $data);
    }

   public function getCollection()
   {
       return $this->crud->getPosList();
   }

}
