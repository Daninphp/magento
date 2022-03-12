<?php

namespace Codiac\Search\Block;

use Magento\Catalog\Block\Product\View;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Session;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Codiac\Search\Model\ResourceModel\Exclusion\CollectionFactory as ExclusionCollection;
use Codiac\Search\Model\ResourceModel\Notification\CollectionFactory as NotificationCollection;

class Delivery extends Template
{
    private $view;
    private $session;
    private $logger;
    private $config;
    private $exclusionCollection;
    private $_notificationCollection;
    private $today;

    public function __construct(Template\Context $context, View $view, Session $session, LoggerInterface $logger, ScopeConfigInterface $config, ExclusionCollection $exclusionCollection, NotificationCollection $notificationCollection)
    {
        setlocale(LC_TIME, 'de_DE', 'deu_deu');
        $this->today = utf8_encode(strftime('%A'));
        $this->view = $view;
        $this->session = $session;
        $this->logger = $logger;
        $this->config = $config;
        $this->_notificationCollection = $notificationCollection;
        $this->exclusionCollection = $exclusionCollection;
        parent::__construct($context);
    }
    private function returnFinsetArray(array $attributes)
    {
        $idArray = array();
        foreach ($attributes as $attribute) {
            $idArray[]['finset'] = [$attribute];
        }

        return $idArray;
    }

    private function getFrontEndLabel(object $product, string $attributeCode): array
    {
        return explode(',', preg_replace('/\s+/', '', $product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($product)));
    }

    private function getPostalCode()
    {
        if ($this->session->getSearchString()) {
            return $this->session->getSearchString();
        }
    }

    private function disableAllShipping()
    {
        return $this->config->getValue('shipping_system/shipping_general/shipping_value', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getShippingHours()
    {
        return $this->config->getValue('shipping_system/shipping_hours/shipping_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    private function getProductNotification()
    {
        $productId = $this->view->getProduct()->getId();
        $dayOfTheWeek = date("N");
        $filterDaysArray = $this->returnFinsetArray([$dayOfTheWeek]);

        $collection = $this->_notificationCollection->create()
            ->addFieldToSelect('product_id')
            ->addFieldToSelect('shipping_hours')
            ->addFieldToSelect('days_id')
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('days_id', $filterDaysArray);
        if (!empty($collection->getData())) {
            return $collection->getFirstItem()->getProductComment();
        }
    }

    private function getExclusionCalendarDays(string $todayDate) : bool
    {
        $collection = $this->exclusionCollection
            ->create()
            ->addFieldToFilter('exclusion_date', array('eq' => $todayDate))->getFirstItem();

        if(!empty($collection->getData())) {
            return true;
        }

        return false;

    }

    public function getExclusionAvailability()
    {
        $today = $this->today;
        $tomorrowDate = date('Y-m-d', strtotime("+1 day"));
        $_product = $this->view->getProduct();
        $currentTime = \DateTime::createFromFormat('H:i', date('H:i'));

        if ($this->disableAllShipping()) {
            return __('All shipping has temporarily been disabled, please come back later.');
        }

        if ($this->getExclusionCalendarDays($tomorrowDate)) {
            return __('Shipping can not be executed for tomorrow.');
        }

        //Deprecated, now this table displays shipping hours for products
//        if ($this->getProductNotification()) {
//            return __($this->getProductNotification());
//        }

        $postalCodeDayHour = $_product->getExclusionPostDayHour(); // get exclusion information from product attribute
        $searchPostalCode = $this->getPostalCode(); // get user search postal code from session

        if ($postalCodeDayHour && $searchPostalCode) {
            $postalCodeDayHour = explode(';', preg_replace('/\s+/', '', $postalCodeDayHour)); // explode data from product attribute
            foreach ($postalCodeDayHour as $item) {
                if (strlen($item) > 5) {
                    try {
                        $postalCodeFirstNumber = substr(explode(',', $item)[0], 0, 1); // get first element of postal code from product attribute
                        $restrictionDay = explode(',', $item)[1];
                        $restrictionHours = explode(',', $item)[2];
                        $restrictionStartHour = explode('-', $restrictionHours)[0];
                        $restrictionEndHour = explode('-', $restrictionHours)[1];
                        if ($postalCodeFirstNumber == substr($searchPostalCode, 0, 1) && ($today == $restrictionDay)) {
                            $startTimeOfShippingExclusion = \DateTime::createFromFormat('H:i', trim($restrictionStartHour));
                            $endTimeOfShippingExclusion = \DateTime::createFromFormat('H:i', trim($restrictionEndHour));
                            if ($currentTime > $startTimeOfShippingExclusion && $currentTime < $endTimeOfShippingExclusion) {
                                return __('All trucks are currently busy for this region, please come back at the later hour.');
                            }
                        }
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                        continue;
                    }
                }
            }
        }

        if (false): // check later on what kind of testing needs to be done
            $exclusionDays = $this->getFrontEndLabel($_product, 'exclusion_days');
            $exclusionHours = $this->getFrontEndLabel($_product, 'exclusion_hours');

            $startTimeOfShippingExclusion = \DateTime::createFromFormat('H:i', trim(min($exclusionHours)));
            $endTimeOfShippingExclusion = \DateTime::createFromFormat('H:i', trim(max($exclusionHours)));

            if (in_array($today, $exclusionDays)) {
                if ($currentTime > $startTimeOfShippingExclusion && $currentTime < $endTimeOfShippingExclusion) {
                    return false;
                }
            }

            return true;
        endif;

    }


}