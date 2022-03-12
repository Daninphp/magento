<?php

namespace Codiac\RentalExtension\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Catalog\Model\ProductFactory;
use Codiac\Search\Model\CustomerPricing\Customer;

class Html
{

    private $storeManager;
    private $assetRepository;
    private $productFactory;
    private $_customer;

    public function __construct(StoreManagerInterface $storeManager, Repository $assetRepository, ProductFactory $productFactory, Customer $customer)
    {
        $this->assetRepository = $assetRepository;
        $this->productFactory = $productFactory;
        $this->_customer = $customer;
        $this->storeManager = $storeManager->getStore();
    }

    private function getMediaUrl()
    {
        return $this->storeManager->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    private function getTax()
    {
        return $this->_customer->getTaxRate();
    }

    public function getOrderDetails($orderDetails)
    {
        if (!$orderDetails['is_extensional']) {
            $html = '<h4 style="color: #9f191f;">Die Bestellung ist abgelaufen</h4>';
        } else {
            $html = '<div id="rental-products">';
            $html .= '<h4 style="color: #fff;">Bestellübersicht</h4>';
            $html .= '<input type="hidden" id="order-email" value="' . $orderDetails['order_email'] . '">';

            $html .= '
            <table id="table-tg-miet" class="tg" style="table-layout: fixed;width: 100%">
                <thead>
                    <tr>
                        <th class="tg-amwm" style="width: 60px;">Art</th>
                        <th class="tg-amwm">Bezeichnung</th>
                        <th class="tg-amwm">Geliefert</th>
                        <th class="tg-amwm">Adresse</th>
                        <th class="tg-amwm">Abholung</th>
                        <th class="tg-amwm">Neues Datum</th>
                        <th class="tg-amwm">Neuer Zeitraum</th>
                        <th class="tg-amwm">Preis</th>
                    </tr>
                </thead>
                <tbody id="table-response-miet">';

            foreach ($orderDetails['order_items_array'] as $i => $orderDetail) {
                $productId = $orderDetail['product_id'];
                $productAdditionalPrice =  $orderDetail['product_additional_price'];
                $basePeriod = $orderDetail['base_period'];
                $productIds[] = $orderDetail['product_id'];
                $productImage = $this->productFactory->create()->load($orderDetail['product_id'])->getImage();
                $addOpts = $orderDetail['product_options']['info_buyRequest']['additional_options'];
                $rentalTimeStampFrom = $orderDetail['product_options']['info_buyRequest']['additional_options']['rental_to'];
                $realRentalFromStamp = $orderDetail['product_options']['info_buyRequest']['additional_options']['rental_from'];
                $rentalFromHumanReadable = date('d.m.Y. H:i', $rentalTimeStampFrom);
                $rentalFromDateTimePickerReadable = date('d/m/Y', ($rentalTimeStampFrom + 86400));
                $selectedCategory = $orderDetail['product_options']['info_buyRequest']['selected_category'];


                $withoutTime = date('d.m.Y', $rentalTimeStampFrom);
                $rentalFrom = date('d.m.Y', $realRentalFromStamp);
                $fromTimeStamp = $rentalTimeStampFrom;

                if ($orderDetails['is_extensional']) {

                    $optId = 'x';
                    $dataOptIdValue = null;
                    if (isset($addOpts['options'])) {
                        $addOptsOpts = $addOpts['options'];
                        $optId = key($addOptsOpts) ?? 'x';
                        $optIdValue = reset($addOptsOpts);
                        $optIdValueArray = explode('_', $optIdValue);
                        unset($optIdValueArray[0]);
                        $dataOptIdValue = implode('_', $optIdValueArray);
                    }

                    $productPrice = number_format((float)$orderDetail['product_options']['info_buyRequest']['original_price'], 2, '.', '');

                    $html .=
                    '<tr class="rentals looping-products"
                          id="' . $orderDetail['product_id'] . '"
                          data-source="id-' . $orderDetail['product_id'] . '"
                          data-price="' . $productPrice . '"
                          data-option-id="' . $optId . '"
                          data-option-id-value="' . $dataOptIdValue . '"
                          data-stock-qty="' . $orderDetail['stock_qty'] . '"
                          data-order-qty="' . $orderDetail['qty_ordered'] . '"
                          data-order-timestamp-from="' . $fromTimeStamp . '"
                          data-product-add-price="' . $productAdditionalPrice . '"
                          data-product-base-period="' . $basePeriod . '"
                          data-category-id="' . $orderDetail['product_options']['info_buyRequest']['category_id']  . '"
                          data-selected-category="' . $orderDetail['product_options']['info_buyRequest']['selected_category']  . '"
                          data-privater="' . $orderDetail['product_options']['info_buyRequest']['privater']  . '"
                          data-anwesend="' . $orderDetail['product_options']['info_buyRequest']['anwesend']  . '"
                          data-clientaddress="' . $orderDetail['product_options']['info_buyRequest']['clientaddress']  . '"
                          data-clientaddressoptional="' . $orderDetail['product_options']['info_buyRequest']['clientaddressoptional']  . '"
                          data-clientmessage="' . $orderDetail['product_options']['info_buyRequest']['clientmessage']  . '"
                    >';
                    $html .='
                        <td><img alt="product-image" src=" ' . $this->getMediaUrl() . 'catalog/product' . $productImage . ' " /></td>
                        <td>' . $orderDetail['name'] . '<br> für ' . $selectedCategory . '</td>
                        <td>' . $rentalFrom . '</td>
                        <td>' . $orderDetail['product_options']['info_buyRequest']['clientaddress'] . '</td>
                        <td>' . $withoutTime . '</td>
                        <td class="rent-date"><input id="rentto-' . $productId . '" class="rentto-date" data-date="' . $rentalFromDateTimePickerReadable . '" 
                                type="text" 
                                placeholder="-" name="rentto" autocomplete="off" readonly>
                       </td>
                        <td class="rent-time">                            
                            <select name="returntime" id="delivery-time-back-' . $productId . '" class="admin__control-select returntime-miet" title="" aria-required="true">
                            </select>
                        </td>
                        <td><span id="base-price-' . $productId . '">0,00</span> €</b></td>
                    </tr>
                    ';
                    $html .= '<script>
                        require([
                            "jquery",
                        ], function(jQuery){
                            var productId = "' . $productId . '";
    
                            var minDate = "' . $rentalFromDateTimePickerReadable . '";
                            var taxRate = "' . $this->getTax() . '";
                            var additionalPrice = "' . $productAdditionalPrice . '";
                            var basePeriod = "' . $basePeriod . '";
                            var endDayExtendedOrderTimestamp = "' . $fromTimeStamp . '";
                            let price = parseFloat("' . $productPrice . '");
    
                            jQuery("#rentto-" + productId).datepicker({
                                inline:true,
                                dateFormat: "dd/mm/yy",
                                language: "de",
                                minDate: minDate,
                                onSelect: function(dateText) {
                                    jQuery(this).change();
                                }
                            }).on("change", function (){
                                var originalValue = this.value;
                                this.value =  this.value.replace(/[/]+/g, ".");
                                var real = new Date(originalValue.replace( /(\d{2})[/](\d{2})[/](\d{4})/, "$2/$1/$3"));
                                
                                var endDay = real.getDay();
    
                                var chosenTimeUnix = real.getTime() / 1000;
                                var differenceDays = Math.round((chosenTimeUnix - endDayExtendedOrderTimestamp) / 86400);
    
                                var newPrice = ((Math.round(differenceDays) + 1) * additionalPrice * (parseInt(taxRate) + 100) / 100).toFixed(2);
                              
                                jQuery("#base-price-" + productId).html(newPrice.replace(".", ","));
    
                                jQuery(".loaderGif").show();
                                jQuery("#delivery-time-back-" + productId).hide();
    
                                var baseUrl = "' . $this->storeManager->getBaseUrl() . '";
                                var url = baseUrl + "search/shipping/time";
                                var url = url.replace("#", "");
                                jQuery.ajax({
                                    url: url,
                                    method: "post",
                                    data: { data: {productId: productId, startDay: 0, endDay: endDay}},
                                    dataType: "json",
                                }).done(function (answer) {
                                    if (answer[0]) {
                                        jQuery("#delivery-time-back-" + productId).empty();
                                        jQuery("#delivery-time-back-" + productId).append("<option disabled selected>Zeitraum</option>");
                                        answer[1].forEach(function (single) {
                                            jQuery("#delivery-time-back-" + productId).append("<option value=\'" + single.shipping_hours + "\'>" + single.shipping_hours + "</option>");
                                        });
                                    }
                                    jQuery(".loaderGif").hide();
                                    jQuery("#delivery-time-back-" + productId).show();
                                });
                            });
                        });
                    </script>';
                } else {
                    $html .= '<tr class="rentals looping-products">';
                }
            }
            $html .='
                </tbody>
            </table>';

$html .= '</div>';
        }


        return $html;
    }

}