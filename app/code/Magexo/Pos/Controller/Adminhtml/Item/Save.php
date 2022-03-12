<?php
namespace Magexo\Pos\Controller\Adminhtml\Item;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magexo\Pos\Model\ItemFactory;

class Save extends \Magento\Framework\App\Action\Action
{
    private $itemFactory;

    public function __construct(
        Context $context,
        ItemFactory $itemFactory
    )
    {
        $this->itemFactory = $itemFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->itemFactory->create()
            ->setData($this->getRequest()->getPostValue()['general'])
            ->save();
        return $this->resultRedirectFactory->create()->setPath('pointofsale/index/index');
    }
}
