<?php

namespace Codiac\Search\Controller\Search;

use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Codiac\Search\Model\GpsLocation as LocatorModel;

class Locator extends \Magento\Framework\App\Action\Action
{
    private $locatorModel;

    public function __construct(Context $context, LocatorModel $locatorModel)
    {
        $this->locatorModel = $locatorModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $result = $this->locatorModel->getNearestPlaceAvailableForShipping($data);

        echo $result;
    }
}
