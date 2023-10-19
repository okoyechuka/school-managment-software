<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	#	Stripe Payment module for SOA
	#	location: gateway/2checkout/index.php
	#	Developed by: Ynet Interactive
	#	Special thanks: Mr. White

*/
global $LANG;
global $configverssion_id;
global $configapp_version;
global $server;
$userID = getUser();
global $userID;

class PaymentGateway {
	function initiatePayment($gateway,$transaction_reference,$amount,$user_id,$button='') {
		/* ------------------------ Standard Gateway operaions -------- */
		$Currency = new DefaultCurrency();
		$userSymbul = $Currency->Symbul(getUser());
		$userCurrencyCode = $Currency->Code(getUser());	
		$school_id = $_SESSION['school_id'];
		global $LANG;
		$gatewayCurrencyCode = paymentGatewayData($gateway,'currency_id');			//get the currency Code for this gateway
		$gatewayCurrRate = currencyExchangeRate($gatewayCurrencyCode);				//exchange rate for the gateway's curremcy
		$amount_converted = $amount*$gatewayCurrRate;								//Convert amount to gateway's currency
		if($gatewayCurrencyCode==$userCurrencyCode) {
			$amount_converted = $amount;											// No conversion needed
		}
		/* ------------------------ Gateway specific script -------- */
		$stripe_publishable_api_key = trim(paymentGatewayData($gateway,'param1'));	//This is saved in param1 column
		
		//build HTML form
		$out = '<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
        <script type="text/javascript">
            Stripe.setPublishableKey(\''.$stripe_publishable_api_key.'\');
            function stripeResponseHandler(status, response) {
                if (response.error) {
                    $(\'#inv_pay_b\').removeAttr("disabled");
					swal("Oops!", response.error.message, "warning");
                } else {
                    var form$ = $("#payment-form");
                    var token = response[\'id\'];
                    form$.append("<input type=\'hidden\' name=\'stripeToken\' value=\'" + token + "\' />");
                    form$.get(0).submit();
                }
            }
            $(document).ready(function() {
                $("#inv_pay_b").click(function(event) {
                    $(\'#inv_pay_b\').attr("disabled", "disabled");
                    Stripe.createToken({
                        number: $(\'.card-number\').val(),
                        cvc: $(\'.card-cvc\').val(),
                        exp_month: $(\'.card-expiry-month\').val(),
                        exp_year: $(\'.card-expiry-year\').val()
                    }, stripeResponseHandler);
                    return false; // submit from callback
                });
            });
        </script>
	<div class="panel-body2">
        <form action="'.home_base_url().'gateway/stripe/callback.php?tx_reference='.$transaction_reference.'" method="POST" id="payment-form" onsubmit="return false;">
        	<table width="90%" border="0" cellspacing="0" cellpadding="0">         
              <tr class="cont">
                <td align="right">
						<div class="row no-m-t no-m-b">
                             <div class="input-field col s12">
                                   <input required id="card-number" size="20" autocomplete="off" type="text" class="validate card-number"  value="">
                                   <label for="card-number">Card Number</label>
                             </div>
                       	  </div>
						<div class="row no-m-t no-m-b">
                             <div class="input-field col s12">
                                   <input required id="card-cvc" size="4" autocomplete="off" type="text" class="validate card-cvc"  value="">
                                   <label for="card-cvc">CVC</label>
                             </div>
                       	  </div>
						<div class="row no-m-t no-m-b">
                             <div class="input-field col s6">
                                   <input required id="card-expiry-month" size="2" autocomplete="off" type="number" class="validate card-expiry-month"  value="">
                                   <label for="card-expiry-month">Expiration (MM)</label>
                             </div>
							 <div class="input-field col s6">
                                   <input required id="card-expiry-year" size="4" autocomplete="off" type="number" class="validate card-expiry-year"  value="">
                                   <label for="card-expiry-year">Expiration (YYYY)</label>
                             </div>
                       	  </div>
				</td>
              </tr>             
            </table>         
            <input type="hidden" name="transaction_id" value="'.$transaction_reference.'">
            <input type="hidden" name="amount" value="'.round($amount_converted*100).'">
    </div>';        
		$out .= $button;
		$out .= '</form>';
		return $out;	  
	}
	function validatePayment($transaction_reference,$amount,$user_id,$postdata='') {
		//Not required for strip
	}	
}

?>
