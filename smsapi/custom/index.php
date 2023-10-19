<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	#	Custom SMS module for SOA
	#	location: smsapi/custom/index.php
	#	Developed by: Ynet Interactive
	#	Special thanks: Mr. White
*/
global $LANG;
global $configverssion_id;
global $configapp_version;
global $server;

$message_id = $THIS_MESSAGE_ID;
$school_id = singleMessageData($THIS_MESSAGE_ID,'customer_id');
$gateway_id = $school_id;

$customer_id = singleMessageData($THIS_MESSAGE_ID,'customer_id');
$from = singleMessageData($THIS_MESSAGE_ID,'sender_id');
$to = singleMessageData($THIS_MESSAGE_ID,'recipient');
$message = singleMessageData($THIS_MESSAGE_ID,'message');

//gateway authentication stuffs		
$authentication = smsGatewayData($gateway_id,'authentication'); 
$username = smsGatewayData($gateway_id,'username'); 
$password = smsGatewayData($gateway_id,'password'); 
$base64_encode= smsGatewayData($gateway_id,'base64_encode'); 
$authenticate = $username.':'.$password;
if($base64_encode>0) {
	$authenticate = base64_encode($username.':'.$password);
}

//parameters
$base_url = smsGatewayData($gateway_id,'base_url'); 
$success_word = smsGatewayData($gateway_id,'success_word'); 
$success_logic= smsGatewayData($gateway_id,'success_logic'); 
$json_encode = smsGatewayData($gateway_id,'json_encode'); 
$request_type= smsGatewayData($gateway_id,'request_type'); 

if($request_type == 'POST') { 
	$data = array();
	$data[smsGatewayData($gateway_id,'sender_field')] = $from;
	$data[smsGatewayData($gateway_id,'recipient_field')] = $to;
	$data[smsGatewayData($gateway_id,'message_field')] = $message;					
	if( (smsGatewayData($gateway_id,'param1_field')) != '')
	$data[smsGatewayData($gateway_id,'param1_field')] = smsGatewayData($gateway_id,'param1_value');				
	if( (smsGatewayData($gateway_id,'param2_field')) != '')
	$data[smsGatewayData($gateway_id,'param2_field')] = smsGatewayData($gateway_id,'param2_value');					
	if( (smsGatewayData($gateway_id,'param3_field')) != '')
	$data[smsGatewayData($gateway_id,'param3_field')] = smsGatewayData($gateway_id,'param3_value');					
	if( (smsGatewayData($gateway_id,'param4_field')) != '')
	$data[smsGatewayData($gateway_id,'param4_field')] = smsGatewayData($gateway_id,'param4_value');

	$data_string = $data;
	$encoded = smsGatewayData($gateway_id,'json_encode');  
	if($encoded > 0) {
		$data_string = json_encode($data);
	}

	if($encoded > 0) {
		$x = curl_init($base_url);
		curl_setopt($x, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($x, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
		if($authentication > 0) { 
			curl_setopt($x, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'authorization: Basic "'.$authenticate.'"',
				'Accept: application/json',
				'Content-Length: ' . strlen($data_string))
			);
		} else {
			curl_setopt($x, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Accept: application/json',
				'Content-Length: ' . strlen($data_string))
			);					
		}
		curl_setopt($x, CURLOPT_TIMEOUT, 15);
		curl_setopt($x, CURLOPT_CONNECTTIMEOUT, 15);					
	} else { 
		$post = http_build_query($data);
		$x = curl_init($base_url );
		curl_setopt($x, CURLOPT_POST, true);
		curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
		if($authentication > 0) { 
			curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($x, CURLOPT_USERPWD, $authenticate);
		}
		curl_setopt($x, CURLOPT_POSTFIELDS, $post);
	}
	
	$response = curl_exec($x);
	curl_close($x);
	if(curl_error($x)) {
		$response = curl_error($x);	
	}				
} else {			
	$url = $base_url.'?';
	if(parse_url($base_url, PHP_URL_QUERY)) {
		$url = $base_url.'&';
	}
	$url .= smsGatewayData($gateway_id,'sender_field').'='.urlencode($from);
	$url .= '&'.smsGatewayData($gateway_id,'recipient_field').'='.$to;
	$url .= '&'.smsGatewayData($gateway_id,'message_field').'='.urlencode($message);					
	
	if( (smsGatewayData($gateway_id,'param1_field')) != '')
	$url .= '&'.smsGatewayData($gateway_id,'param1_field').'='.urlencode(smsGatewayData($gateway_id,'param1_value'));				
	if( (smsGatewayData($gateway_id,'param2_field')) != '')
	$url .= '&'.smsGatewayData($gateway_id,'param2_field').'='.urlencode(smsGatewayData($gateway_id,'param2_value'));					
	if( (smsGatewayData($gateway_id,'param3_field')) != '')
	$url .= '&'.smsGatewayData($gateway_id,'param3_field').'='.urlencode(smsGatewayData($gateway_id,'param3_value'));					
	if( (smsGatewayData($gateway_id,'param4_field')) != '')
	$url .= '&'.smsGatewayData($gateway_id,'param4_field').'='.urlencode(smsGatewayData($gateway_id,'param4_value'));
	
	$ch = curl_init($url);	
	if($authentication > 0) { 
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
		curl_setopt($ch, CURLOPT_USERPWD, $authenticate);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
	curl_setopt($ch, CURLOPT_FAILONERROR, true);	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
	if(curl_error($ch)) {
	 	$response = curl_error($ch);	
	}
	curl_close($ch);				
}

if(!$response) {
	  $status2 = mysqli_real_escape_string($server,$response);
	$status = 'Connection to Gateway Failed. '.$status2;  //. curl_error($ch)
	mysqli_query($server, "UPDATE  `messagedetails` SET `status` = 'failed', `notice` = '$status' WHERE `id` = '$message_id'");
} else {
	if(strtolower($success_logic) == 'contain') {
		if((stripos(strtolower($response),strtolower($success_word)) !== false)) {
		   $status = 'sent';
			mysqli_query($server, "UPDATE  `messagedetails` SET `status` = 'sent' WHERE `id` = '$message_id'");
	   } else {
		  $status = $response;
		  $status = mysqli_real_escape_string($server,$status);
		  mysqli_query($server, "UPDATE  `messagedetails` SET `status` = 'failed', `notice` = '$status' WHERE `id` = '$message_id'");
	   }
	} else {
	   if((stripos(strtolower($response),strtolower($success_word)) === false)) {
		   $status = 'sent';
			mysqli_query($server, "UPDATE  `messagedetails` SET `status` = 'sent' WHERE `id` = '$message_id'");
	   } else {
		  $status = $response;
		  $status = mysqli_real_escape_string($server,$status);
		  mysqli_query($server, "UPDATE  `messagedetails` SET `status` = 'failed',  `notice` = '$status' WHERE `id` = '$message_id'");
	   }
	}
}
?>