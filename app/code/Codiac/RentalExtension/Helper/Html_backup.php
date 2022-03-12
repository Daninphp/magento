<?php

namespace Codiac\RentalExtension\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Catalog\Model\ProductFactory;

class Html
{

    private $storeManager;
    private $assetRepository;
    private $productFactory;

    public function __construct(StoreManagerInterface $storeManager, Repository $assetRepository, ProductFactory $productFactory)
    {
        $this->assetRepository = $assetRepository;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager->getStore();
    }

    private function getMediaUrl()
    {
        return $this->storeManager->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getOrderDetails($orderDetails)
    {
        if (!$orderDetails['is_extensional']) {
            $html = '<h3 style="color: #9f191f;">Die Bestellung ist abgelaufen</h3>';
        } else {
            $html = '<div id="rental-products" class="row">';
            $html .= '<input type="hidden" id="order-email" value="' . $orderDetails['order_email'] . '">';

            foreach ($orderDetails['order_items_array'] as $i => $orderDetail) {
                //echo'<pre>';print_r($orderDetail);die();
                $productId = $orderDetail['product_id'];
                $productAdditionalPrice =  $orderDetail['product_additional_price'];
                $basePeriod = $orderDetail['base_period'];
                $productIds[] = $orderDetail['product_id'];
                $productImage = $this->productFactory->create()->load($orderDetail['product_id'])->getImage();
                $addOpts = $orderDetail['product_options']['info_buyRequest']['additional_options'];
                $rentalTimeStampFrom = $orderDetail['product_options']['info_buyRequest']['additional_options']['rental_to'];
                $rentalFromHumanReadable = date('d.m.Y. H:i', $rentalTimeStampFrom);
                $rentalFromDateTimePickerReadable = date('d/m/Y', ($rentalTimeStampFrom + 86400));

                $withoutTime = date('d.m.Y.', $rentalTimeStampFrom );
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
                    //var_dump($optId); exit();
                    $productPrice = number_format((float)$orderDetail['product_options']['info_buyRequest']['original_price'], 2, '.', '');
                    $labelVon = 'Von';

                    $html .=
                        '<div class="col-sm-6 rentals looping-products" 
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
                } else {
                    $labelVon = 'Lief aus';
                    $html .= '<div class="col-sm-6 rentals looping-products">';
                }


                $html .=
                    '<h3 style="color: #fff">' . $orderDetail['name'] . '</h3>
                        <div class="col-sm-3 rental-image">
                            <img src=" ' . $this->getMediaUrl() . 'catalog/product' . $productImage . ' " />
                        </div>
                        <div class="rental-qty">
                            <div class="rental-time block-content">
                                <label style="display: inline-block; width: 100%;color: #fff;">
                                    Quantität: <p style="font-size: 18px !important; padding-left: 10px;"><b>' . (int)$orderDetail['qty_ordered'] . '</b></p>
                                </label>
                            </div>
                        </div>
                        <div class="rental-price">
                            <div class="rental-time block-content">
                                <label style="display: inline-block; width: 100%;color: #fff;">
                                    Preis: <p style="font-size: 18px !important; padding-left: 10px;"><b><span id="base-price-' . $productId . '">' . $productPrice . '</span> €</b></p>
                                </label>
                            </div>
                        </div>';
                $html .= '
                    <div id="order-start-date">
                        <div class="rental-time block-content">
                            <label style="display: inline-block; width: 100%;color: #fff;">
                                ' . $labelVon . ': <p style="font-size: 18px !important;"><b>' . $withoutTime . '</b></p>
                            </label>';
                if ($orderDetails['is_extensional']) {
                    $html .= '   
                            <label style="display: inline-block; width: 100%;color: #fff;">
                                Bis:<input id="rentto-' . $productId . '" class="rentto-date" data-date="' . $rentalFromDateTimePickerReadable . '" type="text" placeholder="Datum auswählen" style="color: #fff;border: 1px solid;background: url(' . $this->assetRepository->getUrlWithParams('Magenest_RentalSystem::images/calendar.png', []) . ') no-repeat scroll 4px 4px;
                                               background-size: 22px 22px;
                                               padding-left:40px;" name="rentto" autocomplete="off" readonly>
                            </label>';
                }
                $html .= '</div>
                    </div>';

                if ($orderDetails['is_extensional']) {
                    $html .= '<div class="field required delivery-times">
                        <label class="label" style="color: #fff;" for="delivery-time-back-' . $productId . '">
                            <span style="color: #fff;">Lieferzeit zurück</span>
                        </label>
                        <div class="control">
                            <select name="returntime" id="delivery-time-back-' . $productId . '" class="admin__control-select" title="" aria-required="true">
                                <option value="">-- Bitte Datum auswählen --</option>
                            </select>
                        </div>
                        <div class="loaderGif"></div>
                    </div>';

                    $html .= '<script>
                       require([
                                    "jquery",
                                ], function(jQuery){
                                    var productId = "' . $productId . '";
                                    
                                    var minDate = "' . $rentalFromDateTimePickerReadable . '";
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
                                            var real = new Date(this.value.replace( /(\d{2})[/](\d{2})[/](\d{4})/, "$2/$1/$3"));
                                            var endDay = real.getDay();
                                            
                                            var chosenTimeUnix = real.getTime() / 1000;
                                            var differenceDays = Math.round((chosenTimeUnix - endDayExtendedOrderTimestamp) / 86400);
                                            
                                            if (differenceDays > basePeriod) {
                                                var newPrice = price + (Math.round(differenceDays - basePeriod)) * additionalPrice;
                                                jQuery("#base-price-" + productId).html(newPrice.toFixed(2));      
                                            }
                                                                                   
                                            
                                            jQuery(".loaderGif").show();
                                            jQuery("#delivery-time-back-" + productId).hide();
                            
                                            var baseUrl = "' . $this->storeManager->getBaseUrl() . '";
                                            var url = baseUrl + "search/shipping/time";
                                            var url = url.replace("#", "");
                                            var data = [productId, 0, endDay];
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
                }

                $html .= '</div>';
            }
            $html .= '</div>';
        }


        return $html;
    }

}