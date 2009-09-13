<form name="paymexpro_form" id="paymexpro_form" method="post" action="{$SECUREURL}/cart/process/{$token}/">
<div class="box contact-form">
    <h2>Pay by credit card</h2>
    <input type="hidden" name="token" id="token" value="{$token}" />
    <input type="hidden" name="paymentmethod" value="paymex_pro" />{* this line probably not needed *}
    <input type="hidden" name="handler" value="paymex_pro" />
    
    <label for="paymexpro_cardType">Card Type:</label>
    <select name="cardType" id="paymexpro_cardType">
      <option value="">Select card type</option>
      {section name=c loop=$cardtypes}
      <option value="{$cardtypes[c]}"{if ($fields.cardType==$cardtypes[c] && $OPTIONS.cart_test_mode!='yes') || ($OPTIONS.cart_test_mode=='yes' && $cardtypes[c]|strtolower=='visa')} selected="selected"{/if}>{$cardtypes[c]|ucfirst}</option>
      {/section}
    </select><br />
    
    <label for="paymexpro_cardNumber">Card Number:</label>
    <input type="text" size="30" maxlength="19" name="cardNumber" id="paymexpro_cardNumber" value="{if $OPTIONS.cart_test_mode=='yes'}4111 1111 1111 1111{/if}" autocomplete="off" /><br />
    
    <label for="paymexpro_cardExpiryMonth">Expiry Date:</label>
    <div class="form-field">
    <input type="text" size="2" maxlength="2" name="cardExpiryMonth" id="paymexpro_cardExpiryMonth" value="{if $OPTIONS.cart_test_mode=='yes'}07{/if}" autocomplete="off" /> / <input type="text" size="4" maxlength="4" name="cardExpiryYear" id="paymexpro_cardExpiryYear" value="{if $OPTIONS.cart_test_mode=='yes'}2012{/if}" /> (mm/yyyy)
    </div><br />
    
    <label for="paymexpro_cardName">Name on card:</label>
    <input type="text" size="30" name="cardName" id="paymexpro_cardName" value="{if $OPTIONS.cart_test_mode=='yes'}TESTER{/if}" autocomplete="off" /><br />
    
    <label for="paymexpro_secureId">Secure ID:</label>
    <input type="text" size="4" name="secureId" id="paymexpro_secureId" value="{if $OPTIONS.cart_test_mode=='yes'}123{/if}" autocomplete="off" /><br />
    
  </div>

<div style="text-align: center;"><input type="image" src="images/btn-pay-now.gif" name="pay" id="pay" value="Pay by Credit card" onclick="if (validateSecurePayTech()){ldelim}$('#paymexpro_pay').attr('disabled',true);$('#paymexpro_form').submit();{rdelim}else{ldelim}return false;{rdelim}" /></div>

</form>
<div style="text-align:center">
  <a href="http://www.paymex.co.nz" rel="nofollow" target="_BLANK"><img src="images/payment_methods2.png" alt="Paymex payment methods - Visa, Mastercard, AMEX" /></a>
</div>

<script type="text/javascript">
{literal}
function validatePaymexPro() {
  /* check card type is selected */  
  if ($('#paymexpro_cardType').val() == '') {
    alert('Please enter the card type');
    return false;
  }
  /* check card format */  
  if (!checkCreditCard($('#paymexpro_cardNumber').val(), $('#paymexpro_cardType').val())) {
    alert('Please enter a valid credit card number');
    return false;
  }
  /* check expiry date is not empty */  
  if (($('#paymexpro_cardExpiryMonth').val() == '') || ($('#paymexpro_cardExpiryYear').val() == '')) {
    alert('Please enter an expiry date');
    return false;
  }
  /* check card name is not empty */  
  if ($('#paymexpro_cardName').val() == '') {
    alert('Please enter the name on the card');
    return false;
  }
  return true;
}

function paymark_verify(merchant) {
  window.open('http://www.paymark.co.nz/dart/darthttp.dll?etsl&tn=verify&merchantid='+merchant,'verify', 'scrollbars=yes, width=400, height=400');
}
</script>
{/literal}
</script>