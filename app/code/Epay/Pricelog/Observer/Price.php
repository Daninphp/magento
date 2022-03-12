<?php
declare(strict_types = 1);

namespace Epay\Pricelog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Backend\Model\Auth\Session;
use Epay\Pricelog\Model\LogData;
use Epay\Pricelog\Helper\Config;

class Price implements ObserverInterface
{
    const TIME_ZONE = 'Europe/Berlin';

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var Session
     */
    protected $_authSession;

    /**
     * @var LogData
     */
    protected $_logData;

    /**
     * @var Config
     */
    protected $_configHelper;


    /**
     * Price constructor.
     * @param Config $configHelper
     * @param LogData $logData
     * @param Session $authSession
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct
    (
        Config $configHelper,
        LogData $logData,
        Session $authSession,
        ProductRepositoryInterface $productRepository
    )
    {
        date_default_timezone_set(self::TIME_ZONE);
        $this->_configHelper = $configHelper;
        $this->_logData = $logData;
        $this->_authSession = $authSession;
        $this->_productRepository = $productRepository;
    }

    /**
     * @return string|null
     */
    private function isEnabled()
    {
        return $this->_configHelper->isEnabled();
    }

    /**
     * @return string|null
     */
    private function isEmailEnabled()
    {
        return $this->_configHelper->isEmailEnabled();
    }

    public function execute(Observer $observer)
    {
        if ($this->isEnabled()) :
            try {
                $product = $observer->getProduct();
                $productSku = $product->getSku();
                $productName = $product->getName();
                $newPrice = round($product->getPrice(), 2);
                $oldPrice =  round($this->_productRepository->get($productSku)->getPrice(), 2);
                if ($newPrice != $oldPrice) {
                    $timeOfChange = date('Y-m-d H:i:s');
                    $adminUser = $this->_authSession->getUser();
                    $userInfo = $adminUser->getFirstname() . ' ' . $adminUser->getLastname() .', email: ' . $adminUser->getEmail();

                    $message = '';
                    $message .= __('Product: ')     . $productName . ', ';
                    $message .= __('SKU: ')         . $productSku . ', ';
                    $message .= __('Old Price: ')   . $oldPrice . ', ';
                    $message .= __('New Price: ')   . $newPrice . ', ';
                    $message .= __('Admin info: ')  . $userInfo . ', ';
                    $message .= __('Date/Time: ')   . $timeOfChange . '.';

                    $this->_logData->logInformation($message);

                    $data = [
                        'sku' =>            $productSku,
                        'product_name' =>   $productName,
                        'old_price' =>      $oldPrice,
                        'new_price' =>      $newPrice,
                        'admin_details' =>  $userInfo,
                        'changed_at' =>     $timeOfChange,
                    ];

                    $this->_logData->saveData($data);

                    if ($this->isEmailEnabled()) {
                        $this->_logData->sendEmail($productName, $message);
                    }
                }
            } catch (\Exception $e) {
                $this->_logData->_magentoLogger->error($e->getMessage());
            }
        endif;
    }
}
