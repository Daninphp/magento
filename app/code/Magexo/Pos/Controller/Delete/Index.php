<?php
namespace Magexo\Pos\Controller\Delete;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magexo\Pos\Model\Pos\Pos as Crud;

class Index extends Action {

    /**
     * Index constructor.
     * @param Context $context
     */

    private $crud;

    public function __construct(
        Context $context,
        Crud $crud
    )
    {
        parent::__construct($context);
        $this->crud = $crud;
    }


    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id = $this->getRequest()->getPostValue()['id'];
        if(isset($id)){
            $this->crud->deleteProduct($id);
        }
    }

}
