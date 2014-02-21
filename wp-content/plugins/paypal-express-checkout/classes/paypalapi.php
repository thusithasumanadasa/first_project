<?php
session_start();

/**
 * PayPal API
 */
if ( ! class_exists('HCCoder_PayPalAPI') ) {

  class HCCoder_PayPalAPI {
  
    /**
     * Start express checkout
     */
    function StartExpressCheckout() {
      
      $config = HCCoder_PayPalConfig::getInstance();
      
      if ( get_option('paypal_environment') != 'sandbox' && get_option('paypal_environment') != 'live' )
        trigger_error('Environment does not defined! Please define it at the plugin configuration page!', E_USER_ERROR);
      
      if ( get_option('paypal_cancel_page') === FALSE || ! is_numeric(get_option('paypal_cancel_page')) )
        trigger_error('Cancel page not defined! Please define it at the plugin configuration page!', E_USER_ERROR);
      
      if ( get_option('paypal_success_page') === FALSE || ! is_numeric(get_option('paypal_success_page')) )
        trigger_error('Success page not defined! Please define it at the plugin configuration page!', E_USER_ERROR);
      
      // FIELDS
      $fields = array(
              'USER' => urlencode(get_option('paypal_api_username')),
              'PWD' => urlencode(get_option('paypal_api_password')),
              'SIGNATURE' => urlencode(get_option('paypal_api_signature')),
              'VERSION' => urlencode('72.0'),
              'PAYMENTREQUEST_0_PAYMENTACTION' => urlencode('Sale'),
              'PAYMENTREQUEST_0_AMT0' => urlencode($_POST['AMT']),
              'PAYMENTREQUEST_0_AMT' => urlencode($_POST['AMT']),
              'PAYMENTREQUEST_0_ITEMAMT' => urlencode($_POST['AMT']),
              'ITEMAMT' => urlencode($_POST['AMT']),
              'PAYMENTREQUEST_0_CURRENCYCODE' => urlencode($_POST['CURRENCYCODE']),
              'RETURNURL' => urlencode($config->getItem('plugin_form_handler_url').'?func=confirm'),
              'CANCELURL' => urlencode(get_permalink(get_option('paypal_cancel_page'))),
              'METHOD' => urlencode('SetExpressCheckout')
          );
      
      if ( isset($_POST['PAYMENTREQUEST_0_DESC']) )
        $fields['PAYMENTREQUEST_0_DESC'] = $_POST['PAYMENTREQUEST_0_DESC'];
      
      if ( isset($_POST['RETURN_URL']) )
        $_SESSION['RETURN_URL'] = $_POST['RETURN_URL'];
      
      if ( isset($_POST['CANCEL_URL']) )
        $fields['CANCELURL'] = $_POST['CANCEL_URL'];
      
      if ( isset($_POST['PAYMENTREQUEST_0_QTY']) ) {
        $fields['PAYMENTREQUEST_0_QTY0'] = $_POST['PAYMENTREQUEST_0_QTY'];
        $fields['PAYMENTREQUEST_0_AMT'] = $fields['PAYMENTREQUEST_0_AMT'] * $_POST['PAYMENTREQUEST_0_QTY'];
        $fields['PAYMENTREQUEST_0_ITEMAMT'] = $fields['PAYMENTREQUEST_0_ITEMAMT'] * $_POST['PAYMENTREQUEST_0_QTY'];
        $fields['ITEMAMT'] = $fields['ITEMAMT'] * $_POST['PAYMENTREQUEST_0_QTY'];
        
      }
      
      
      if ( isset($_POST['TAXAMT']) ) {
        $fields['PAYMENTREQUEST_0_TAXAMT'] = $_POST['TAXAMT'];
        $fields['PAYMENTREQUEST_0_AMT'] += $_POST['TAXAMT'];
      }
      
            
      if ( isset($_POST['HANDLINGAMT']) ) {
        $fields['PAYMENTREQUEST_0_HANDLINGAMT'] = $_POST['HANDLINGAMT'];
        $fields['PAYMENTREQUEST_0_AMT'] += $_POST['HANDLINGAMT'];
      }
            
      if ( isset($_POST['SHIPPINGAMT']) ) {
        $fields['PAYMENTREQUEST_0_SHIPPINGAMT'] = $_POST['SHIPPINGAMT'];
        $fields['PAYMENTREQUEST_0_AMT'] += $_POST['SHIPPINGAMT'];
      }
      
      $fields_string = '';

      foreach ( $fields as $key => $value ) 
        $fields_string .= $key.'='.$value.'&';
        
      rtrim($fields_string,'&');
      
      // CURL
      $ch = curl_init();
      
      if ( get_option('paypal_environment') == 'sandbox' )
        curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
      elseif ( get_option('paypal_environment') == 'live' )
        curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');
        
      curl_setopt($ch, CURLOPT_POST, count($fields));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
      //execute post
      $result = curl_exec($ch);
      
      //close connection
      curl_close($ch);
      
      parse_str($result, $result);
      
      if ( $result['ACK'] == 'Success' ) {
        
        if ( get_option('paypal_environment') == 'sandbox' )
          header('Location: https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token='.$result['TOKEN']);
        elseif ( get_option('paypal_environment') == 'live' )
          header('Location: https://www.paypal.com/webscr?cmd=_express-checkout&token='.$result['TOKEN']);
        exit;
        
      } else {
        print_r($result);
      }
      
    }
    
    /**
     * Validate payment
     */
    function ConfirmExpressCheckout() {
    
      $config = HCCoder_PayPalConfig::getInstance();
      
      // FIELDS
      $fields = array(
              'USER' => urlencode(get_option('paypal_api_username')),
              'PWD' => urlencode(get_option('paypal_api_password')),
              'SIGNATURE' => urlencode(get_option('paypal_api_signature')),
              'VERSION' => urlencode('72.0'),
              'TOKEN' => urlencode($_GET['token']),
              'METHOD' => urlencode('GetExpressCheckoutDetails')
          );
      
      $fields_string = '';
      foreach ( $fields as $key => $value )
        $fields_string .= $key.'='.$value.'&';
      rtrim($fields_string,'&');
      
      // CURL
      $ch = curl_init();
      
      if ( get_option('paypal_environment') == 'sandbox' )
        curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
      elseif ( get_option('paypal_environment') == 'live' )
        curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');
        
      curl_setopt($ch, CURLOPT_POST, count($fields));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
      //execute post
      $result = curl_exec($ch);
      //close connection
      curl_close($ch);
      
      parse_str($result, $result);
      
      if ( $result['ACK'] == 'Success' ) {
        HCCoder_PayPalAPI::SavePayment($result, 'pending');
        HCCoder_PayPalAPI::DoExpressCheckout($result);
      } else {
        HCCoder_PayPalAPI::SavePayment($result, 'failed');
      }
    }
    
    /**
     * Close transaction
     */
    function DoExpressCheckout($result) {
    
      $config = HCCoder_PayPalConfig::getInstance();
    
      // FIELDS
      $fields = array(
              'USER' => urlencode(get_option('paypal_api_username')),
              'PWD' => urlencode(get_option('paypal_api_password')),
              'SIGNATURE' => urlencode(get_option('paypal_api_signature')),
              'VERSION' => urlencode('72.0'),
              'PAYMENTREQUEST_0_PAYMENTACTION' => urlencode('Sale'),
              'PAYERID' => urlencode($result['PAYERID']),
              'TOKEN' => urlencode($result['TOKEN']),
              'PAYMENTREQUEST_0_AMT' => urlencode($result['AMT']),
              'METHOD' => urlencode('DoExpressCheckoutPayment')
          );
      
      $fields_string = '';
      foreach ( $fields as $key => $value)
        $fields_string .= $key.'='.$value.'&';
      rtrim($fields_string,'&');
      
      // CURL
      $ch = curl_init();
      
      if ( get_option('paypal_environment') == 'sandbox' )
        curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
      elseif ( get_option('paypal_environment') == 'live' )
        curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');
      
      curl_setopt($ch, CURLOPT_POST, count($fields));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
      //execute post
      $result = curl_exec($ch);
      //close connection
      curl_close($ch);
      
      parse_str($result, $result);
      
      if ( $result['ACK'] == 'Success' ) {
        HCCoder_PayPalAPI::UpdatePayment($result, 'success');
      } else {
        HCCoder_PayPalAPI::UpdatePayment($result, 'failed');
      }
    }
    
    /**
     * Save payment result into database
     */
    function SavePayment($result, $status) {
      global $wpdb;
      
      $insert_data = array('token' => $result['TOKEN'],
                           'amount' => $result['AMT'],
                           'currency' => $result['CURRENCYCODE'],
                           'status' => 'pending',
                           'firstname' => $result['FIRSTNAME'],
                           'lastname' => $result['LASTNAME'],
                           'email' => $result['EMAIL'],
                           'description' => $result['PAYMENTREQUEST_0_DESC'],
                           'summary' => serialize($result),
                           'created' => time());
      
      $insert_format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d');
      
      $wpdb->insert('hccoder_paypal', $insert_data, $insert_format);
    }
    
    /**
     * Update payment
     */
    function UpdatePayment($result, $status) {
      global $wpdb;
      
      $update_data = array('transaction_id' => $result['PAYMENTINFO_0_TRANSACTIONID'],
                           'status' => $status);
      
      $where = array('token' => $result['TOKEN']);
      
      $update_format = array('%s', '%s');
      
      $wpdb->update('hccoder_paypal', $update_data, $where, $update_format);
    }
    
  }
  
}