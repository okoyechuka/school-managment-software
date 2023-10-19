<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	#	2co Payment module for SOA
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
		$checkout_seller_id = trim(paymentGatewayData($gateway,'param1'));			//This is saved in param1 column
		$amount_converted = round($amount_converted,2);
		//build HTML form
		$out = "<form id='2chechout' action='https://www.2checkout.com/checkout/purchase' method='post'>
        <input type='hidden' name='sid' value='$checkout_seller_id' />
        <input type='hidden' name='mode' value='2CO' />
        <input type='hidden' name='li_0_type' value='product' />
        <input type='hidden' name='li_0_name' value='Payment for order #$transaction_reference' />
        <input type='hidden' name='li_0_price' value='$amount_converted' />
        <input type='hidden' name='li_0_quantity' value='1' >
        <input type='hidden' name='card_holder_name' value='".userData(getUser(),'name')."' />
        <input type='hidden' name='street_address' value='".userData(getUser(),'address')."' />
        <input type='hidden' name='street_address2' value='' />
        <input type='hidden' name='city' value='".userData(getUser(),'city')."' />
        <input type='hidden' name='state' value='".userData(getUser(),'state')."' />
        <input type='hidden' name='zip' value='' />
        <input type='hidden' name='country' value='' />
        <input type='hidden' name='email' value='".userData(getUser(),'email')."' />
        <input type='hidden' name='phone' value='".userData(getUser(),'phone')."' />
        <input type='hidden' name='x_receipt_link_url' value='".home_base_url().'gateway/2checkout/callback.php?tx_reference='.$transaction_reference."'>";
		$out .= $button;
		$out .= '</form>';	
		return $out;	  
	}
	function validatePayment($transaction_reference,$amount,$user_id,$postdata='') {
		//Not required for 2checkout
	}	
}