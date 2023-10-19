<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	#	Paystack Payment module for SOA
	#	location: gateway/paystack/index.php
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
		$amount_converted = $amount_converted*100;							//This is just because Paystack wants the amount in K
		$secret_key = trim(paymentGatewayData($gateway,'param2'));			//Paystack stores private key in param2 column
		$public_key = trim(paymentGatewayData($gateway,'param1'));			//Paystack stores public key in param1 column
		//build HTML form
		$out = '<form id="paystack" action="'.home_base_url().'gateway/paystack/callback.php?tx_reference='.$transaction_reference.'" method="post" >';
		$out .= $button;
		$out .=	'<script 
					src="https://js.paystack.co/v1/inline.js" 
					data-key="'.$public_key.'" 
					data-email="'.userData(getUser(),'email').'"
					data-amount="'.round($amount_converted).'"
					data-ref="'.$transaction_reference.'"
					data-custom-button="inv_pay_b" 
				  >
				  </script>';
		$out .= '</form>';	
		return $out;	  
	}
	function validatePayment($transaction_reference,$amount,$user_id,$postdata='') {
		//Not required for paystack
	}	
}