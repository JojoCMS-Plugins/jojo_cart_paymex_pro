<?php        
    //A function for basic validation of fields
    function ValidateField($field, $fieldName, &$error, $isMandatory, $reg)
    {
        if ($isMandatory)
        {
            if ($field == '')
            {
                $error .= $fieldName.' is a mandatory field. ';
                return false;
            }
        }
        
        if ($reg != null)
        {
            //Check the regex
            if (!preg_match($reg, $field))
            {
                $error .= $fieldName.' contains invalid data. ';
                return false;
            }
            
        }
        return true;
    }

    //A function for basic validation of credit cards
    function ValidateCC($ccnum, $type, &$error) 
    {
        if (strlen($ccnum) == 0)
            return false;
        if (strlen($type) == 0)
            return false;
        if ($type == "Amex") 
        {
            $pattern = "/^([34|37]{2})([0-9]{13})$/";//American Express
            if (preg_match($pattern,$ccnum)) 
            {
                return true;
            } 
            else 
            {
                $error .= 'Invalid American Express number. ';
                return false;
            }
        }
        
        if ($type == "Visa") 
        {
            $pattern = "/^([4]{1})([0-9]{12,15})$/";//Visa
            if (preg_match($pattern,$ccnum)) 
            {
                return true;
            } 
            else 
            {
                $error .= 'Invalid Visa number. ';
                return false;
            }
        }

        if ($type == "Mastercard") 
        {
            $pattern = "/^([51|52|53|54|55]{2})([0-9]{14})$/";//Mastercard
            if (preg_match($pattern,$ccnum)) 
            {
                return true;
            } 
            else 
            {
                $error .= 'Invalid Mastercard number. ';
                return false;
            }
        }	
        
        $error .= 'Invalid card type. ';

    }
?>