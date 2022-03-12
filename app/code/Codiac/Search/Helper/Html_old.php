<?php

namespace Codiac\Search\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Asset\Repository;
use Magenest\RentalSystem\Block\Product\Rental;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;

class Html
{

    protected $storeManager;
    protected $assetRepository;
    protected $_rental;
    protected $_scopeConfig;
    protected $_session;

    private $mappingSizes = [
        '7' => 'M',
        '8' => 'M',
        '10' => 'L',
        '18' => 'XL',
        '24' => 'XL',
        '38' => 'XLL',
    ];


    public function __construct(StoreManagerInterface $storeManager, Repository $assetRepository, Rental $rental, ScopeConfigInterface $scopeConfig, Session $_session)
    {
        setlocale(LC_TIME, 'de_DE', 'deu_deu');
        $this->assetRepository = $assetRepository;
        $this->storeManager = $storeManager->getStore();
        $this->_rental = $rental;
        $this->_session = $_session;
        $this->_scopeConfig = $scopeConfig;
    }

    private function getTaxText()
    {
        if ($this->_session->getCustomer()->getGroupId() == '3') {
            return '(inkl. Transport, exkl. MwSt.)';
        }

        return '(inkl. Transport, inkl. MwSt.)';
    }

    private function getMediaUrl()
    {
        return $this->storeManager->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    private function getHolidays()
    {
        $holidays = json_decode($this->_rental->getHolidays());
        $timeStampHolidays = [];
        foreach ($holidays as $holiday) {
//            $newDate = new Date('y/m/d',$holiday);
            $timeStampHolidays[] = strtotime($holiday);
        }

        return $timeStampHolidays;
    }


    private function getBootstrapClass(int $arrayToCount)
    {
        if (ceil(12 / $arrayToCount) < 3) {
            return 3;
        }

        return (ceil(12 / $arrayToCount));
    }

    private function getSizeLabel(string $sizeCode)
    {
        $searchString = (int)explode(' ', $sizeCode)[0];

        return $this->mappingSizes[$searchString];
    }

    private function getShippingHours()
    {
        return $this->_scopeConfig->getValue('shipping_system/shipping_hours/shipping_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    private function getShippingCost()
    {
        return $this->_rental->getShippingPrice();
    }

    public function getHtml($categoryData)
    {
        if (!isset($categoryData['error'])) {
            $html = '
                <div class="eael-dual-header">
				    <h2 class="title">
                        <span class="lead-color">ONLINE</span> 
                        <span>KONFIGURATOR</span>
				    </h2>
					<span class="eael-dch-svg-icon">
					    <i aria-hidden="true" class="fa fa-wrench" style="font-size: 40px; color: #fff"></i>
                    </span>				
                </div>';
            $html .= '<h2 class="wastetype">'. __('Bitte wählen Sie die Abfallart aus:*') .'</h2>';
            $html .= '<div class="response-details">';
            foreach ($categoryData[0] as $key => $category) {
                $html .=
                    '<div class="categories">
                        <div data-source="' . $category['url'] . '" class="category" id="' . $category['id'] . '">
                            <div class="categories-base">
                                <img src="' . $category['image'] .'" alt="waste-image">
                            </div>
                            <div class="categories-name">
                                <h3>' . utf8_encode($category['name']) . '</h3>
                            </div>
                        </div>
                    </div>
                ';

//                $html .= '<div class="modal fade" id="category' . $category['id'] . '" role="dialog">';
//                $html .= '<div class="modal-dialog">';
//                $html .= '<div class="modal-content">';
//                $html .= '<div class="modal-header">';
//                $html .= '
//                        <button type="button" class="close" data-dismiss="modal">
//                                <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
//                                <path d="M0.773926 24L23.7739 1" stroke="#22211F" stroke-width="2"></path>
//                                <path d="M0.773926 1L23.7739 24" stroke="#22211F" stroke-width="2"></path>
//                            </svg>
//                        </button>';
//                $html .= '<h4 class="modal-title">'. utf8_encode($category['name']) . '</h4>';
//                $html .= '</div>';
//                $html .= '<div class="modal-body">'. __($category['description']) . '</div>';
//                $html .= '</div>';
//                $html .= '</div>';
//                $html .= '</div>';
            }
            $html .= '</div>';
            $html .= '<div id="container-types-answer" class="row" style="min-height: 200px;"></div>';

            $html .= '<script src="' . $this->assetRepository->getUrlWithParams('Codiac_Search::js/container-search.js', []) .'"></script>';

            return $html;

        } else {
            return '<div><h3>' . $categoryData['error'] . '</h3></div>';
        }

    }

    public function getContainerTypes($containerTypes)
    {
        $shippingCost = $this->getShippingCost();
        $mediaUrl = $this->getMediaUrl();
        $shippingMessage =  $this->shippingHours();

        $html = '<h2 class="type">'. __('Welcher Muldentyp wird benötigt?') .'</h2>';
        $html .= '<p class="typetext" style="color: #fff; margin-bottom: 12px;">'. __('Min. 10 Tage Miete sind inkludiert.') .'</p>';
        $html .= '<div class="bar"><span><img class="truck-message" src="'. $mediaUrl .'/truck_back.png' .'" alt="Truck">' . $shippingMessage . '</span></div>';

        if (!isset($containerTypes['error'])) {
            foreach ($containerTypes[0] as $container) {
                $containerSize = strtok($container['size'], ' ');
//                $containerSize = $this->mappingSizes[$containerSize];

                $imagesHtml = '';
                foreach ($container['images'] as $key => $image) {
                    if ($key < 3) {
                        $imagesHtml .= '<div class="col-sm-3 container-images" style="background-image: url('. $image .')"></div>';
                    }
                }

                $html .=
                    '<div class="col-lg-4 col-sm-6 container-type-version" data-id="' . $container['id'] . '" data-source="' . $this->storeManager->getBaseUrl() . $container['url_key'] . '.html" >
                        <div class="containers-front">
                            <img src="'. $mediaUrl .'catalog/product' . strtolower($container['img']) .'" alt="Container Image"/>
                            <div class="container-grose">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#product' . $container['id'] .'">
                                    <span class="cat-info cat-info-container">i</span>
                                 </a>
                            </div>
                            <div class="border-top"></div>
                            <div class="container-type-version-name">
                                <h3>' . $container['name'] . '</h3>
                                <hr class="separator-border">
                                <h4 style="color: #000">  Preis: '. number_format($container['price'] + $shippingCost, 2,',','.') .' € <span style="font-size: 12px; color: #000"></span></h4><span class="span-tax">' . $this->getTaxText() . '</span>
                            </div>
                        </div>
                    </div>
                    ';
                $html .= '<div class="modal fade" id="product' . $container['id'] . '" role="dialog">';
                $html .= '<div class="modal-dialog">';
                $html .= '<div class="modal-content">';
                $html .= '<div class="modal-header">';
                $html .= '
                        <button type="button" class="close" data-dismiss="modal">
                                <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.773926 24L23.7739 1" stroke="#22211F" stroke-width="2"></path>
                                <path d="M0.773926 1L23.7739 24" stroke="#22211F" stroke-width="2"></path>
                            </svg>
                        </button>';
                $html .= '<h4 class="modal-title">'. __($container['name']) . '</h4>';
                $html .= '</div>';
                $html .=
                    '<div class="modal-body">
                        <div class="container-desc">'
                    . __($container['description']) .
                    '</div>
                        <div class="row modal-container-image">
                        ' . $imagesHtml . '
                        </div>
                        <div class="modal-container-message">
                            <h5>Immer noch unsicher, welche Größe Du brauchst?</h5>
                            <p>Schicke uns ein Foto von deinem Projekt. Wir können so besser abschätzen welche Größe Du benötigst</p>
                            <a class="btn btn-sec d-inline-block mb-4 px-4" href="' . $this->storeManager->getBaseUrl() . 'kontakt" target="_blank">Kontaktiere uns</a>
                        </div>
                    </div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= '<div id="container-product" class="col-sm-12">
                        <div class="weiter-total">
                            <a id="product-url-href" href="#">
                            <span class="animation-weiter" style="display: none">
                                <i class="fa fa-chevron-right" style="font-size: 16px"></i>
                                <i class="fa fa-chevron-right" style="font-size: 16px"></i>
                                <i class="fa fa-chevron-right" style="font-size: 16px"></i>
                            </span>
                            Weiter zu den Details&nbsp; 
                                <i class="fa fa-chevron-right" style="font-size: 16px;"></i>
                            </a>  
                        </div>
 
                        </div>';
            $html .= '<script src="' . $this->assetRepository->getUrlWithParams('Codiac_Search::js/container-product.js', []) .'"></script>';

            return $html;

        } else {
            return '<div><h3>' . $containerTypes['error'] . '</h3></div>';
        }

    }

    private function shippingHours()
    {
        $html = '';
        date_default_timezone_set("Europe/Berlin");
        $dayOfTheWeek = date("N");
        $endTime = $this->getShippingHours();
        $limitTime = new \DateTime($endTime);
        $timeNow = new \DateTime();
        $showTime = false;
        if ($timeNow->getTimestamp() < $limitTime->getTimestamp()) {
            $showTime = true;
        }

        $format = new \IntlDateFormatter('de_DE', \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE, NULL, NULL, "LLLL");

        $tomorrowShippingDate = date("Y/m/d", strtotime("+ 1 day"));

        $i = 1;
        //check if tomorrow date is in holidays array, if so add another day and check again...
        while (true) {
            $tmp = strtotime($tomorrowShippingDate);
            $day = date('w', $tmp);
            /** TODO implement check for if it's Sunday ($day) */
            if (!in_array($tmp, $this->getHolidays())) {
                break;
            }
            $tomorrowShippingDate = date('Y/m/d', strtotime("+ $i day"));
            $i++;
        }

        $monthName = datefmt_format($format, mktime(0, 0, 0, explode("/",$tomorrowShippingDate)[1]));
        $day = explode('/', $tomorrowShippingDate)[2];

        if ($showTime && $dayOfTheWeek != 6) {
            $html .=  'Lieferung bis '. $day . ' ' . $monthName . ' bei Bestellung innerhalb von '.'<label id="counting-time"></label>';
            $html .= '<script src="' . $this->assetRepository->getUrlWithParams('Codiac_Search::js/shippingHour.js', []) .'"></script>';
        } else {
            $html .=  __('Lieferung innerhalb von 2 Tagen möglich.');
        }

        return $html;
    }

}