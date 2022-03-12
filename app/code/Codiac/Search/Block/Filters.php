<?php

namespace Codiac\Search\Block;

use Magento\Catalog\Block\Product\View;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Session;

class Filters extends Template
{
    private $view;
    private $session;

    private $mappingDays = [
        '1' => 'Montag',
        '2' => 'Dienstag',
        '3' => 'Mittwoch',
        '4' => 'Donnerstag',
        '5' => 'Freitag',
        '6' => 'Samstag',
        '7' => 'Sonntag',
    ];

    public function __construct(Template\Context $context, View $view, Session $session)
    {
        $this->view = $view;
        $this->session = $session;
        parent::__construct($context);
    }

    public function getSessionQuery()
    {
        if ($this->session->getPostQuery()) {
            return $this->session->getPostQuery();
        }
    }

    public function getSearchString()
    {
        if ($this->session->getSearchString()) {
            return $this->session->getSearchString();
        }
    }

    public function getCategoryName()
    {
        if ($this->session->getCategoryName()) {
            return $this->session->getCategoryName();
        }
    }

    public function getCategoryId()
    {
        if ($this->session->getCategoryId()) {
            return $this->session->getCategoryId();
        }
    }

    public function getProductSize()
    {
        $product = $this->view->getProduct();

        if (isset($product)) {
            return $product->getResource()->getAttribute('size')->getFrontend()->getValue($product);
        }

    }

    public function getProductTypeId()
    {
        return $this->view->getProduct()->getType();
    }

    public function getSizeAttributeId()
    {
        if ($this->session->getSizeAttributeId()) {
            return $this->session->getSizeAttributeId();
        }
    }

    public function getLatitudeLongitude()
    {
        if ($this->session->getLatitude() && $this->session->getLongitude()) {
            return ['ltd' => $this->session->getLatitude(), 'lng' => $this->session->getLongitude()];
        }
    }

    public function getCityName()
    {
        if ($this->session->getCityName()) {
            return $this->session->getCityName();
        }
    }

}