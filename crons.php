<?php
define('ENVIRONMENT', 'production');
ob_start();
@session_start();
ini_set('memory_limit', '2048M');
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
	header('location: index.php');
} 
define('CroneJob',true)	;	
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
setTimeZone();
require_once BASEPATH.'lang/'.setLang(1);
require_once BASEPATH.'includes/crons.php';