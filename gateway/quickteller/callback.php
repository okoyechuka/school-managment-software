<?php
/*
	#	Quickteller Payment module callback for SOA
	#	location: gateway/quickteller/callback.php
	#	Developed by: Ynet Interactive
	#	Special thanks: Mr. White

*/
define('ENVIRONMENT', 'production');
ob_start();
session_start();
$URL = 'dashboard';
if(isset($_GET['url']))
{ 
	$URL = $_GET['url'];
}

if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(E_ALL);
		break;
	
		case 'testing':
		case 'production':
			error_reporting(0);
		break;

		default:
			exit('The application environment is not set correctly.');
	}
}
$system_path = '../../system';
$application_folder = 'hooks';
$module_folder = 'modules';
	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	// ensure there's a trailing slash
	$system_path = rtrim($system_path, '/').'/';
	// Is the system path correct?
	if ( ! is_dir($system_path))
	{
		exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));
	}
	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
	// The PHP file extension
	// this global constant is deprecated.
	define('EXT', '.php');
	// Path to the system folder
	define('BASEPATH', str_replace("\\", "/", $system_path));
	// Path to the front controller (this file)
	define('FCPATH', str_replace(SELF, '', __FILE__));
	// Name of the "system folder"
	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

	// The path to the "application" folder
	if (is_dir($application_folder))
	{
		define('APPPATH', $application_folder.'/');
		define('MODPATH', $module_folder.'/');
	}
	else
	{
		if ( ! is_dir(BASEPATH.$application_folder.'/'))
		{
			exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);
		}

		define('APPPATH', BASEPATH.$application_folder.'/');
		define('MODPATH', BASEPATH.$module_folder.'/');
	}
define('COR', BASEPATH.'core/');	
define('LIB', BASEPATH.'libraries/');	
define('HELP', BASEPATH.'helpers/');	
require_once '../../root.php';
require_once BASEPATH.'core/Config.php';
foreach (glob(COR."/*.php") as $filename) { include $filename; }
foreach (glob(HELP."/*.php") as $filename) { include $filename; }
foreach (glob(LIB."/*.php") as $filename) { include $filename; }
$Config = new CI_Config();
setTimeZone();
require_once BASEPATH.'lang/'.setLang(1);
global $server;

$is_valid = 0;
$response = $LANG['Payment verification failed'];
//Load saved transaction from Session is available
$Transaction_id = $_SESSION['Transaction_id'];
$Transaction_ref = $_SESSION['tran_reference'];
$Payment_gateway = $_SESSION['payment_gateway'];
if(!isset($_SESSION['tran_reference']) || empty($_SESSION['tran_reference'])) {
	//use query string
	$result = mysqli_query($server,"SELECT * FROM transactions WHERE transaction_reference = '".$_REQUEST['local_reference']."'"); 
	$row = mysqli_fetch_assoc($result);
	$Transaction_id = $row['id'];
	$Transaction_ref = $row['transaction_reference'];
	$Payment_gateway = $row['gateway'];
}
$_SESSION['Transaction_id'] = $Transaction_id ;
$payment_method = $alias = paymentGatewayData($Payment_gateway,'type');


if($Transaction_ref!="") {
	$_SESSION['reference'] = $Transaction_ref;
	$comments = 'Your payment was not successful';
	$is_valid = 0;
	$resp_code = $_REQUEST['resp_code'];
	$tx_ref = $_REQUEST['tx_ref'];
	$comments = $_REQUEST['resp_desc'];
	if(!empty($resp_code)) { 			
		$respinseCode = $resp_code;	
		$desc = $comments;
		if($respinseCode == '00') {
			$is_valid = 1;
			$comments = $json['ResponseDescription'];
		} else {
			$is_valid = -1;
			$comments = 'Payment Failed '.$desc;
		}
	} else { 
		$is_valid = -1;
		$comments = 'Payment Failed '.$comments;
	}	
}

if($is_valid < 1) {
	$_SESSION['comment'] = $comments;
	mysqli_query($server,"UPDATE transactions SET status = 'Failed' WHERE transaction_reference = '$Transaction_ref'");
	mysqli_query($server,"UPDATE transactions SET gateway_comment = '".mysqli_real_escape_string($server,$comments)."' WHERE transaction_reference = '$Transaction_ref'");
	header('location: ../../userfee?failed');
	exit();
} else {
	processTransaction($Transaction_id,$payment_method);
	mysqli_query($server,"UPDATE transactions SET gateway_comment = '".mysqli_real_escape_string($server,$comments)."' WHERE transaction_reference = '$Transaction_ref'");
	header('location: ../../userfee?success');
	exit();	
}
?>
