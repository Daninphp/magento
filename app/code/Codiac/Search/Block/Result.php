<?php

namespace Codiac\Search\Block;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Session;
use Magento\Catalog\Block\Product\View;

class Result extends View
{
    public function getText()
    {
        return 'Text from Result.php';
    }
}