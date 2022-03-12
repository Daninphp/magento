<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codiac\Search\Block\Product;

use Magento\Framework\View\Element\Template;
use Codiac\Search\Block\Filters;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\App\Response\Http;

/**
 * Class ExtraInfo
 * @package Codiac\Search\Block\Product
 */
class ExtraInfo extends Template
{
    private $_filters;
    private $_categoryRepository;
    private $_httpd;

    public function __construct(Http $http, CategoryRepository $categoryRepository, Filters $filters, Template\Context $context, array $data = [])
    {
        $this->_filters = $filters;
        $this->_categoryRepository = $categoryRepository;
        $this->_httpd = $http;
        parent::__construct($context, $data);
    }

    public function getPriceInfo()
    {
        return  [
            'inkl. Lieferung und Abholung und Entsorgung',
            '(max. Tonnage:  1,20 t)',
        ];
    }

    private function getCategoryDetails()
    {
        if ($this->_filters->getCategoryId()) {
            return $this->_categoryRepository->get($this->_filters->getCategoryId(), $this->_storeManager->getStore()->getId());
        }

        return null;
    }

    public function getCategoryHinein()
    {
        if ($this->getCategoryDetails()) {
            return nl2br($this->getCategoryDetails()->getHinein());
        }

        $this->_httpd->setRedirect($this->getBaseUrl());
    }

    public function getCategoryNichtHinein()
    {
        if ($this->getCategoryDetails()) {
            return nl2br($this->getCategoryDetails()->getNichtHinein());
        }

        $this->_httpd->setRedirect($this->getBaseUrl());
    }
}
