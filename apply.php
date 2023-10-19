<?php
define('ENVIRONMENT', 'production');
ob_start();
session_start();
$URL = 'apply';
if(isset($_GET['url']))
{ 
	$URL = $_GET['url'];
}
if($URL=='incomingCallback') header('HTTP/1.0 200 OK');
global $URL;
$_SESSION['request_token'] = time(); 
if(isset($_GET['logout'])) {
	setcookie("SOAApply", '', $timeout, '/'); // clear password;
	$_SESSION['SOAApply'] = 0;
	unset($_COOKIE['SOAApply']);
	unset($_SESSION['SOAApply']);

	$_SESSION['manageUser'] = 0;
	unset($_SESSION['manageUser']);
	
  header("location: apply.php");
  exit;
}
if(isset($_GET['exituser'])) {
	$_SESSION['manageUser'] = 0;
	unset($_SESSION['manageUser']);	
	header("location: admin.php");
  	exit;
}
if(isset($_GET['ref'])) {
	$_SESSION['referrer']=$_GET['ref'];
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

$system_path = 'system';
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
if(isset($_GET['lang'])) {
	if(file_exists(BASEPATH.'lang/'.strtolower($_GET['lang']).'.php')) { $_SESSION['lang'] = strtolower($_GET['lang']);}
	header('location: apply.php');
} 
	
define('COR', BASEPATH.'core/');	
define('LIB', BASEPATH.'libraries/');	
define('HELP', BASEPATH.'helpers/');	
define('HOOKS', BASEPATH.'eventhooks/');	
require_once 'root.php'; 
require_once BASEPATH.'core/Config.php';
foreach (glob(COR."/*.php") as $filename) { include $filename; }
foreach (glob(HELP."/*.php") as $filename) { include $filename; }
foreach (glob(LIB."/*.php") as $filename) { include $filename; } 
foreach (glob(HOOKS."/*.php") as $filename) { include $filename; }
global $hooks;
$Config = new CI_Config();
setSchoolID();
setTimeZone();
require_once BASEPATH.'lang/'.setLang(1); 
loadHeaders($URL);
which_app($URL);
loadFooters($URL);