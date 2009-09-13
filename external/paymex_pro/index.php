<?php
    //Try and process
    require_once('Paymex.php');

    $px = new PaymexProcessor();
    //Change these
    $px->TestMode = true;
    $px->ErrorUrl = 'http://www.google.co.nz/';
    $px->BusinessId = '0B297558-090B-4A66-9A86-1B6CF1D87B23';
    $px->Password = 'Q6Vl2VdVKe';

    //Build up the customer details
    $customer = new CustomerDetail();
    $customer->FirstName = 'Joe';
    $customer->LastName = 'Bloggs';
    //Do you have these available?
    $customer->PhoneArea = '0800';
    $customer->PhoneNumber = '729639';
    $customer->Email = 'dev@paymex.co.nz';
    //Build up the billing address
    $billing = new Address();
    $billing->Address1 = 'PO Box 6101';
    //$billing->Address2 = '';
    $billing->Suburb = 'Tauranga';
    $billing->PostCode = '3146';
    $billing->City = 'Tauranga';
    $billing->Country = 'NZ';
    $customer->BillingAddress = $billing;

    //Build up the card details
    $card = new CardDetail();
    $card->CardType = 'Visa';
    $card->CardNumber = '4111111111111111';
    $card->CardHolder = 'TESTER';
    $card->ExpiryMonth = '07';
    $card->ExpiryYear = '2012';
    $card->SecureId = '123';

    //Try and process the payment
	try
	{
		$response = $px->ProcessPayment(100.00, 'NZD', 'Reference', 'Reference', $card, $customer);
		if ($response == 0)
	    {
	        //Do the successful thing
	        echo 'Processed successfully!';
			return;
	    }
	    else
	    {
	        if ($response < -100)
	            return;
	        $error = $px->GetLastError();
			/*
			foreach ($customer->BuildArray() as $key => $value) 
			{
		        echo $key.' = '.$value.'<br />';
		    }*/
	    }
	}
	catch (Exception $e)
	{
		$error = $e->getMessage();
	}
	echo 'Failed to process: '. $error;
?>