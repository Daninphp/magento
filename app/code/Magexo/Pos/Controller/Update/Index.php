<?php
namespace Magexo\Pos\Controller\Update;

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
        $data = $this->getRequest()->getPostValue();
//echo'<pre>';print_r($data);die();
        if(!empty($data)){
            if($data['type'] == 'read'){
                $collection = $this->crud->getPosList()->getData();
                echo json_encode($collection);
            } elseif($data['type'] == 'update' && $data['pos_id'] !== '') {
                $this->crud->updatePos($data);
            } elseif($data['type'] == 'create' && $data['pos_id'] !== '' && $data['name'] !== '' && $data['address'] !== '' && $data['availability'] !== '') {
                $this->crud->createPos($data);
            } else {
                echo json_encode('Error, missing data');
            }
        } else {
            echo json_encode('Error, no data provided');
        }
    }

}
