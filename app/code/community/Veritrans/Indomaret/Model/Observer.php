<?php

require_once(Mage::getBaseDir('lib') . '/veritrans-php/Veritrans.php');
class Veritrans_Indomaret_Model_Observer extends Varien_Object{
	public function save_order($observer){
		Mage::log("oshopbase_create_order Observer Indomaret");
		$payment = $observer->getEvent()->getPayment();
		$order = $observer->getEvent()->getOrder();
		$paymentmethod = $payment['method'];
		if($paymentmethod === "indomaret"){
			Mage::log("ini Indomaret");
			Veritrans_Config::$isProduction = Mage::getStoreConfig("payment/$paymentmethod/environment") == 'production' ? true : false;
            Veritrans_Config::$serverKey = Mage::getStoreConfig("payment/$paymentmethod/server_key_v2");
            $expiryDuration = (int) Mage::getStoreConfig('payment/indomaret/expiry_duration');
			$expiryDuration = ($expiryDuration == 0 ? 168 : $expiryDuration);

            $transaction_details = array();
            $transaction_details['order_id'] = $order->getIncrementId();
            $transaction_details['gross_amount'] = $order->getGrandTotal();
            
            $order_billing_address = $order->getBillingAddress();
            $billing_address = array();
            $billing_address['first_name']   = $order_billing_address->getFirstname();
            $billing_address['last_name']    = $order_billing_address->getLastname();
            $billing_address['address']      = $order_billing_address->getStreet(1);
            $billing_address['city']         = $order_billing_address->getCity();

            if(strlen($billing_address['city'])>20){
                $split=explode('/',$billing_address['city']);
                if(count($split)==1){
                    $billing_address['city']=substr($billing_address['city'],20);
                }else{
                    $billing_address['city']=$split[1];
                }
            }
			if($order_billing_address->getPostcode())
            	$billing_address['postal_code']  = $order_billing_address->getPostcode();
			else
            	$billing_address['postal_code']  = '00000';

            $billing_address['country_code'] = $this->convert_country_code($order_billing_address->getCountry());
            $billing_address['phone']        = $this->convert_country_code($order_billing_address->getTelephone());

            $order_shipping_address = $order->getShippingAddress();
            $shipping_address = array();
            $shipping_address['first_name']   = $order_shipping_address->getFirstname();
            $shipping_address['last_name']    = $order_shipping_address->getLastname();
            $shipping_address['address']      = $order_shipping_address->getStreet(1);
            $shipping_address['city']         = $order_shipping_address->getCity();
            if(strlen($shipping_address['city'])>20){
                $split=explode('/',$shipping_address['city']);
                if(count($split)==1){
                    $shipping_address['city']=substr($shipping_address['city'],20);
                }else{
                    $shipping_address['city']=$split[1];
                }
            }
			if($order_shipping_address->getPostcode())
            	$shipping_address['postal_code']  = $order_shipping_address->getPostcode();
			else
            	$shipping_address['postal_code']  = '00000';
            $shipping_address['phone']        = $order_shipping_address->getTelephone();
            $shipping_address['country_code'] = $this->convert_country_code($order_shipping_address->getCountry());

            $customer_details = array();
            $customer_details['billing_address']  = $billing_address;
            $customer_details['shipping_address'] = $shipping_address;
            $customer_details['first_name']       = $order_billing_address->getFirstname();
            $customer_details['last_name']        = $order_billing_address->getLastname();
            $customer_details['email']            = $order_billing_address->getEmail();
            $customer_details['phone']            = $order_billing_address->getTelephone();

            $items               = $order->getAllItems();
            $shipping_amount     = $order->getShippingAmount();
            $shipping_tax_amount = $order->getShippingTaxAmount();
            $tax_amount = $order->getTaxAmount();

            $item_details = array();


            foreach ($items as $each) {
              $item = array(
                  'id'       => $each->getProductId(),
                  'price'    => $each->getPrice(),
                  'quantity' => $each->getQtyToInvoice(),
                  'name'     => substr($each->getName(),0,50)
                );
              
              if ($item['quantity'] == 0) continue;
              // error_log(print_r($each->getProductOptions(), true));
              $item_details[] = $item;
            }
            
            $num_products = count($item_details);

            unset($each);

            if ($order->getDiscountAmount() != 0) {
              $couponItem = array(
                  'id' => 'DISCOUNT',
                  'price' => $order->getDiscountAmount(),
                  'quantity' => 1,
                  'name' => 'DISCOUNT'
                );
              $item_details[] = $couponItem;
            }

            if ($shipping_amount > 0) {
              $shipping_item = array(
                  'id' => 'SHIPPING',
                  'price' => $shipping_amount,
                  'quantity' => 1,
                  'name' => 'Shipping Cost'
                );
              $item_details[] =$shipping_item;
            }
            
            if ($shipping_tax_amount > 0) {
              $shipping_tax_item = array(
                  'id' => 'SHIPPING_TAX',
                  'price' => $shipping_tax_amount,
                  'quantity' => 1,
                  'name' => 'Shipping Tax'
                );
              $item_details[] = $shipping_tax_item;
            }

            if ($tax_amount > 0) {
              $tax_item = array(
                  'id' => 'TAX',
                  'price' => $tax_amount,
                  'quantity' => 1,
                  'name' => 'Tax'
                );
              $item_details[] = $tax_item;
            }

            // convert to IDR
            $current_currency = Mage::app()->getStore()->getCurrentCurrencyCode();
            if ($current_currency != 'IDR') {
              $conversion_func = function ($non_idr_price) {
                  return $non_idr_price *
                      Mage::getStoreConfig("payment/$paymentmethod/conversion_rate");
                };
              foreach ($item_details as &$item) {
                $item['price'] =
                    call_user_func($conversion_func, intval(round($item['price'])));
              }
              unset($item);
            }
            else {
              foreach ($item_details as &$each) {
                $each['price'] = (int) $each['price'];
              }
              unset($each);
            }

            //inquiry free text
            $free_text_inquiry = array();
            $free_text_inquiry['id'] = 'Pembayaran untuk order # '.$orderIncrementId;
            $free_text_inquiry['en'] = 'Payment for order # '.$orderIncrementId;

            $free_text_payment = array();
            $free_text_payment['id'] = 'Pembayaran untuk order # '.$orderIncrementId;
            $free_text_payment['en'] = 'Payment for order # '.$orderIncrementId;

            $payloads = array();
            $payloads['transaction_details'] = $transaction_details;
            $payloads['item_details']        = $item_details;
            $payloads['customer_details']    = $customer_details;
            $payloads['payment_type']        = 'cstore';
            $payloads['custom_expiry']       =  array ( 
                                                  "expiry_duration" => $expiryDuration,
                                                  "unit" => "hour"
                                              );
            $payloads['cstore']       = array(
                                                    'store' => "indomaret",
                                                    'message' => $free_text_inquiry['id'],
                                                );
            $logpayment=$paymentmethod.'_veritrans.log';
            $storeconfig = 'payment/'.$paymentmethod.'/';
            $message = "The order has been created using Indomaret payment method. To get information detail please look at payment information.";
        
            try {
                $result = Veritrans_VtDirect::charge($payloads);
                Mage::log($result,null,$logpayment);
                if($result->status_code=='201') {
                	$no_va = $result->payment_code;
                    /* send an order email when redirecting to payment page although payment has not been completed. */
                    $order->setState(Mage::getStoreConfig($storeconfig),true, 'New order, waiting for payment.');
                    // $this->send_new_order_mail(Mage::app()->getStore()->getStoreId(), $order, $order_billing_address, $order->getPayment(), $customer_details['email'] , $order_billing_address->getName(), $order->getCustomerIsGuest(), $no_va, $result->gross_amount);
                    
                    $order->setEmailSent(true);

                    $created_date = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                    $expire_date = date('Y-m-d H:i',strtotime("+$expiryDuration hour",strtotime($created_date)));                  
                    $table = Mage::getSingleton('core/resource');
                    $writeAdapter = $table->getConnection('core_write');
                    if(!empty($result->payment_code)) {
                        $method=$paymentmethod;
                        $storeArray= array(
                        'order_id' => $order->getId(), 
                        'order_number' => $order->getIncrementId(),
                        'va_number' => $result->payment_code,
                        'expire_date' => $expire_date,
                        'created_date' => $created_date
                        );     
                        $smsdata= array(
                            'amount'=> Mage::helper('core')->formatPrice($result->gross_amount, false),
                            'order_id' => $order->getId(), 
                            'order_number' => $order->getIncrementId(),
                            'va_number' => $result->payment_code,
                            'expire_date' => $expire_date
                            ); 
                        $writeAdapter->insert( $table->getTableName('sales_order_virtualaccount'), $storeArray);
                    }
                    Mage::helper('oshop_smsnotification')->sms($customer_details['phone'],$method,$smsdata); 
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('indomaret')->__($message));
            }
            catch (Exception $e) {
	            Mage::log($e,null,'vtdirect_veritrans.log',true);      
	            error_log($e->getMessage());
				//Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
				$this->_getSession()->addException($e, $this->__('Indomaret Code Creating error: %s', $e->getMessage()));
				$this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
            }
		}
	}

	/**
   * Convert 2 digits coundry code to 3 digit country code
   *
   * @param String $country_code Country code which will be converted
   */
	public function convert_country_code( $country_code ) {
        // 3 digits country codes
        $cc_three = array(
          'AF' => 'AFG',
          'AX' => 'ALA',
          'AL' => 'ALB',
          'DZ' => 'DZA',
          'AD' => 'AND',
          'AO' => 'AGO',
          'AI' => 'AIA',
          'AQ' => 'ATA',
          'AG' => 'ATG',
          'AR' => 'ARG',
          'AM' => 'ARM',
          'AW' => 'ABW',
          'AU' => 'AUS',
          'AT' => 'AUT',
          'AZ' => 'AZE',
          'BS' => 'BHS',
          'BH' => 'BHR',
          'BD' => 'BGD',
          'BB' => 'BRB',
          'BY' => 'BLR',
          'BE' => 'BEL',
          'PW' => 'PLW',
          'BZ' => 'BLZ',
          'BJ' => 'BEN',
          'BM' => 'BMU',
          'BT' => 'BTN',
          'BO' => 'BOL',
          'BQ' => 'BES',
          'BA' => 'BIH',
          'BW' => 'BWA',
          'BV' => 'BVT',
          'BR' => 'BRA',
          'IO' => 'IOT',
          'VG' => 'VGB',
          'BN' => 'BRN',
          'BG' => 'BGR',
          'BF' => 'BFA',
          'BI' => 'BDI',
          'KH' => 'KHM',
          'CM' => 'CMR',
          'CA' => 'CAN',
          'CV' => 'CPV',
          'KY' => 'CYM',
          'CF' => 'CAF',
          'TD' => 'TCD',
          'CL' => 'CHL',
          'CN' => 'CHN',
          'CX' => 'CXR',
          'CC' => 'CCK',
          'CO' => 'COL',
          'KM' => 'COM',
          'CG' => 'COG',
          'CD' => 'COD',
          'CK' => 'COK',
          'CR' => 'CRI',
          'HR' => 'HRV',
          'CU' => 'CUB',
          'CW' => 'CUW',
          'CY' => 'CYP',
          'CZ' => 'CZE',
          'DK' => 'DNK',
          'DJ' => 'DJI',
          'DM' => 'DMA',
          'DO' => 'DOM',
          'EC' => 'ECU',
          'EG' => 'EGY',
          'SV' => 'SLV',
          'GQ' => 'GNQ',
          'ER' => 'ERI',
          'EE' => 'EST',
          'ET' => 'ETH',
          'FK' => 'FLK',
          'FO' => 'FRO',
          'FJ' => 'FJI',
          'FI' => 'FIN',
          'FR' => 'FRA',
          'GF' => 'GUF',
          'PF' => 'PYF',
          'TF' => 'ATF',
          'GA' => 'GAB',
          'GM' => 'GMB',
          'GE' => 'GEO',
          'DE' => 'DEU',
          'GH' => 'GHA',
          'GI' => 'GIB',
          'GR' => 'GRC',
          'GL' => 'GRL',
          'GD' => 'GRD',
          'GP' => 'GLP',
          'GT' => 'GTM',
          'GG' => 'GGY',
          'GN' => 'GIN',
          'GW' => 'GNB',
          'GY' => 'GUY',
          'HT' => 'HTI',
          'HM' => 'HMD',
          'HN' => 'HND',
          'HK' => 'HKG',
          'HU' => 'HUN',
          'IS' => 'ISL',
          'IN' => 'IND',
          'ID' => 'IDN',
          'IR' => 'RIN',
          'IQ' => 'IRQ',
          'IE' => 'IRL',
          'IM' => 'IMN',
          'IL' => 'ISR',
          'IT' => 'ITA',
          'CI' => 'CIV',
          'JM' => 'JAM',
          'JP' => 'JPN',
          'JE' => 'JEY',
          'JO' => 'JOR',
          'KZ' => 'KAZ',
          'KE' => 'KEN',
          'KI' => 'KIR',
          'KW' => 'KWT',
          'KG' => 'KGZ',
          'LA' => 'LAO',
          'LV' => 'LVA',
          'LB' => 'LBN',
          'LS' => 'LSO',
          'LR' => 'LBR',
          'LY' => 'LBY',
          'LI' => 'LIE',
          'LT' => 'LTU',
          'LU' => 'LUX',
          'MO' => 'MAC',
          'MK' => 'MKD',
          'MG' => 'MDG',
          'MW' => 'MWI',
          'MY' => 'MYS',
          'MV' => 'MDV',
          'ML' => 'MLI',
          'MT' => 'MLT',
          'MH' => 'MHL',
          'MQ' => 'MTQ',
          'MR' => 'MRT',
          'MU' => 'MUS',
          'YT' => 'MYT',
          'MX' => 'MEX',
          'FM' => 'FSM',
          'MD' => 'MDA',
          'MC' => 'MCO',
          'MN' => 'MNG',
          'ME' => 'MNE',
          'MS' => 'MSR',
          'MA' => 'MAR',
          'MZ' => 'MOZ',
          'MM' => 'MMR',
          'NA' => 'NAM',
          'NR' => 'NRU',
          'NP' => 'NPL',
          'NL' => 'NLD',
          'AN' => 'ANT',
          'NC' => 'NCL',
          'NZ' => 'NZL',
          'NI' => 'NIC',
          'NE' => 'NER',
          'NG' => 'NGA',
          'NU' => 'NIU',
          'NF' => 'NFK',
          'KP' => 'MNP',
          'NO' => 'NOR',
          'OM' => 'OMN',
          'PK' => 'PAK',
          'PS' => 'PSE',
          'PA' => 'PAN',
          'PG' => 'PNG',
          'PY' => 'PRY',
          'PE' => 'PER',
          'PH' => 'PHL',
          'PN' => 'PCN',
          'PL' => 'POL',
          'PT' => 'PRT',
          'QA' => 'QAT',
          'RE' => 'REU',
          'RO' => 'SHN',
          'RU' => 'RUS',
          'RW' => 'EWA',
          'BL' => 'BLM',
          'SH' => 'SHN',
          'KN' => 'KNA',
          'LC' => 'LCA',
          'MF' => 'MAF',
          'SX' => 'SXM',
          'PM' => 'SPM',
          'VC' => 'VCT',
          'SM' => 'SMR',
          'ST' => 'STP',
          'SA' => 'SAU',
          'SN' => 'SEN',
          'RS' => 'SRB',
          'SC' => 'SYC',
          'SL' => 'SLE',
          'SG' => 'SGP',
          'SK' => 'SVK',
          'SI' => 'SVN',
          'SB' => 'SLB',
          'SO' => 'SOM',
          'ZA' => 'ZAF',
          'GS' => 'SGS',
          'KR' => 'KOR',
          'SS' => 'SSD',
          'ES' => 'ESP',
          'LK' => 'LKA',
          'SD' => 'SDN',
          'SR' => 'SUR',
          'SJ' => 'SJM',
          'SZ' => 'SWZ',
          'SE' => 'SWE',
          'CH' => 'CHE',
          'SY' => 'SYR',
          'TW' => 'TWN',
          'TJ' => 'TJK',
          'TZ' => 'TZA',
          'TH' => 'THA',
          'TL' => 'TLS',
          'TG' => 'TGO',
          'TK' => 'TKL',
          'TO' => 'TON',
          'TT' => 'TTO',
          'TN' => 'TUN',
          'TR' => 'TUR',
          'TM' => 'TKM',
          'TC' => 'TCA',
          'TV' => 'TUV',
          'UG' => 'UGA',
          'UA' => 'UKR',
          'AE' => 'ARE',
          'GB' => 'GBR',
          'US' => 'USA',
          'UY' => 'URY',
          'UZ' => 'UZB',
          'VU' => 'VUT',
          'VA' => 'VAT',
          'VE' => 'VEN',
          'VN' => 'VNM',
          'WF' => 'WLF',
          'EH' => 'ESH',
          'WS' => 'WSM',
          'YE' => 'YEM',
          'ZM' => 'ZMB',
          'ZW' => 'ZWE'
        );

        // Check if country code exists
        if( isset( $cc_three[ $country_code ] ) && $cc_three[ $country_code ] != '' ) {
          $country_code = $cc_three[ $country_code ];
        }
		return $country_code;
    }
}