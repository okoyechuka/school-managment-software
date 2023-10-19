<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  
global $configverssion_id; global $configapp_version; global $userID; global $server; global $LANG;

define('TIMEOUT_MINUTES', 30);
define('TIMEOUT_CHECK_ACTIVITY', true);
define('SESSION_MASK', '553095209538359359');
define('SESSION_EXP', '16200');
// timeout in seconds
$timeout = (TIMEOUT_MINUTES == 0 ? 0 : time() + TIMEOUT_MINUTES * 1200);

$working_file=basename($_SERVER['PHP_SELF']);
global $hooks;$hooks->do_action('LoginPageTop'); 
switch ($working_file) {
	case 'admin.php': 
	adminLoginForm();
	break;
	case 'apply.php': 
	applicantLoginForm();
	break;
	default: studentLoginForm();
}
global $hooks;$hooks->do_action('LoginPageBottom');