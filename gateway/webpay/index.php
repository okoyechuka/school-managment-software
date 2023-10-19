<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	#	WEbPay Payment module for SOA
	#	location: gateway/webpay/index.php
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
		$interswitch_product_id = trim(paymentGatewayData($gateway,'param1'));	//This is saved in param1 column
		$interswitch_mac_key = trim(paymentGatewayData($gateway,'param2'));	//This is saved in param2 column
		
		$notify_url = home_base_url().'gateway/webpay/callback.php?tx_reference='.$transaction_reference;
		$tran_ref = $transaction_reference;
		$form_action='https://webpay.interswitchng.com/paydirect/webpay/pay.aspx';				
		$inter_hash_string = trim($tran_ref.$interswitch_product_id.userData(getUser(),'email').$amount_converted.$notify_url.$interswitch_mac_key);
		$interswitch_hash = hash('sha512',$inter_hash_string, true);		
		
		//build HTML form
		$out = "<form action='$form_action' method='POST'>
        <input type='hidden' value='$inter_hash_string'/>
        <input name='product_id' type='hidden' value='$interswitch_product_id'/>
        <input name='pay_item_id' type='hidden' value='".userData(getUser(),'email')."'/>
        <input name='amount' type='hidden' value='$amount_converted'/>
        <input name='currency' type='hidden' value='566'/>
        <input name='cust_name' type='hidden' value='".userData(getUser(),'name')."'/>
        <input name='cust_id' type='hidden' value='".userData(getUser(),'email')."' />
        <input name='site_redirect_url' type='hidden' value='$notify_url'/>
        <input name='txn_ref' type='hidden' value='$transaction_reference'/>
        <input name='hash' type='hidden' value='$interswitch_hash'/>  ";
		$out .= $button;
		$out .= '</form>';	
		return $out;	  
	}
	function validatePayment($transaction_reference,$amount,$user_id,$postdata='') {
		//Not required for webpay
	}	
}
?>
