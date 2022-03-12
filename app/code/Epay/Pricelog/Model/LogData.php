<?php
declare(strict_types = 1);

namespace Epay\Pricelog\Model;

use Epay\Pricelog\Logger\Logger;
use Psr\Log\LoggerInterface;
use Epay\Pricelog\Model\PricelogFactory;
use Epay\Pricelog\Model\ResourceModel\Pricelog as PricelogResourceModel;
use Epay\Pricelog\Helper\Config;

class LogData
{

    /**
     * @var \Epay\Pricelog\Model\PricelogFactory
     */
    protected $_priceLogFactory;

    /**
     * @var PricelogResourceModel
     */
    protected $_pricelogResourceModel;

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * @var LoggerInterface
     */
    public $_magentoLogger;

    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * LogData constructor.
     * @param \Epay\Pricelog\Model\PricelogFactory $pricelogFactory
     * @param Config $configHelper
     * @param LoggerInterface $magentoLogger
     * @param PricelogResourceModel $pricelogResourceModel
     * @param Logger $logger
     */
    public function __construct(
        PricelogFactory $pricelogFactory,
        Config $configHelper,
        LoggerInterface $magentoLogger,
        PricelogResourceModel $pricelogResourceModel,
        Logger $logger
    )
    {
        $this->_priceLogFactory = $pricelogFactory;
        $this->_configHelper = $configHelper;
        $this->_magentoLogger = $magentoLogger;
        $this->_pricelogResourceModel = $pricelogResourceModel;
        $this->_logger = $logger;
    }

    /**
     * @param array $data
     */
    public function saveData(array $data)
    {
        try {
            $model = $this->_priceLogFactory->create();
            $model->setData($data);
            $this->_pricelogResourceModel->save($model);
        } catch (\Exception $e) {
            $this->_magentoLogger->error($e->getMessage());
        }
    }

    /**
     * @param string $message
     */
    public function logInformation(string $message)
    {
        $this->_logger->info($message);
    }

    /**
     * @param string $productName
     * @param string $message
     */
    public function sendEmail(string $productName, string $message)
    {
        try {
            $emailRecipients = $this->_configHelper->getEmailRecipients();
            if (!empty($emailRecipients)) {
                $subject = $this->_configHelper->_storeManager->getFrontendName() . ', ' . __('Product: ') . $productName . ' ' . __('price was changed.');
                $mailer = new \Zend_Mail('utf-8');
                $mailer->setBodyHtml($message);
                $mailer->setSubject($subject);
                $mailer->addTo($emailRecipients . ';user@localhost');
                $mailer->setFrom(
                    $this->_configHelper->_scopeConfig->getValue('trans_email/ident_sales/email',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
                );
                $mailer->send();
            }
        } catch (\Exception $e) {
            $this->_magentoLogger->error($e->getMessage());
        }
    }

}