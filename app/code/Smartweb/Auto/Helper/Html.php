<?php

namespace Smartweb\Auto\Helper;

use Magento\Store\Model\StoreManagerInterface;

class Html
{
    const CATALOG_URL = 'catalog/product';

    protected $_storeManager;

    protected $_baseUrl;

    protected $_imageUrl;

    /**
     * @param StoreManagerInterface $storeManager
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
        $this->_baseUrl = $storeManager->getStore()->getBaseUrl();
        $this->_imageUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::CATALOG_URL;
    }

    /**
     * @param $collection
     * @return string
     */
    public function generateHtml($collection): string
    {
        $html = '<div><ol class="products list items product-items">';
        foreach ($collection as $product) {
            $html .= '<li class="item product product-item">';
                $html .= '<div class="product-item-info">';
                    $html .= '<a class="product photo product-item-photo" href="'. $this->_baseUrl . $product->getUrlKey() .'.html">';
                        $html .= '<span class="product-image-container" style="width: 240px">';
                            $html .= '<span class="product-image-wrapper" style="padding-bottom: 125%">';
                                $html .= '<img class="product-image-photo" src="'. $this->_imageUrl . $product->getImage() .'" />';
                            $html .= '</span>';
                        $html .= '</span>';
                    $html .= '</a>';
                    $html .= '<div class="product details product-item-details">';
                        $html .= '<strong class="product name product-item-name">';
                            $html .= '<a class="product-item-link" href="'. $this->_baseUrl . $product->getUrlKey() .'.html">';
                                $html .= $product->getName();
                            $html .= '</a>';
                        $html .='</strong>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</li>';
        }

        $html .= '</ol></div>';

        return $html;
    }
}