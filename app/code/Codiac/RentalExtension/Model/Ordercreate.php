<?php
namespace Codiac\RentalExtension\Model;

class Ordercreate extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    protected $_storeManager;

    protected $_customerFactory;

    protected $_productFactory;

    protected $_cartRepositoryInterface;

    protected $_shippingRate;


    protected $orderRepository;

    protected $cartManagementInterface;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Quote\Model\Quote\Address\Rate $rate,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        $this->_registry = $registry;
        $this->_storeManager = $storeManager;
        $this->_customerFactory = $customerFactory;
        $this->_objectManager = $objectManager;
        $this->_productFactory = $productFactory;
        $this->_customerRepository = $customerRepository;
        $this->_cartRepositoryInterface = $cartRepository;
        $this->_shippingRate = $rate;
        $this->orderRepository = $orderRepository;
        parent::__construct($context,$registry);
    }

    private function orderData()
    {
        $additionalOptions = [
            'options'[4] => '500.00_5_4_fixed',
            'rental_price' => '522.00',
            'rental_from' => '1612911600',
            'rental_to' => '1612915200',
            'rental_start' => '1612911600000',
            'rental_hours' => '1',
            'has_time' => '1',
        ];

        $orderData = [
            'currency_id' => 'EUR',
            'email' => 'danindragosavac@gmail.com', //buyer email id
            'shipping_address' => [
                'firstname' => 'Test',
                'lastname' => 'Order',
                'street' => '6146 Honey Bluff Parkway',
                'city' => 'Wienna',
                'country_id' => 'AT',
                'region' => '102',
                'postcode' => '1100',
                'telephone' => '5552293326',
                'fax' => '12345',
                'save_in_address_book' => 1
            ],
            'items' => [
                [
                    'product_id' => '4',
                    'qty' => 1,
                    'rentfrom' => $postData['datefrom'],
                    'rentto' => $postData['dateto'],
                    'options' => [
                        'additional_options' => $additionalOptions, // 'option_id' => 'value'
                    ]
                ]
            ]
        ];
    }

    public function createOrder($orderData)
    {
        $response = array();
        $response['success']=FALSE;

        if(!count($orderData['items'])) {
            $response['error_msg'] = 'No Item to order';
        } else {
            $this->cartManagementInterface = $this->_objectManager->get('\Magento\Quote\Api\CartManagementInterface');

            //init the store id and website id
            $store = $this->_storeManager->getStore(1);
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();

            //init the customer
            $customer = $this->_customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->loadByEmail($orderData['email']);// load customer by email address

            //check the customer
            if (!$customer->getEntityId()) {

                //If not available then create this customer
                $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email'])
                    ->setPassword($orderData['email']);

                $customer->save();
            }

            //init the quote
            $cart_id = $this->cartManagementInterface->createEmptyCart();
            $cart = $this->_cartRepositoryInterface->get($cart_id);

            $cart->setStore($store);

            // if you have already buyer id then you can load customer directly
            $customer = $this->_customerRepository->getById($customer->getEntityId());
            $cart->setCurrency();
            $cart->assignCustomer($customer); //Assign quote to customer

            $_productModel = $this->_productFactory->create();
            //add items in quote
            foreach ($orderData['items'] as $item) {
                $product = $_productModel->load($item['product_id']);
//
                try {
                    // print_r($item); die();
                    $params = array('product' => $item['product_id'], 'qty' => $item['qty'], 'rentfrom' => '02/04 11:00', 'rentto' => '02/14 12:00');
                    if (array_key_exists('options', $item) && $item['options']) {
                        $params['options'] = json_decode(json_encode($item['options']), True);
                    }
                    if ($product->getTypeId() == 'configurable') {
                        $params['super_attribute'] = $item['super_attribute'];
                    } elseif ($product->getTypeId() == 'bundle') {
                        $params['bundle_option'] = $item['bundle_option'];
                        $params['bundle_option_qty'] = $item['bundle_option_qty'];
                    } elseif ($product->getTypeId() == 'grouped') {
                        $params['super_group'] = $item['super_group'];
                    }

                    $objParam = new \Magento\Framework\DataObject();
                    $objParam->setData($params);
                    // print_r($objParam); die();
                    $cart->addProduct($product, $objParam);

                } catch (\Exception $e) {
                    $response[$item['product_id']]= $e->getMessage();
                }
            }

            //Set Address to quote
            $cart->getBillingAddress()->addData($orderData['shipping_address']);
            $cart->getShippingAddress()->addData($orderData['shipping_address']);

            // Collect Rates and Set Shipping & Payment Method
            $this->_shippingRate
                ->setCode('flatrate_flatrate')
                ->getPrice(1);

            $shippingAddress = $cart->getShippingAddress();

            $shippingAddress->setCollectShippingRates(false)
                ->collectShippingRates()
                ->setShippingMethod('flatrate_flatrate'); //shipping method
            $cart->getShippingAddress()->addShippingRate($this->_shippingRate);

            $cart->setPaymentMethod('checkmo'); //payment method

            $cart->setInventoryProcessed(false);

            // Set sales order payment
            $cart->getPayment()->importData(['method' => 'checkmo']);

            // Collect total and saeve
            $cart->collectTotals();

            // Submit the quote and create the order
            $cart->save();
            $cart = $this->_cartRepositoryInterface->get($cart->getId());
            try{
                $order_id = $this->cartManagementInterface->placeOrder($cart->getId());
                if(isset($order_id) && !empty($order_id)) {
                    $order = $this->orderRepository->get($order_id);
                    $response['success'] = TRUE;
                    $response['success_data']['increment_id'] = $order->getIncrementId();
                }
            } catch (\Exception $e) {
                $response['error_msg'] = $e->getMessage();
            }
        }
        return $response;
    }
    public function getCheckoutSession(){
        $checkoutSession = $this->_objectManager->get('Magento\Checkout\Model\Session');//checkout session
        return $checkoutSession;
    }

    public function getItemModel(){
        $itemModel = $this->_objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item model to load quote item
        return $itemModel;
    }
}