<?php

namespace Codiac\Search\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;

class CreateInvoice implements ObserverInterface
{
    protected $invoiceService;
    protected $transaction;
    protected $invoiceSender;
    protected $orderRepository;
    protected $helper;
    protected $_scopeConfig;

    public function __construct(
        InvoiceService $invoiceService,
        Transaction $transaction,
        OrderRepositoryInterface $orderRepository,
        InvoiceSender $invoiceSender,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->orderRepository = $orderRepository;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @return string|null
     */
    private function isEnabled()
    {
        return $this->_scopeConfig->getValue('email_invoice_notification_root/email_invoice_notification_general/email_invoice_notification_value',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function execute(Observer $observer)
    {
        if ($this->isEnabled()) :
            try {
                $orderId = $observer->getEvent()->getOrderIds()[0]; //it should be order id
                $order = $this->orderRepository->get($orderId);
                if ($order->canInvoice()) {
                    $invoice = $this->invoiceService->prepareInvoice($order);
                    $invoice->register();
                    $invoice->save();
                    $transactionSave = $this->transaction->addObject(
                        $invoice
                    )->addObject(
                        $invoice->getOrder()
                    );
                    $transactionSave->save();
                    $this->invoiceSender->send($invoice);
                    //Send Invoice mail to customer
                    $order->addStatusHistoryComment(
                        __('Notified customer about invoice creation #%1.', $invoice->getId())
                    )
                        ->setIsCustomerNotified(true)
                        ->save();
                }
            } catch (\Exception $e) {
                return null;
            }
        endif;
    }
}