<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Harvey Kane <code@ragepank.com>
 * Copyright 2008 Michael Holt <code@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

define('_PAYMEX_PRO_CURRENCY', 'NZD'); //Currently hard-coded to NZD

class JOJO_Plugin_jojo_cart_paymex_pro extends JOJO_Plugin
{
   function getPaymentOptions()
    {
        /* ensure the order currency is the same as Paymex currency */
        $currency = call_user_func(array(Jojo_Cart_Class, 'getCartCurrency'));
        if ($currency != _PAYMEX_PRO_CURRENCY) return array();

        global $smarty;
        $options = array();

        /* get available card types (specified in options) */
        $cardtypes = explode(',', Jojo::getOption('paymex_pro_card_types', 'visa,mastercard,amex'));

        /* uppercase first letter of each card type */
        foreach ($cardtypes as $k => $v) {
            $cardtypes[$k] = trim(ucwords($v));
        }
        $smarty->assign('cardtypes', $cardtypes);
        $options[] = array('id' => 'paymex_pro', 'label' => 'Credit card ('.implode(', ', $cardtypes).')', 'html' => $smarty->fetch('jojo_cart_paymex_pro_checkout.tpl'));
        return $options;
    }

   /*
    * Determines whether this payment plugin is active for the current payment.
    */
    function isActive()
    {
        /* Look for a post variable specifying DPS */
        return (Jojo::getFormData('handler', false) == 'paymex_pro') ? true : false;
    }

   /*
    * Process the credit card based on POST data. Return array(success, receipt, errors)
    */
   function process()
   {
      global $smarty;
      $cart =& $_SESSION['jojo_cart'];

      $testmode = call_user_func(array(Jojo_Cart_Class, 'isTestMode'));

      $errors  = array();

      /* ensure the order currency is the same as PaymexPro currency */
      $currency = call_user_func(array(Jojo_Cart_Class, 'getCartCurrency'));
      if ($currency != _PAYMEX_PRO_CURRENCY) {
          return array(
                      'success' => false,
                      'receipt' => array(),
                      'errors'  => array('Paymex is only able to process transactions in '._PAYMEX_PRO_CURRENCY.'.')
                      );
      }

      foreach (Jojo::listPlugins('external/paymex_pro/Paymex.php') as $pluginfile) {
          require_once($pluginfile);
          break;
      }

      $px = new PaymexProcessor();
      $px->TestMode = $testmode;
      $px->ErrorUrl = _SECUREURL.'/cart/error/';

      /* merchant ID and password are stored as Jojo options */
      if ($testmode) {
          $px->BusinessId  = '0B297558-090B-4A66-9A86-1B6CF1D87B23';
          $px->Password    = 'Q6Vl2VdVKe';
      } else {
          $px->BusinessId  = trim(Jojo::getOption('paymex_pro_business_id', ''));
          $px->Password    = trim(Jojo::getOption('paymex_pro_password', ''));
      }

      /* Build up the customer details */
      $customer = new CustomerDetail();
      $customer->FirstName      = $cart['fields']['FirstName'];
      $customer->LastName       = $cart['fields']['LastName'];
      //$customer->PhoneArea    = '';
      $customer->PhoneNumber    = $cart['fields']['Phone'];
      $customer->Email          = $cart['fields']['Email'];
      /* Build up the billing address */
      $billing = new Address();
      $billing->Address1        = $cart['fields']['Address1'];
      $billing->Address2        = $cart['fields']['Address2'];
      $billing->Suburb          = $cart['fields']['Suburb'];
      $billing->PostCode        = $cart['fields']['PostCode'];
      $billing->City            = $cart['fields']['City'];
      $billing->Country         = $cart['fields']['Country'];
      $customer->BillingAddress = $billing;

      /* Build up the card details */
      $card = new CardDetail();
      $card->CardType           = Jojo::getFormData('cardType',        '');
      $card->CardNumber         = str_replace(array(' ', '-'), '', Jojo::getFormData('cardNumber',      ''));
      $card->CardHolder         = Jojo::getFormData('cardName',        '');
      $card->ExpiryMonth        = Jojo::getFormData('cardExpiryMonth', '');
      $card->ExpiryYear         = Jojo::getFormData('cardExpiryYear',  '');
      $card->SecureId           = Jojo::getFormData('secureId',        '');

      /* Try and process the payment */
      try
      {
          $response = $px->ProcessPayment(number_format($cart['order']['amount'], 2, '.', ''), _PAYMEX_PRO_CURRENCY, $cart['token'], $cart['token'], $card, $customer);

          if ($response == 0) {
              /* success */
              $receipt = array(
                            'Merchant name'                  => Jojo::getOption('cart_merchant_name', _SITETITLE),
                            'Merchant address'               => Jojo::getOption('cart_merchant_address', 'unspecified'),
                            'Transaction ID'                 => $cart['token'],
                            'Purchase amount'                => _PAYMEX_PRO_CURRENCY.number_format($cart['order']['amount'], 2, '.', ''),
                            'Transaction date'               => date('d M Y, h:i'),
                            'Result'                         => 'ACCEPTED',
              );

              return array(
                 'success' => true,
                 'receipt' => $receipt,
                 'errors'  => array(),
                 'message' => 'Thank you for your payment via Credit Card.'
              );
          } else {
              /* not success */
              if ($response < -100)
                  return array(
                 'success' => false,
                 'receipt' => array(),
                 'errors'  => array('Failed to process: response code '.$response)
                 );
              $error = $px->GetLastError();
              /*
              foreach ($customer->BuildArray() as $key => $value)
              {
                  echo $key.' = '.$value.'<br />';
              }*/
          }
      } catch (Exception $e) {
          $error = $e->getMessage();
      }

      return array(
                 'success' => false,
                 'receipt' => array(),
                 'errors'  => array('Failed to process: '.$error)
                 );
   }
}