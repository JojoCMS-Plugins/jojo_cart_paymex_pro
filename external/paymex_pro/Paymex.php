<?php
    require_once('CustomerDetail.php');
    require_once('CardDetail.php');
    
    class PaymexProcessor
    {
        //DEFINE SOME CONSTANTS
        //The path to the WSDL for the webservice
        //private $pxwsdl = 'https://secure.paymex.co.nz/payment/paymex.asmx?WSDL';
        private $pxwsdl = 'paymex.wsdl';
        //The path to the webservice
        private $pxws = 'https://secure.paymex.co.nz/payment/paymex.asmx';
        //The namespace of the webservice
        private $pxns = 'https://secure.paymex.co.nz/payment/';

        //CLIENT VARIABLES
        //Test mode
        public $TestMode = false;
        //This url is used for errors
        public $ErrorUrl = 'http://localhost/error/';
        //Your business id
        public $BusinessId = '0B297558-090B-4A66-9A86-1B6CF1D87B23';
        //Your unique professional account password
        public $Password = 'Q6Vl2VdVKe';
        
        //PRIVATE VARIABLES
        private $lastError = '';
        function PaymexProcessor()
        {
            $this->pxwsdl = _PLUGINDIR.'/jojo_cart_paymex_pro/external/paymex_pro/paymex.wsdl';
        }
        
        //METHODS
        function GetLastError()
        {
            return $this->lastError;
        }
        
        function ProcessPayment($amount, $currency, $clientRef, $retailRef, $cardDetails, $customerDetails)
        {
            //Make sure we are using HTTPS
            $is_https = (!empty($_SERVER['HTTPS'])); 
            if (!$this->TestMode && !$is_https)
            {
            	header( 'Location: '.$this->ErrorUrl) ;
            	return -101;
            }

            //Work out the total
            $total = $amount;

            //Do some validation on some fields
            $passValidation = true;
            $this->lastError = '';
            if ($amount < 0)
            {
                $this->lastError = 'Invalid amount specified. ';
                $passValidation = false;
            }
            $passValidation .= $cardDetails->Validate();
            if (!$passValidation)
                $lastError .= $cardDetails->Errors;
            $passValidation .= $customerDetails->Validate();
            if (!$passValidation)
                $lastError .= $customerDetails->Errors;

            //If we passed validation then do the payment
            if (!$passValidation)
                return -1;

            //Use our webservice (the Paymex php curl library has become obsolete)
            $response = -1;
              
            //Build the options for the webservice
            $options = array(
                          'trace' => $this->TestMode,
                          'uri' => $this->pxns, 
                          'exceptions' => $this->TestMode
                          );
            //debug only
            $options = array(
                          'trace' => true,
                          'uri' => $this->pxns, 
                          'exceptions' => true
                          );
            
            //Create the webservice based upon the WSDL
            $client = new SoapClient($this->pxwsdl, $options);

            //Create the billing details object
            $billingDetails = $customerDetails->BuildArray();

            //Build the parameters to pass
            $params = array(
                        'cardHolder'=>$cardDetails->CardHolder, 
                        'cardNumber'=>$cardDetails->CardNumber, 
                        'expiryDate'=>$cardDetails->ExpiryDate, 
                        'secureId'=>$cardDetails->SecureId, 
                        'amount'=>$total, 
                        'currency'=>$currency, 
                        'clientReference'=>$clientRef, 
                        'comment'=>$retailRef, 
                        'details'=>$billingDetails, 
                        'testMode'=>$this->TestMode);
              
            //Build the authentication header
            $auth = array(
                 'ns1:BusinessId' => $this->BusinessId,
                 'ns1:Password' => $this->Password);
            $authvalues = new SoapVar($auth, SOAP_ENC_OBJECT);
            $header = new SoapHeader($this->pxns, 'AuthenticationHeader', $authvalues, false);
              
            //Set the headers
            $client->__setSoapHeaders(array($header));
              
            //Do the request and get the response
            $responsec = $client->ProcessCreditCardPayment($params);
            $response = $responsec->ProcessCreditCardPaymentResult;
            
            if (strlen($response) > 0 && $response == 0)
            {
                return 0;
            }
            else
            {
                if (strlen($response) == 0)
                    $response = -2;
                switch ($response)
                {
                    case -1:
                        $this->lastError = 'Payment failed - General error ('.$responsec->getMessage().')';			
                        break;
                    case 30:
                    case 37:
                    case 38:
                    case 60:
                    case 65:	
                    case 989:
                    case 988:
                    case 987:
                        $this->lastError = 'Payment failed - setup problem ('.$response.')';
                        break;
                    case 31:
                        $this->lastError = 'Payment failed - invalid amount ('.$response.')';
                        break;
                    case 156:
                    case 157:
                    case 158:
                    case 159:	
                    case 160:	
                    case 50:
                    case 51:
                    case 52:
                    case 53:	
                    case 54:					
                    case 55:					
                    case 57:	
                    case 71:	
                    case 72:	
                    case 73:	
                        $this->lastError = 'Payment failed - invalid details provided ('.$response.')';
                        break;
                    case 75:	
                    case 76:	
                        $this->lastError = 'Payment declined ('.$response.')';
                        break;
                    case 77:	
                        $this->lastError = 'Payment declined - Secure Id invalid ('.$response.')';
                        break;
                    case 79:
                    default:
                        $this->lastError = 'Payment failed ('.$response.')';
                        break;
                }
            }
            return $response;
        }
    }
?>