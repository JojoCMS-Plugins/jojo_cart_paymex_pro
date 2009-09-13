<?php
    require_once('Validation.php');
        
    class CardDetail
    {
        public $CardType;
        public $CardNumber;
        public $CardHolder;
        public $ExpiryDate;
        public $ExpiryMonth;
        public $ExpiryYear;
        public $SecureId;
        
        public $Errors = '';
        
        function Validate()
        {
            $this->Errors = '';
            $passValidation = true;
            
            $passValidation &= ValidateField($this->CardType, 'Card Type', $this->Errors, true, null);
            $passValidation &= ValidateField($this->CardNumber, 'Card Number', $this->Errors, true, null);
            $passValidation &= ValidateCC($this->CardNumber, $this->CardType, $this->Errors);
            $passValidation &= ValidateField($this->CardHolder, 'Card Holder', $this->Errors, true, null);
            $expiryOk = ValidateField($this->ExpiryMonth, 'Expiry Month', $this->Errors, true, '/^\d{2,2}$/');
            $ExpiryDate = '';
            if ($expiryOk)
            {
                $expiryOk = ValidateField($this->ExpiryYear, 'Expiry Year', $this->Errors, true, '/^\d{4,4}$/');
                if ($expiryOk)
                {
                    //check date
                    if ($this->ExpiryMonth < 1 || $this->ExpiryMonth > 12)
                        $expiryOk = false;
                    else
                    {
                        $this->ExpiryDate = $this->ExpiryMonth.substr($this->ExpiryYear,2);
                    }
                }
            }
            if (!$expiryOk)
            {
                $this->Errors .= 'Invalid expiry date. ';
                $passValidation = false;
            }
            $passValidation &= ValidateField($this->SecureId, 'Secure Id', $this->Errors, true, '/^\d{3,4}$/');
            return $passValidation;
        }
        
    }
?>