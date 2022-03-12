<?php

namespace Codiac\Search\Controller\Adminhtml\System;

use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Config\Model\Config\Factory;
use Psr\Log\LoggerInterface;

class Save extends \Magento\Framework\App\Action\Action
{
    private $session;
    private $_configFactory;
    private $_logger;

    public function __construct(Context $context, Session $session, Factory $configFactory, LoggerInterface $logger)
    {
        $this->session = $session;
        $this->_configFactory = $configFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        if (isset($this->getRequest()->getPost()['value']) || isset($this->getRequest()->getPost()['hour'])) {
            $boolValue = $this->getRequest()->getPost()['value'];
            $hourValue = $this->getRequest()->getPost()['hour'];

            $configData = [
                'section' => 'shipping_system',
                'website' => '',
                'groups' => [
                    'shipping_general' => [
                        'fields' => [
                            'shipping_value' => [
                                'value' => $boolValue
                            ]
                        ]
                    ],
                    'shipping_hours' => [
                        'fields' => [
                            'shipping_time' => [
                                'value' => $hourValue
                            ]
                        ]
                    ]
                ]
            ];

            try {
                /** @var \Magento\Config\Model\Config $configModel  */

                $configModel = $this->_configFactory->create(['data' => $configData]);
                $configModel->save();
                if ($this->getRequest()->getPost()['sender'] != 'update-shipping-hour') {
                    $boolValue == 0 ? $message = __("Versand erfolgreich für alle Container aktiviert!") : $message = __("Versand wurde deaktiviert!");
                    $messageDiv = '#return-message';
                } else {
                    $message = __("Stunde erfolgreich aktualisiert!");
                    $messageDiv = '#udpated-hour-message';
                }
                echo json_encode(['success'=> true, 'messageDiv' => $messageDiv ,'message' => $message]);
            } catch (\Exception $e) {
                $this->_logger->error($e->getMessage());
                echo json_encode(['success' => false, 'messageDiv' => $messageDiv, 'message' => 'Etwas ist schief gelaufen. Bitte versuche es erneut!']);
            }
        }

    }
}
