<?php

namespace Syncitgroup\Sgform\Controller\Form;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Captcha\Observer\CaptchaStringResolver;
use Magento\Framework\Serialize\Serializer\Json;
use Syncitgroup\Sgform\Model\SyncitFormFactory;
use Syncitgroup\Sgform\Model\ResourceModel\SyncitForm as SyncitResource;
use Magento\Framework\Escaper;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Syncitgroup\Sgform\Helper\Email as EmailHelper;

class Submit extends \Magento\Framework\App\Action\Action
{
    protected $_helper;

    protected $_actionFlag;

    protected $_messageManager;

    protected $_redirect;

    protected $_captchaStringResolver;

    protected $_json;

    protected $_syncitFormFactory;

    protected $_syncitResourceModel;

    protected $_escaper;

    protected $_eventManager;

    protected $_emailHelper;

    public function __construct(
        Context                                     $context,
        \Magento\Captcha\Helper\Data                $helper,
        \Magento\Framework\App\ActionFlag           $actionFlag,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        CaptchaStringResolver                       $captchaStringResolver,
        Json                                        $json,
        SyncitFormFactory                           $syncitFormFactory,
        SyncitResource                              $syncitResource,
        Escaper                                     $escaper,
        EventManager                                $eventManager,
        EmailHelper                                 $emailHelper
    )
    {
        $this->_helper = $helper;
        $this->_actionFlag = $actionFlag;
        $this->_messageManager = $messageManager;
        $this->_captchaStringResolver = $captchaStringResolver;
        $this->_json = $json;
        $this->_syncitFormFactory = $syncitFormFactory;
        $this->_syncitResourceModel = $syncitResource;
        $this->_escaper = $escaper;
        $this->_eventManager = $eventManager;
        $this->_emailHelper = $emailHelper;
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     * @throws NotFoundException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute()
    {
        try {
            // Captcha validation
            $formId = \Syncitgroup\Sgform\Helper\Config::SYNCIT_FORM_ID;
            $captcha = $this->_helper->getCaptcha($formId);
            if ($captcha->isRequired()) {
                if (!$captcha->isCorrect($this->_captchaStringResolver->resolve($this->getRequest(), $formId))) {
                    $wrongCaptcha = __('Incorrect CAPTCHA.');
                    $this->messageManager->addErrorMessage($wrongCaptcha);
                    $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                    echo $this->_json->serialize(['response_code' => 444, 'success' => false, 'message' => $wrongCaptcha]);
                    return;
                }
            }

            $postData = $this->getRequest()->getPostValue();
            // Filter values sent by customer
            $filteredData = $this->filterPostData($postData);

            if (empty($filteredData)) {
                echo $this->_json->serialize($this->defaultError());
                return;
            }

            // Set data from the form in DB and send email with the same data
            if ($this->setData($filteredData) && $this->sendEmail($filteredData)) {
                $this->triggerEvent($filteredData);
            } else {
                echo $this->_json->serialize($this->defaultError());
                return;
            }

            echo $this->_json->serialize(['response_code' => 200, 'success' => true, 'message' => __("Thank you for submitting to Syncit Group custom form!")]);
        } catch (\Exception $e) {
            echo $this->_json->serialize($this->defaultError($e->getMessage()));
        }
    }

    /**
     * @param $data
     * @return bool
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function setData($data)
    {
        $model = $this->_syncitFormFactory->create()->setData($data);
        $this->_syncitResourceModel->save($model);
        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    private function sendEmail($data): bool
    {
        $this->_emailHelper->sendEmail($data);
        return true;
    }

    /**
     * @param $data
     * @return void
     */
    private function triggerEvent($data)
    {
        $this->_eventManager->dispatch(\Syncitgroup\Sgform\Helper\Config::SYNCIT_EVENT, $data);
    }

    /**
     * @param $data
     * @return array
     */
    private function filterPostData($data): array
    {
        foreach ($data as $key => $element) {
            if ($key == 'captcha' || $key == 'form_key') continue;
            $filteredData[$key] = $this->_escaper->escapeHtml(stripslashes($element));
        }

        return $filteredData ?? [];
    }

    /**
     * @param $message
     * @return array
     */
    private function defaultError($message = null): array
    {
        return ['response_code' => 400, 'success' => false, 'message' => $message ?? __('Something went wrong, please contact administrator!')];
    }

}
