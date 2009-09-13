<?php    
    require_once('Address.php');
    require_once('Validation.php');
    class CustomerDetail
    {
        public $FirstName = '';
        public $LastName = '';
        
        public $Phone;
        public $PhoneArea;
        public $PhoneNumber;
        
        public $Email;
    
        public $BillingAddress = null;
        public $ShippingAddress = null;
        
        public $Errors;
        
        function Validate()
        {
            //Clean data first
            $this->FirstName = ucfirst($this->FirstName);
            $this->LastName = ucfirst($this->LastName);
            
            $this->Phone = '('.str_replace(')','',str_replace('(','',$this->PhoneArea)).')'.str_replace('-','',str_replace(' ','',$this->PhoneNumber));
 
            //Do validations
            $passValidation = true;
            $passValidation &= ValidateField($this->FirstName, 'First Name', $this->Errors, true, '/^[A-Z][a-z\. ]*$/');
            $passValidation &= ValidateField($this->LastName, 'Last Name', $this->Errors, true, '/^[A-Z][A-Za-z\. \-]*$/');
            
            $passValidation &= ValidateField($this->Email, 'Email', $this->Errors, true, '/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i');
            $passValidation &= ValidateField($this->Phone, 'Phone Number', $this->Errors, true, '/^\(\d+\)\d{5,10}$/');
            
            //Validate the Billing Address
            if ($this->BillingAddress == null)
            {
                $this->Errors .= 'Billing details are mandatory. ';
            }
            else
            {
                $billingPass = $this->BillingAddress->Validate();
                if (!$billingPass)
                {
                    $passValidation = false;
                    $this->Errors .= $this->BillingAddress->Errors;
                }
            }
            
            //Shipping address
            if ($this->ShippingAddress != null)
            {
                $shippingPass = $this->ShippingAddress->Validate();
                if (!$shippingPass)
                {
                    $passValidation = false;
                    $this->Errors .= $this->ShippingAddress->Errors;
                }
            }
            
            //Return whether it passed
            return $passValidation;
        }
        
        function BuildArray()
        {
            if ($this->ShippingAddress != null)
            {
                return array(
                    'FirstName' => $this->FirstName,
                    'LastName' => $this->LastName,
                    'Address1' => $this->BillingAddress->Address1,
                    'Address2' => $this->BillingAddress->Address2,
                    'Suburb' => $this->BillingAddress->Suburb,
                    'City' => $this->BillingAddress->City,
                    'PostCode' => $this->BillingAddress->PostCode,
                    'Country' => $this->BillingAddress->Country,
                    'Email' => $this->Email,
                    'Phone' => $this->Phone,
                    'ShipAddress1' => $this->ShippingAddress->Address1,
                    'ShipAddress2' => $this->ShippingAddress->Address2,
                    'ShipSuburb' => $this->ShippingAddress->Suburb,
                    'ShipCity' => $this->ShippingAddress->City,
                    'ShipPostCode' => $this->ShippingAddress->PostCode,
                    'ShipCountry' => $this->ShippingAddress->Country
                    );
            }
            else
            {
                return array(
                    'FirstName' => $this->FirstName,
                    'LastName' => $this->LastName,
                    'Address1' => $this->BillingAddress->Address1,
                    'Address2' => $this->BillingAddress->Address2,
                    'Suburb' => $this->BillingAddress->Suburb,
                    'City' => $this->BillingAddress->City,
                    'PostCode' => $this->BillingAddress->PostCode,
                    'Country' => $this->BillingAddress->Country,
                    'Email' => $this->Email,
                    'Phone' => $this->Phone,
                    'ShipAddress1' => '',
                    'ShipAddress2' => '',
                    'ShipSuburb' => '',
                    'ShipCity' => '',
                    'ShipPostCode' => '',
                    'ShipCountry' => ''
                    );
            }
        }
    }
?>