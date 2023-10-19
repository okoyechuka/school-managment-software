<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	#	GTPay Payment module for SOA
	#	location: gateway/gtpay/index.php
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
		$uemail = empty(userData($userID,'email'))?getSetting('email'):userData($userID,'email');
		
		if($gatewayCurrencyCode==$userCurrencyCode) {
			$amount_converted = $amount;											// No conversion needed
		}
		/* ------------------------ Gateway specific script -------- */
		$amount_converted = round($amount_converted*100);
		$gtpay_merchant_id = trim(paymentGatewayData($gateway,'param1'));		//This is saved in param1 column
		$gtpay_hash_key = trim(paymentGatewayData($gateway,'param2'));	//This is saved in param2 column
		
		$notify_url = home_base_url().'gateway/gtpay/callback.php?tx_reference='.$transaction_reference;
		$tran_ref = $transaction_reference;
		$tf = trim($tran_ref.$amount_converted.$notify_url.$gtpay_hash_key);
		$hash = hash('sha512',$tf);

		$tf = trim($gtpay_merchant_id.$tran_ref.$amount_converted.'566'.getUser().$notify_url.$gtpay_hash_key);
	
		$hash2 = hash('sha512',$tf);
		
		//build HTML form
		$out = "<form action='http://gtweb2.gtbank.com/GTPayM/Tranx.aspx' method='post'>
		<input type='hidden' name='gtpay_mert_id' value='$gtpay_merchant_id' />
		<input type='hidden' name='gtpay_tranx_id' value='$transaction_reference' />
		<input type='hidden' name='gtpay_tranx_amt' value='$amount_converted' />
		<input type='hidden' name='gtpay_tranx_curr' value='566' />
		<input type='hidden' name='gtpay_cust_id' value='".getUser()."' />
		<input type='hidden' name='gtpay_cust_name' value='".userData(getUser(),'name')."' />
		<input type='hidden' name='gtpay_tranx_memo' value='Payment for transaction #$transaction_reference' />
		<input type='hidden' name='gtpay_hash' value='$hash2' />
		<input type='hidden' name='gtpay_tranx_noti_url' value='".home_base_url().'gateway/gtpay/callback.php?tx_reference='.$transaction_reference."'>";
		$out .= $button;
		$out .= '</form>';	
		return $out;	  
	}
	function validatePayment($transaction_reference,$amount,$user_id,$postdata='') {
		//Not required for gtpay
	}	
}

?>