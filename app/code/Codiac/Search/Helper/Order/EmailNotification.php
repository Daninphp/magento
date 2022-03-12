<?php
/**
 * Responsible for order expiration emails
 *
 * @copyright   Copyright (c) Codiac
 */
declare(strict_types = 1);

namespace Codiac\Search\Helper\Order;

use Magento\Framework\Config\Scope;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Theme\Block\Html\Header\Logo;
use Magenest\RentalSystem\Model\ResourceModel\RentalOrder\CollectionFactory;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigCollection;
use Psr\Log\LoggerInterface;

class EmailNotification
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Repository
     */
    protected $_assetRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Logo
     */
    protected $_logo;

    /**
     * @var CollectionFactory
     */
    protected $_rentalCollection;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var ConfigCollection
     */
    protected $_configCollection;

    /**
     * Helper constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param Repository $assetRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param Logo $logo
     * @param CollectionFactory $rentalCollection
     * @param LoggerInterface $logger
     * @param ConfigCollection $configCollection
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(StoreManagerInterface $storeManager, Repository $assetRepository, ScopeConfigInterface $scopeConfig, Logo $logo, CollectionFactory $rentalCollection, LoggerInterface $logger, ConfigCollection $configCollection)
    {
        $this->_assetRepository = $assetRepository;
        $this->_storeManager = $storeManager->getStore();
        $this->_scopeConfig = $scopeConfig;
        $this->_logo = $logo;
        $this->_rentalCollection = $rentalCollection;
        $this->_configCollection = $configCollection;
        $this->_logger = $logger;
    }


    /**
     * @return void
     */
    public function _init()
    {
        $this->getRentalOrders();
    }

    /**
     * @return string|null
     */
    private function getDaysBeforeExpiry()
    {
        return $this->_scopeConfig->getValue('email_notification_root/email_notification_general/shipping_time',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string|null
     */
    private function getFooterText()
    {
        return $this->_scopeConfig->getValue('email_notification_root/email_notification_general_text/email_footer',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string|null
     */
    private function getLogoUrl()
    {
        return $this->_configCollection->create()
                ->addFieldToFilter('path', 'design/header/logo_src')
                ->getFirstItem()
                ->getValue();
    }

    /**
     * @return string|null
     */
    private function getStoreEmail()
    {
        return $this->_configCollection->create()
                ->addFieldToFilter('path', 'trans_email/ident_sales/email')
                ->getFirstItem()
                ->getValue();
    }

    /**
     * @return string|null
     */
    private function getStoreName()
    {
        return $this->_configCollection->create()
                ->addFieldToFilter('path', 'trans_email/ident_sales/name')
                ->getFirstItem()
                ->getValue();
    }

    /**
     * @return void
     * @throws \Zend_Mail_Exception
     * @throws \Exception
     */
    private function getRentalOrders()
    {
        $collection = $this->_rentalCollection->create();
        $this->getLogoUrl() ? $logoUrl = $this->getLogoUrl() : $logoUrl = 'stores/1/logo.png';
        $this->getDaysBeforeExpiry() ? $daysBeforeExpiry = $this->getDaysBeforeExpiry() : $daysBeforeExpiry = 6;
        $today = date('Y-m-d');
        
        foreach ($collection as $orderItem) {
            if ($orderItem->getEndTime()) {
                $userDefinedExpiryDate = new \DateTime($orderItem->getEndTime());
                $userDefinedExpiryDate->modify("- ". $daysBeforeExpiry . " day");

                //Check if current date matches date minus days set in admin
                if ($userDefinedExpiryDate->format("Y-m-d") == $today) {
                    $this->sendEmail($orderItem, (int)$this->getDaysBeforeExpiry(), $logoUrl);
                    //Check if current date equals end date in rental order grid
                } else if ((new \DateTime($orderItem->getEndTime()))->format("Y-m-d") == $today) {
                    $this->sendEmail($orderItem, 0, $logoUrl);
                }
            }

            continue;
        }
    }

    /**
     * @param object $orderInformation
     * @param int $remainingDays
     * @param string $logoUrl
     *
     * @return string
     */
    private function mailHtml(object $orderInformation, int $remainingDays, string $logoUrl): string
    {
        $logoUrl = $this->_storeManager->getBaseUrl() . 'pub/media/logo/' . $logoUrl;
        $this->_storeManager->setCurrentStore(0);

        $remainingDays > 0
            ? $message = sprintf("Wir möchten Sie über den Ablauf Ihrer Bestellung informieren. Ihre Bestellung läuft in %u Tagen ab.", $remainingDays)
            : $message = ('Ihre Bestellung läuft heute ab. Wenn Sie sie verlängern möchten, klicken Sie auf den in dieser E-Mail angegebenen Link.');

        // Check if there is a footer message in admin
        $this->getFooterText()
            ? $footerSection = '<div><p>'. $this->getFooterText() .'</p></div>'
            : $footerSection = '';

        $html = '
        <head>
            <style>
            .rent-url {
                font-weight: bold;
                text-decoration: none;
                color: #255C8A;         
            }
            
            .rent-url:hover {  
                transition: all 2s ease-in-out;    
                text-decoration: underline
            }
            
            #customers {
              font-family: Arial, Helvetica, sans-serif;
              border-collapse: collapse;
              width: auto;
            }

            #customers td, #customers th {
              border: 1px solid #ddd;
              padding: 15px;
            }

            #customers tr:nth-child(even){background-color: #f2f2f2;}

            #customers tr:hover {background-color: #ddd;}

            #customers th {
              padding-top: 12px;
              padding-bottom: 12px;
              text-align: left;
              background-color: #255C8A;
              color: white;
            }
            </style>
        </head>
        <body style="padding: 10px">
        <div>
          <div style="text-align: center">
            <img src="'. $logoUrl . '" alt="'. $this->_logo->getLogoAlt() .'" style="background-color: #255c8a;display:block;height: 80px;border-radius: 10%;padding: 15px;" />
          </div>
            <h3>
                Hallo zusammen, 
            </h3>
            <p>
                '. $message .'
            </p>
            <p>
                Wenn Sie Ihre Mietdauer verlängern möchten, klicken Sie bitte auf den unten stehenden Link.
            </p>
        </div>
        <table id="customers">
            <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Addresse</th>
                    <th>Verfallsdatum</th>
                </tr>
            </thead>
            <tbody style="border-bottom: 1px solid #18344B;">';
        $html .= '
                <tr>
                    <td>
                        <div> '. $orderInformation->getTitle() . ' </div>
                    </td>
                    <td>
                        <div><span>'. $orderInformation->getCustomerAddress() . ' </span></div>
                    </td>
                    <td>
                        <div><span>'. $orderInformation->getEndTime() . ' </span></div>
                    </td>
                </tr>';
        $html .='
            </tbody>
        </table>
        <div style="margin: 10px 0">
            <a class="rent-url" href="' . $this->_storeManager->getBaseUrl() .'verlangern?orderId='. $orderInformation->getOrderIncrementId() . '&email=' . $orderInformation->getCustomerEmail() . '">'. 'Auftragserweiterung!' . '</a>
        </div>
        ';
        $html .= $footerSection;

        return $html;
    }

    /**
     * @param object $orderData
     * @param int $remainingDays
     * @param string $logoUrl
     *
     * @throws \Zend_Mail_Exception
     */
    private function sendEmail(object $orderData, int $remainingDays, string $logoUrl)
    {
        try {
            // Get html for the email
            $emailBodyHtml = $this->mailHtml($orderData, $remainingDays, $logoUrl);

            $email = new \Zend_Mail();
            $email->setSubject('Auftragserweiterung');
            $email->setBodyHtml($emailBodyHtml, 'UTF-8', 'text/html');
            $email->setFrom($this->getStoreEmail(), $this->getStoreName());
            $email->addTo($orderData->getCustomerEmail(), $orderData->getCustomerName()); //toEmail
            $email->send();
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage(),[]);
        }

    }


}