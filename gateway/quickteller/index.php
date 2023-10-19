<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	#	Quickteller Payment module for SOA
	#	location: gateway/quickteller/index.php
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
		$amount_converted = round($amount_converted*100);
		$quickteller_payment_code = trim(paymentGatewayData($gateway,'param1'));	//This is saved in param1 column
		
		//build HTML form
		$out = "<form action='https://paywith.quickteller.com/' method='post' >
		  <input type='hidden' name='amount' value='$amount_converted' />
		  <input type='hidden' name='customerId' value='$transaction_reference' />
		  <input type='hidden' name='redirectUrl' value='".home_base_url().'gateway/quickteller/callback.php?tx_reference='.$transaction_reference."' />
		  <input type='hidden' name='paymentCode' value='$quickteller_payment_code' /> 
		  <input type='hidden' name='mobileNumber' value='".userData(getUser(),'phone')."' /> 
		  <input type='hidden' name='emailAddress' value='".userData(getUser(),'email')."' /> "; 
		$out .= $button;
		$out .= '</form>';	
		return $out;	  
	}
	function validatePayment($transaction_reference,$amount,$user_id,$postdata='') {
		//Not required for gtpay
	}	
} 
?>