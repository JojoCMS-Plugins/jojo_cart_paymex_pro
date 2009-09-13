<?php
    require_once('Validation.php');    
    class Address
    {
        public $Address1;
        public $Address2;
        public $Suburb;
        public $City;
        public $PostCode;
        public $Country;
        
        public $Errors = '';
        
        function Validate()
        {
            $this->Errors = '';
            $passValidation = true;
            $passValidation &= ValidateField($this->Address1, 'Address 1', $this->Errors, true, null);
            $passValidation &= ValidateField($this->Suburb, 'Suburb', $this->Errors, true, null);
            $passValidation &= ValidateField($this->City, 'City', $this->Errors, true, null);
            $passValidation &= ValidateField($this->PostCode, 'Post Code', $this->Errors, true, null);
            $passValidation &= ValidateField($this->Country, 'Country', $this->Errors, true, '/^[A-Z]{2}$/');
            return $passValidation;
        }
		
		function ToString()
		{
			$str = 'Address1: '.$Address1.'\n';
			$str .= 'Address2: '.$Address2.'\n';
			$str .= 'Suburb: '.$Suburb.'\n';
			$str .= 'City: '.$City.'\n';
			$str .= 'PostCode: '.$PostCode.'\n';
			$str .= 'Country: '.$Country.'\n';
			return $str;
		}
    }
?>