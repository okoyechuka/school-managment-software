<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
global $configverssion_id;
global $configapp_version;
//set working school ID
setSchoolID();

/**
 * Y-iT21
 *
 * pplication development framework for PHP 5.6.3 or newer
 *
 * @package		YiT21
 * @author		Chuka Okoye
 * @copyright	Copyright (c) 20011 - 2022, Chuka Okoyee.
 * @since		Version 6.1
 * @filesource
 */
 function getUser() {
	if(!isset($_SESSION['localsession_id'])) return 0;
	$session_user=0;
	$working_file=basename($_SERVER['PHP_SELF']);
	switch ($working_file) {
		case 'admin.php': 
			$session_user=@$_SESSION['SOAAdmin'];
			if(!is_valid_session($session_user)) {
				$session_user = 0;
			}
		break;
		case 'apply.php': 
			$session_user=@$_SESSION['SOAApply'];
		break;
		default: 
			if(isset($_SESSION['SOAAdmin'])) {
				if(!is_valid_session($_SESSION['SOAAdmin'])) {
					$session_user=@$_SESSION['SOAUser'];
				} else {
					$session_user=$_SESSION['SOAAdmin'];
				}
			} else {
				$session_user=@$_SESSION['SOAUser'];
			}
	}
	return $session_user; 
}	

 function schoolByDomain() {
	global $server;
	$domain = $_SERVER['SERVER_NAME'];
	$domain = str_replace('www.','',$domain);
	$domain = str_replace('http://','',$domain);
	$domain = str_replace('https://','',$domain);
	$query="SELECT * FROM settings WHERE field = 'schoolDomain' AND value = '$domain'"; 
	$result = mysqli_query($server,$query) or die(mysqli_error($server));  
	$row = mysqli_fetch_assoc($result); 
	$value = $row['school_id'];	
	if(empty($value)) {
		return '0';
	} else {
		if(getUser()>0&&isOwner(getUser())) return 0;			
		return $value;
	}	
}
 
function setSchoolID() {
	global $server;
	$school_id=1;
	//by domain
	$sbd = schoolByDomain();
	if($sbd>0) { 
		$school_id = $sbd;
	} else {
		//check by url
		if(isset($_GET['school_username'])&&!empty($_GET['school_username'])) {
			$sbu=mysqli_real_escape_string($server,$_GET['school_username']);
			$query="SELECT * FROM schools WHERE username = '$sbu'"; 
			$result = mysqli_query($server,$query);  
			$row = mysqli_fetch_assoc($result); 
			$value = $row['id'];	
			if($value>0) $school_id=$value;
		}
	}
	if(getUser()>0) {
		$user=getUser();
		$query="SELECT * FROM users WHERE id = '$user'"; 
		$result = mysqli_query($server,$query);  
		$row = mysqli_fetch_assoc($result); 
		$school_id = $row['school_id'];
	}
	if(isset($_SESSION['mana_school_id'])&&$_SESSION['mana_school_id']>0) {
		$school_id = $_SESSION['mana_school_id'];	
	}
	$_SESSION['school_id']=$school_id;
}
  
if ( ! function_exists('getSetting')){
	function getSetting($field,$sch='none') {
		global $server;
		$school_id = $_SESSION['school_id'];
		if($sch != 'none') $school_id = $sch;
		$query="SELECT * FROM settings WHERE field = '$field' AND school_id = '$school_id'"; 
		$result = mysqli_query($server,$query) or die(mysqli_error($server));  
		$row = mysqli_fetch_assoc($result); 
		$value = $row['value'];	
		if(empty($value)) {
		return false;
		} else {
		return $value;
		}
	}
} 
 
$site_configs=array(
'domain'=>"",
'google_recaptcha_sitekey'=>getSetting('google_recaptcha_sitekey'),
'google_recaptcha_secret'=>getSetting('google_recaptcha_secret'),
);
global $site_configs;
if ( ! function_exists('set_cookie')) {
function set_cookie($ck,$cv,$duration=604800){
	$accessRange=".{$_SERVER['HTTP_HOST']}"; 	//$accessRange="";
	setcookie($ck,$cv,time()+$duration,"/",$accessRange);
}
}
if ( ! function_exists('get_cookie')){
function get_cookie($ck){ return isset($_COOKIE[$ck])?$_COOKIE[$ck]:"";}
}

if ( ! function_exists('verify_google_recaptcha')){
function verify_google_recaptcha($user_response=''){
	if(get_cookie('recaptcha_validated'))return true;
		
	global $site_configs;
	if(empty($site_configs['google_recaptcha_secret']))return true; //'Admin Error: Recaptcha secret not set';
	
	if(empty($user_response))$user_response=@$_POST['g-recaptcha-response'];
	if(empty($user_response))return 'Captcha response not supplied';
	
	$capt_secret=$site_configs['google_recaptcha_secret'];
	
	$post_data=array('secret'=>$capt_secret,'response'=>$user_response,'remoteip'=>$_SERVER['REMOTE_ADDR']);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,'https://www.google.com/recaptcha/api/siteverify');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	$returnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if($returnCode != 200)$response_info=curl_error($ch);
	curl_close($ch);
			
	$json=@json_decode($response,true);
	if(empty($json))return 'Unable to validate captcha at the moment';
	if(!empty($json['error-codes']))return implode('<br/>',$json['error-codes']);
	if(!empty($json['success']))set_cookie('recaptcha_validated',true);		
	return @$json['success'];
}
}
if ( ! function_exists('get_recaptcha_button')){
function get_recaptcha_button(){
	if(get_cookie('recaptcha_validated'))return "";
	global $site_configs;
	if(empty($site_configs['google_recaptcha_sitekey']))return '';
	
	return "<div class='g-recaptcha' data-sitekey='{$site_configs['google_recaptcha_sitekey']}' style='transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;'></div>";
}
}
if ( ! function_exists('get_recaptcha_script')){
function get_recaptcha_script(){
	if(get_cookie('recaptcha_validated'))return "";
	$s=_is_https()?'https:':'http:';
	$s='https:';
	return "<script src='$s//www.google.com/recaptcha/api.js'></script>";
}
}
if ( ! function_exists('_is_https')){
	function _is_https(){
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')return true;
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') return true;
		return false;
	}
}

if ( ! function_exists('is_php'))
{
	function is_php($version = '5.0.0')
	{
		static $_is_php;
		$version = (string)$version;

		if ( ! isset($_is_php[$version]))
		{
			$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
		}

		return $_is_php[$version];
	}
}

// ------------------------------------------------------------------------

/**
 * Tests for file writability
 *
 * is_writable() returns TRUE on Windows servers when you really can't write to
 * the file, based on the read-only attribute.  is_writable() is also unreliable
 * on Unix servers if safe_mode is on.
 *
 * @access	private
 * @return	void
 */
if ( ! function_exists('is_really_writable'))
{
	function is_really_writable($file)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
		{
			return is_writable($file);
		}

		// For windows servers and safe_mode "on" installations we'll actually
		// write a file then read it.  Bah...
		if (is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(mt_rand(1,100).mt_rand(1,100));

			if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, DIR_WRITE_MODE);
			@unlink($file);
			return TRUE;
		}
		elseif ( ! is_file($file) OR ($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}
}

function loadHeaders($URL) {
global $userID; global $LAST_NOTICE; global $LANG; global $server;
if(!isset($_GET['export']) && !isset($_GET['exportc']) && !isset($_GET['exportd'])) {
if($URL != 'API' && $URL != 'smsAPI' && $URL != 'incomingCallback') require_once BASEPATH.'includes/header.php';
if($URL != 'API' && $URL != 'smsAPI' && $URL != 'incomingCallback') require_once BASEPATH.'includes/menu.php';	
}
}
function loadFooters($URL) {
global $userID; global $LAST_NOTICE; global $LANG; global $server;	
if(!isset($_GET['export']) && !isset($_GET['exportc']) && !isset($_GET['exportd'])) {
if($URL != 'API' && $URL != 'smsAPI' && $URL != 'incomingCallback') require_once BASEPATH.'includes/footer.php';
if($URL != 'API' && $URL != 'smsAPI' && $URL != 'incomingCallback') require_once BASEPATH.'includes/scripts.php';	
if($URL != 'API' && $URL != 'smsAPI' && $URL != 'incomingCallback') injectionMatrix();	
}
}

// ------------------------------------------------------------------------

/**
* Class registry
*
* This function acts as a singleton.  If the requested class does not
* exist it is instantiated and set to a static variable.  If it has
* previously been instantiated the variable is returned.
*
* @access	public
* @param	string	the class name being requested
* @param	string	the directory where the class should be found
* @param	string	the class name prefix
* @return	object
*/
if ( ! function_exists('load_class'))

{
	function &load_class($class, $directory = 'libraries', $prefix = 'CI_')
	{
		static $_classes = array();

		// Does the class exist?  If so, we're done...
		if (isset($_classes[$class]))
		{
			return $_classes[$class];
		}

		$name = FALSE;

		// Look for the class first in the local application/libraries folder
		// then in the native system/libraries folder
		foreach (array(APPPATH, BASEPATH) as $path)
		{
			if (file_exists($path.$directory.'/'.$class.'.php'))
			{
				$name = $prefix.$class;

				if (class_exists($name) === FALSE)
				{
					require($path.$directory.'/'.$class.'.php');
				}

				break;
			}
		}

		// Is the request a class extension?  If so we load it too
		if (file_exists(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php'))
		{
			$name = config_item('subclass_prefix').$class;

			if (class_exists($name) === FALSE)
			{
				require(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php');
			}
		}

		// Did we find the class?
		if ($name === FALSE)
		{
			// Note: We use exit() rather then w_error() in order to avoid a
			// self-referencing loop with the Excptions class
			exit('Unable to locate the specified class: '.$class.'.php');
		}

		// Keep track of what we just loaded
		is_loaded($class);

		$_classes[$class] = new $name();
		return $_classes[$class];
	}
}

// --------------------------------------------------------------------

/**
* Keeps track of which libraries have been loaded.  This function is
* called by the load_class() function above
*
* @access	public
* @return	array
*/
if ( ! function_exists('is_loaded'))
{
	function &is_loaded($class = '')
	{
		static $_is_loaded = array();

		if ($class != '')
		{
			$_is_loaded[strtolower($class)] = $class;
		}

		return $_is_loaded;
	}
}

// ------------------------------------------------------------------------

/**
* Loads the main config.php file
*
* This function lets us grab the config file even if the Config class
* hasn't been instantiated yet
*
* @access	private
* @return	array
*/
if ( ! function_exists('get_config'))
{
	function &get_config($replace = array())
	{
		static $_config;

		if (isset($_config))
		{
			return $_config[0];
		}

		// Is the config file in the environment folder?
		if ( ! defined('ENVIRONMENT') OR ! file_exists($file_path = APPPATH.'config/'.ENVIRONMENT.'/config.php'))
		{
			$file_path = APPPATH.'config/config.php';
		}

		// Fetch the config file
		if ( ! file_exists($file_path))
		{
			exit('The configuration file does not exist.');
		}

		require($file_path);

		// Does the $config array exist in the file?
		if ( ! isset($config) OR ! is_array($config))
		{
			exit('Your config file does not appear to be formatted correctly.');
		}

		// Are any values being dynamically replaced?
		if (count($replace) > 0)
		{
			foreach ($replace as $key => $val)
			{
				if (isset($config[$key]))
				{
					$config[$key] = $val;
				}
			}
		}

		return $_config[0] =& $config;
	}
}

// ------------------------------------------------------------------------

/**
* Returns the specified config item
*
* @access	public
* @return	mixed
*/
if ( ! function_exists('config_item'))
{
	function config_item($item)
	{
		static $_config_item = array();

		if ( ! isset($_config_item[$item]))
		{
			$config =& get_config();

			if ( ! isset($config[$item]))
			{
				return FALSE;
			}
			$_config_item[$item] = $config[$item];
		}

		return $_config_item[$item];
	}
}

// ------------------------------------------------------------------------

/**
* Error Handler
*
* This function lets us invoke the exception class and
* display errors using the standard error template located
* in application/errors/errors.php
* This function will send the error page directly to the
* browser and exit.
*
* @access	public
* @return	void
*/
if ( ! function_exists('show_error'))
{
	function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
	{
		$_error =& load_class('Exceptions', 'core');
		echo $_error->show_error($heading, $message, 'error_general', $status_code);
		exit;
	}
}

// ------------------------------------------------------------------------

/**
* 404 Page Handler
*
* This function is similar to the show_error() function above
* However, instead of the standard error template it displays
* 404 errors.
*
* @access	public
* @return	void
*/
if ( ! function_exists('show_404'))
{
	function show_404($page = '', $log_error = TRUE)
	{
		$_error =& load_class('Exceptions', 'core');
		$_error->show_404($page, $log_error);
		exit;
	}
}

// ------------------------------------------------------------------------

/**
* Error Logging Interface
*
* We use this as a simple mechanism to access the logging
* class and send messages to be logged.
*
* @access	public
* @return	void
*/
if ( ! function_exists('log_message'))
{
	function log_message($level = 'error', $message, $php_error = FALSE)
	{
		static $_log;

		if (config_item('log_threshold') == 0)
		{
			return;
		}

		$_log =& load_class('Log');
		$_log->write_log($level, $message, $php_error);
	}
}

// ------------------------------------------------------------------------

/**
 * Set HTTP Status Header
 *
 * @access	public
 * @param	int		the status code
 * @param	string
 * @return	void
 */
if ( ! function_exists('set_status_header'))
{
	function set_status_header($code = 200, $text = '')
	{
		$stati = array(
							200	=> 'OK',
							201	=> 'Created',
							202	=> 'Accepted',
							203	=> 'Non-Authoritative Information',
							204	=> 'No Content',
							205	=> 'Reset Content',
							206	=> 'Partial Content',

							300	=> 'Multiple Choices',
							301	=> 'Moved Permanently',
							302	=> 'Found',
							304	=> 'Not Modified',
							305	=> 'Use Proxy',
							307	=> 'Temporary Redirect',

							400	=> 'Bad Request',
							401	=> 'Unauthorized',
							403	=> 'Forbidden',
							404	=> 'Not Found',
							405	=> 'Method Not Allowed',
							406	=> 'Not Acceptable',
							407	=> 'Proxy Authentication Required',
							408	=> 'Request Timeout',
							409	=> 'Conflict',
							410	=> 'Gone',
							411	=> 'Length Required',
							412	=> 'Precondition Failed',
							413	=> 'Request Entity Too Large',
							414	=> 'Request-URI Too Long',
							415	=> 'Unsupported Media Type',
							416	=> 'Requested Range Not Satisfiable',
							417	=> 'Expectation Failed',

							500	=> 'Internal Server Error',
							501	=> 'Not Implemented',
							502	=> 'Bad Gateway',
							503	=> 'Service Unavailable',
							504	=> 'Gateway Timeout',
							505	=> 'HTTP Version Not Supported'
						);

		if ($code == '' OR ! is_numeric($code))
		{
			show_error('Status codes must be numeric', 500);
		}

		if (isset($stati[$code]) AND $text == '')
		{
			$text = $stati[$code];
		}

		if ($text == '')
		{
			show_error('No status text available.  Please check your status code number or supply your own message text.', 500);
		}

		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

		if (substr(php_sapi_name(), 0, 3) == 'cgi')
		{
			header("Status: {$code} {$text}", TRUE);
		}
		elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0')
		{
			header($server_protocol." {$code} {$text}", TRUE, $code);
		}
		else
		{
			header("HTTP/1.1 {$code} {$text}", TRUE, $code);
		}
	}
}

// --------------------------------------------------------------------

/**
* Exception Handler
*
* This is the custom exception handler that is declaired at the top
* of Codeigniter.php.  The main reason we use this is to permit
* PHP errors to be logged in our own log files since the user may
* not have access to server logs. Since this function
* effectively intercepts PHP errors, however, we also need
* to display errors based on the current error_reporting level.
* We do that with the use of a PHP error template.
*
* @access	private
* @return	void
*/
if ( ! function_exists('_exception_handler'))
{
	function _exception_handler($severity, $message, $filepath, $line)
	{
		 // We don't bother with "strict" notices since they tend to fill up
		 // the log file with excess information that isn't normally very helpful.
		 // For example, if you are running PHP 5 and you use version 4 style
		 // class functions (without prefixes like "public", "private", etc.)
		 // you'll get notices telling you that these have been deprecated.
		if ($severity == E_STRICT)
		{
			return;
		}

		$_error =& load_class('Exceptions', 'core');

		// Should we display the error? We'll get the current error_reporting
		// level and add its bits with the severity bits to find out.
		if (($severity & error_reporting()) == $severity)
		{
			$_error->show_php_error($severity, $message, $filepath, $line);
		}

		// Should we log the error?  No?  We're done...
		if (config_item('log_threshold') == 0)
		{
			return;
		}

		$_error->log_exception($severity, $message, $filepath, $line);
	}
}

function moduleExist($title,$author,$type) {	
  global $server;	
  $type = strtolower($type);
	$query="SELECT id as value FROM modules WHERE (title LIKE '$title' AND author LIKE '$author') AND type = '$type'"; 
	$result = mysqli_query($server,$query) or die(mysqli_error($server));  
	$row = mysqli_fetch_assoc($result); 
	$found = $row['value'];
	if($found > 0) return true;
	return false;
}
// --------------------------------------------------------------------

/**
 * Remove Invisible Characters
 *
 * This prevents sandwiching null characters
 * between ascii characters, like Java\0script.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('remove_invisible_characters'))
{
	function remove_invisible_characters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();
		
		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)
		
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}
}
// ------------------------------------------------------------------------

/**
* Returns HTML escaped variable
*
* @access	public
* @param	mixed
* @return	mixed
*/
if ( ! function_exists('html_escape'))
{
	function html_escape($var)
	{
		if (is_array($var))
		{
			return array_map('html_escape', $var);
		}
		else
		{
			return htmlspecialchars($var, ENT_QUOTES, config_item('charset'));
		}
	}
}

// --------------------------------------------------------------------
/**
* @access	public
* @return	array
*/
function schoolUsernameTakens($username) {
	global $server;
	$query="SELECT * FROM schools WHERE username = '$username'"; 
	$result = mysqli_query($server,$query);  
	$row = mysqli_fetch_assoc($result); 
	return $row['id']>0?$row['id']:0;	 
}

function adinTakenUsername($username) {
	global $server;
	$query="SELECT * FROM users WHERE username = '$username'"; 
	$result = mysqli_query($server,$query);  
	$row = mysqli_fetch_assoc($result); 
	return $row['id']>0?$row['id']:0;	 
}

function is_valid_session($user_id) {
	global $server;
	$query = "SELECT * FROM users WHERE id = '$user_id'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < 1) {
		return false;
	}
	$row = mysqli_fetch_assoc($result); 
	$localsession_id = $row['localsession_id'];
	if($_SESSION['localsession_id'] != $localsession_id) {
		return false;	
	}
	return true;
}

if ( ! function_exists('which_app')){
	function which_app($url = ''){ 
		global $userID;
		global $configinstalled;
		global $payment_params;
		$working_file=basename($_SERVER['PHP_SELF']);
		switch ($working_file) {
			case 'admin.php': 
			$def_url = 'admindashboard';
			break;
			case 'apply.php': 
			$def_url = 'apply';
			break;
			default: $def_url = 'dashboard';
		}
	
		if(userRole($userID) == 5 || userRole($userID) == 6) {
			if($def_url == 'admindashboard')$def_url = 'dashboard';
		}
		if(userRole($userID) != 5 && userRole($userID) != 6) {
			if($def_url == 'dashboard')$def_url = 'admindashboard';
		}
		if(empty($url)) {
			$url = $def_url;	
		}
		if(!file_exists(APPPATH.$url.'.php')) {
			$url = $def_url;	
		}
		
		if(($url != 'API' && $url != 'incomingCallback') && getUser() < 1) {
			$url = 'login';	
		} else {
			if($working_file=='apply.php') {
				$url = 'apply';
			}
		}
		if($configinstalled != 'INSTALLED') {
			header('location: install/index.php');
		} else {
			if(getUser() >0 && (strpos($url,'module/') !== false)) {
				$nonsense =  explode("/", $url);
				$readthing = end($nonsense);
				$url = $readthing.'/index.php';
				include(MODPATH.$url);
			} else {			
				include(APPPATH.$url.'.php');
			}
		}
	}
} 


function setLang($file=0) {
	$language = getSetting('defaultLanguage');
	if(empty($language)) $language = 'en';
	if(getUser()>0) {
		$language = userData(getUser(),'language_id');
		if(empty($language)) $language = getSetting('defaultLanguage');
	} else {
		if(isset($_SESSION['lang'])) {
			$language = $_SESSION['lang'];
		}
	}
	if(empty($language)) $language = 'en';
	if($file>0) return strtolower($language).'.php';
	return strtolower($language);
}

function saveSettings($field,$value,$user=0) {
	ini_set('max_execution_time', 240); 
	ini_set('memory_limit', '2048M');
	global $server;
	$school_id = $user;
	if($school_id<1) $school_id=$_SESSION['school_id'];
	$query = "SELECT * FROM settings WHERE `field` = '$field' AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);	
	if($num < 1) {	
		mysqli_query($server, "INSERT INTO settings (`field`, `value`, `school_id`) VALUES ('$field', '$value', '$school_id');") or die(mysqli_error($server));
	} else {
		mysqli_query($server, "UPDATE `settings` SET `value` = '$value' WHERE `field` = '$field' AND `school_id` = '$school_id'") or die (mysqli_error($server));	
	}
	systemInformation();
}

function buildJSONResponse($error='',$success=false,$message='') {
	$data           = array(); 	
	$data['errors']  = $error;
	$data['success'] = $success;
	$data['message'] = $message;
	echo json_encode($data); exit;	
}
function genRandomPassword($length = 8)
	{
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$base = strlen($salt);
		$makepass = '';

		$random = genRandomBytes($length + 1);
		$shift = ord($random[0]);
		for ($i = 1; $i <= $length; ++$i)
		{
			$makepass .= $salt[($shift + ord($random[$i])) % $base];
			$shift += ord($random[$i]);
		}

		return $makepass;
	}

	function _toAPRMD5($value, $count)
	{
		/* 64 characters that are valid for APRMD5 passwords. */
		$APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		$aprmd5 = '';
		$count = abs($count);
		while (--$count)
		{
			$aprmd5 .= $APRMD5[$value & 0x3f];
			$value >>= 6;
		}
		return $aprmd5;
	}
	
function genRandomBytes($length = 16)
	{
		$sslStr = '';

		if (
			function_exists('openssl_random_pseudo_bytes')
			&& (version_compare(PHP_VERSION, '5.3.4') >= 0
				|| substr(PHP_OS, 0, 3) !== 'WIN'
			)
		)
		{
			$sslStr = openssl_random_pseudo_bytes($length, $strong);
			if ($strong)
			{
				return $sslStr;
			}
		}

		$bitsPerRound = 2;
		$maxTimeMicro = 400;
		$shaHashLength = 20;
		$randomStr = '';
		$total = $length;

		$urandom = false;
		$handle = null;
		if (function_exists('stream_set_read_buffer') && @is_readable('/dev/urandom'))
		{
			$handle = @fopen('/dev/urandom', 'rb');
			if ($handle)
			{
				$urandom = true;
			}
		}

		while ($length > strlen($randomStr))
		{
			$bytes = ($total > $shaHashLength)? $shaHashLength : $total;
			$total -= $bytes;

			$entropy = rand() . uniqid(mt_rand(), true) . $sslStr;
			$entropy .= implode('', @fstat(fopen( __FILE__, 'r')));
			$entropy .= memory_get_usage();
			$sslStr = '';
			if ($urandom)
			{
				stream_set_read_buffer($handle, 0);
				$entropy .= @fread($handle, $bytes);
			}
			else
			{

				$samples = 3;
				$duration = 0;
				for ($pass = 0; $pass < $samples; ++$pass)
				{
					$microStart = microtime(true) * 1000000;
					$hash = sha1(mt_rand(), true);
					for ($count = 0; $count < 50; ++$count)
					{
						$hash = sha1($hash, true);
					}
					$microEnd = microtime(true) * 1000000;
					$entropy .= $microStart . $microEnd;
					if ($microStart > $microEnd) {
						$microEnd += 1000000;
					}
					$duration += $microEnd - $microStart;
				}
				$duration = $duration / $samples;

				$rounds = (int)(($maxTimeMicro / $duration) * 50);

				$iter = $bytes * (int) ceil(8 / $bitsPerRound);
				for ($pass = 0; $pass < $iter; ++$pass)
				{
					$microStart = microtime(true);
					$hash = sha1(mt_rand(), true);
					for ($count = 0; $count < $rounds; ++$count)
					{
						$hash = sha1($hash, true);
					}
					$entropy .= $microStart . microtime(true);
				}
			}

			$randomStr .= sha1($entropy, true);
		}

		if ($urandom)
		{
			@fclose($handle);
		}

		return substr($randomStr, 0, $length);
	}
function getSalt($encryption = 'md5-hex', $seed = '', $plaintext = '')
	{
		// Encrypt the password.
		switch ($encryption)
		{
			case 'crypt':
			case 'crypt-des':
				if ($seed)
				{
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 2);
				}
				else
				{
					return substr(md5(mt_rand()), 0, 2);
				}
				break;

			case 'crypt-md5':

				if ($seed)
				{
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 12);
				}
				else
				{
					return '$1$' . substr(md5(mt_rand()), 0, 8) . '$';
				}
				break;

			case 'crypt-blowfish':
				if ($seed)
				{
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 16);
				}
				else
				{
					return '$2$' . substr(md5(mt_rand()), 0, 12) . '$';
				}
				break;

			case 'ssha':
				if ($seed)
				{
					return substr(preg_replace('|^{SSHA}|', '', $seed), -20);
				}
				else
				{
					return mhash_keygen_s2k(MHASH_SHA1, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
				}
				break;

			case 'smd5':
				if ($seed)
				{
					return substr(preg_replace('|^{SMD5}|', '', $seed), -16);
				}
				else
				{
					return mhash_keygen_s2k(MHASH_MD5, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
				}
				break;

			case 'aprmd5': /* 64 characters that are valid for APRMD5 passwords. */
				$APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

				if ($seed)
				{
					return substr(preg_replace('/^\$apr1\$(.{8}).*/', '\\1', $seed), 0, 8);
				}
				else
				{
					$salt = '';
					for ($i = 0; $i < 8; $i++)
					{
						$salt .= $APRMD5{rand(0, 63)};
					}
					return $salt;
				}
				break;

			default:
				$salt = '';
				if ($seed)
				{
					$salt = $seed;
				}
				return $salt;
				break;
		}
	}
	
	
function getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false)
	{
		// Get the salt to use.
		$salt = getSalt($encryption, $salt, $plaintext);

		// Encrypt the password.
		switch ($encryption)
		{
			case 'plain':
				return $plaintext;

			case 'sha':
				$encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext));
				return ($show_encrypt) ? '{SHA}' . $encrypted : $encrypted;

			case 'crypt':
			case 'crypt-des':
			case 'crypt-md5':
			case 'crypt-blowfish':
				return ($show_encrypt ? '{crypt}' : '') . crypt($plaintext, $salt);

			case 'md5-base64':
				$encrypted = base64_encode(mhash(MHASH_MD5, $plaintext));
				return ($show_encrypt) ? '{MD5}' . $encrypted : $encrypted;

			case 'ssha':
				$encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext . $salt) . $salt);
				return ($show_encrypt) ? '{SSHA}' . $encrypted : $encrypted;

			case 'smd5':
				$encrypted = base64_encode(mhash(MHASH_MD5, $plaintext . $salt) . $salt);
				return ($show_encrypt) ? '{SMD5}' . $encrypted : $encrypted;

			case 'aprmd5':
				$length = strlen($plaintext);
				$context = $plaintext . '$apr1$' . $salt;
				$binary = _bin(md5($plaintext . $salt . $plaintext));

				for ($i = $length; $i > 0; $i -= 16)
				{
					$context .= substr($binary, 0, ($i > 16 ? 16 : $i));
				}
				for ($i = $length; $i > 0; $i >>= 1)
				{
					$context .= ($i & 1) ? chr(0) : $plaintext[0];
				}

				$binary = _bin(md5($context));

				for ($i = 0; $i < 1000; $i++)
				{
					$new = ($i & 1) ? $plaintext : substr($binary, 0, 16);
					if ($i % 3)
					{
						$new .= $salt;
					}
					if ($i % 7)
					{
						$new .= $plaintext;
					}
					$new .= ($i & 1) ? substr($binary, 0, 16) : $plaintext;
					$binary = _bin(md5($new));
				}

				$p = array();
				for ($i = 0; $i < 5; $i++)
				{
					$k = $i + 6;
					$j = $i + 12;
					if ($j == 16)
					{
						$j = 5;
					}
					$p[] = _toAPRMD5((ord($binary[$i]) << 16) | (ord($binary[$k]) << 8) | (ord($binary[$j])), 5);
				}

				return '$apr1$' . $salt . '$' . implode('', $p) . _toAPRMD5(ord($binary[11]), 3);

			case 'md5-hex':
			default:
				$encrypted = ($salt) ? md5($plaintext . $salt) : md5($plaintext);
				return ($show_encrypt) ? '{MD5}' . $encrypted : $encrypted;
		}
	}

if ( ! function_exists('_bin'))
{
function _bin($hex)
	{
		$bin = '';
		$length = strlen($hex);
		for ($i = 0; $i < $length; $i += 2)
		{
			$tmp = sscanf(substr($hex, $i, 2), '%x');
			$bin .= chr(array_shift($tmp));
		}
		return $bin;
	}		
}

if ( ! function_exists('read_file'))
{
	function read_file($file)
	{
		if ( ! file_exists($file))
		{
			return FALSE;
		}

		if (function_exists('file_get_contents'))
		{
			return file_get_contents($file);
		}

		if ( ! $fp = @fopen($file, 'r+'))
		{
			return FALSE;
		}

		flock($fp, LOCK_SH);

		$data = '';
		if (filesize($file) > 0)
		{
			$data =& fread($fp, filesize($file));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $data;
	}
}
 
if ( ! function_exists('write_file'))
{
	function write_file($path, $data, $mode = 'w+')
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		return TRUE;
	}
}

 
if ( ! function_exists('delete_files'))
{
	function delete_files($path, $del_dir = FALSE, $level = 0)
	{
		// Trim the trailing slash
		$path = rtrim($path, DIRECTORY_SEPARATOR);

		if ( ! $current_dir = @opendir($path))
		{
			return FALSE;

		}

		while (FALSE !== ($filename = @readdir($current_dir)))
		{
			if ($filename != "." and $filename != "..")
			{
				if (is_dir($path.DIRECTORY_SEPARATOR.$filename))
				{
					// Ignore empty folders
					if (substr($filename, 0, 1) != '.')
					{
						delete_files($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $level + 1);
					}
				}
				else
				{
					unlink($path.DIRECTORY_SEPARATOR.$filename);
				}
			}

		}
		@closedir($current_dir);

		if ($del_dir == TRUE AND $level > 0)
		{
			return @rmdir($path);
		}

		return TRUE;
	}
}

if ( ! function_exists('get_filenames'))
{
	function get_filenames($source_dir, $include_path = FALSE, $_recursion = FALSE)
	{
		static $_filedata = array();

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}

			while (FALSE !== ($file = readdir($fp)))
			{
				if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0)
				{
					get_filenames($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, TRUE);
				}
				elseif (strncmp($file, '.', 1) !== 0)
				{
					$_filedata[] = ($include_path == TRUE) ? $source_dir.$file : $file;
				}
			}
			return $_filedata;
		}
		else
		{
			return FALSE;
		}
	}
}


if ( ! function_exists('get_dir_file_info'))
{
	function get_dir_file_info($source_dir, $top_level_only = TRUE, $_recursion = FALSE)
	{
		static $_filedata = array();
		$relative_path = $source_dir;

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}

			// foreach (scandir($source_dir, 1) as $file) // In addition to being PHP5+, scandir() is simply not as fast
			while (FALSE !== ($file = readdir($fp)))
			{
				if (@is_dir($source_dir.$file) AND strncmp($file, '.', 1) !== 0 AND $top_level_only === FALSE)
				{
					get_dir_file_info($source_dir.$file.DIRECTORY_SEPARATOR, $top_level_only, TRUE);
				}
				elseif (strncmp($file, '.', 1) !== 0)
				{
					$_filedata[$file] = get_file_info($source_dir.$file);
					$_filedata[$file]['relative_path'] = $relative_path;
				}
			}

			return $_filedata;
		}
		else
		{
			return FALSE;
		}
	}
}

if ( ! function_exists('xml_parser_create'))
{
	show_error('Your PHP installation does not support XML');
}


// ------------------------------------------------------------------------

/**
 * XML-RPC request handler class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	XML-RPC
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/xmlrpc.html
 */
class CI_Xmlrpc {

	var $debug			= FALSE;	// Debugging on or off
	var $xmlrpcI4		= 'i4';
	var $xmlrpcInt		= 'int';
	var $xmlrpcBoolean	= 'boolean';
	var $xmlrpcDouble	= 'double';
	var $xmlrpcString	= 'string';
	var $xmlrpcDateTime	= 'dateTime.iso8601';
	var $xmlrpcBase64	= 'base64';
	var $xmlrpcArray	= 'array';
	var $xmlrpcStruct	= 'struct';

	var $xmlrpcTypes	= array();
	var $valid_parents	= array();
	var $xmlrpcerr		= array();	// Response numbers
	var $xmlrpcstr		= array();  // Response strings

	var $xmlrpc_defencoding = 'UTF-8';
	var $xmlrpcName			= 'XML-RPC for CodeIgniter';
	var $xmlrpcVersion		= '1.1';
	var $xmlrpcerruser		= 800; // Start of user errors
	var $xmlrpcerrxml		= 100; // Start of XML Parse errors
	var $xmlrpc_backslash	= ''; // formulate backslashes for escaping regexp

	var $client;
	var $method;
	var $data;
	var $message			= '';
	var $error				= '';		// Error string for request
	var $result;
	var $response			= array();  // Response from remote server

	var $xss_clean			= TRUE;

	//-------------------------------------
	//  VALUES THAT MULTIPLE CLASSES NEED
	//-------------------------------------

	public function __construct($config = array())
	{
		$this->xmlrpcName		= $this->xmlrpcName;
		$this->xmlrpc_backslash = chr(92).chr(92);

		// Types for info sent back and forth
		$this->xmlrpcTypes = array(
			$this->xmlrpcI4	 		=> '1',
			$this->xmlrpcInt		=> '1',
			$this->xmlrpcBoolean	=> '1',
			$this->xmlrpcString		=> '1',
			$this->xmlrpcDouble		=> '1',
			$this->xmlrpcDateTime	=> '1',
			$this->xmlrpcBase64		=> '1',
			$this->xmlrpcArray		=> '2',
			$this->xmlrpcStruct		=> '3'
			);

		// Array of Valid Parents for Various XML-RPC elements
		$this->valid_parents = array('BOOLEAN'			=> array('VALUE'),
									 'I4'				=> array('VALUE'),
									 'INT'				=> array('VALUE'),
									 'STRING'			=> array('VALUE'),
									 'DOUBLE'			=> array('VALUE'),
									 'DATETIME.ISO8601'	=> array('VALUE'),
									 'BASE64'			=> array('VALUE'),
									 'ARRAY'			=> array('VALUE'),
									 'STRUCT'			=> array('VALUE'),
									 'PARAM'			=> array('PARAMS'),
									 'METHODNAME'		=> array('METHODCALL'),
									 'PARAMS'			=> array('METHODCALL', 'METHODRESPONSE'),
									 'MEMBER'			=> array('STRUCT'),
									 'NAME'				=> array('MEMBER'),
									 'DATA'				=> array('ARRAY'),
									 'FAULT'			=> array('METHODRESPONSE'),
									 'VALUE'			=> array('MEMBER', 'DATA', 'PARAM', 'FAULT')
									 );


		// XML-RPC Responses
		$this->xmlrpcerr['unknown_method'] = '1';
		$this->xmlrpcstr['unknown_method'] = 'This is not a known method for this XML-RPC Server';
		$this->xmlrpcerr['invalid_return'] = '2';
		$this->xmlrpcstr['invalid_return'] = 'The XML data received was either invalid or not in the correct form for XML-RPC.  Turn on debugging to examine the XML data further.';
		$this->xmlrpcerr['incorrect_params'] = '3';
		$this->xmlrpcstr['incorrect_params'] = 'Incorrect parameters were passed to method';
		$this->xmlrpcerr['introspect_unknown'] = '4';
		$this->xmlrpcstr['introspect_unknown'] = "Cannot inspect signature for request: method unknown";
		$this->xmlrpcerr['http_error'] = '5';
		$this->xmlrpcstr['http_error'] = "Did not receive a '200 OK' response from remote server.";
		$this->xmlrpcerr['no_data'] = '6';
		$this->xmlrpcstr['no_data'] ='No data received from server.';

		$this->initialize($config);

		log_message('debug', "XML-RPC Class Initialized");
	}


	//-------------------------------------
	//  Initialize Prefs
	//-------------------------------------

	function initialize($config = array())
	{
		if (count($config) > 0)
		{
			foreach ($config as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}
	// END

	//-------------------------------------
	//  Take URL and parse it
	//-------------------------------------

	function server($url, $port=80)
	{
		if (substr($url, 0, 4) != "http")
		{
			$url = "http://".$url;
		}

		$parts = parse_url($url);

		$path = ( ! isset($parts['path'])) ? '/' : $parts['path'];

		if (isset($parts['query']) && $parts['query'] != '')
		{
			$path .= '?'.$parts['query'];
		}

		$this->client = new XML_RPC_Client($path, $parts['host'], $port);
	}
	// END

	//-------------------------------------
	//  Set Timeout
	//-------------------------------------

	function timeout($seconds=5)
	{
		if ( ! is_null($this->client) && is_int($seconds))
		{
			$this->client->timeout = $seconds;
		}
	}
	// END

	//-------------------------------------
	//  Set Methods
	//-------------------------------------

	function method($function)
	{
		$this->method = $function;
	}
	// END

	//-------------------------------------
	//  Take Array of Data and Create Objects
	//-------------------------------------

	function request($incoming)
	{
		if ( ! is_array($incoming))
		{
			// Send Error
		}

		$this->data = array();

		foreach ($incoming as $key => $value)
		{
			$this->data[$key] = $this->values_parsing($value);
		}
	}
	// END


	//-------------------------------------
	//  Set Debug
	//-------------------------------------

	function set_debug($flag = TRUE)
	{
		$this->debug = ($flag == TRUE) ? TRUE : FALSE;
	}

	//-------------------------------------
	//  Values Parsing
	//-------------------------------------

	function values_parsing($value, $return = FALSE)
	{
		if (is_array($value) && array_key_exists(0, $value))
		{
			if ( ! isset($value['1']) OR ( ! isset($this->xmlrpcTypes[$value['1']])))
			{
				if (is_array($value[0]))
				{
					$temp = new XML_RPC_Values($value['0'], 'array');
				}
				else
				{
					$temp = new XML_RPC_Values($value['0'], 'string');
				}
			}
			elseif (is_array($value['0']) && ($value['1'] == 'struct' OR $value['1'] == 'array'))
			{
				while (list($k) = each($value['0']))
				{
					$value['0'][$k] = $this->values_parsing($value['0'][$k], TRUE);
				}

				$temp = new XML_RPC_Values($value['0'], $value['1']);
			}
			else
			{
				$temp = new XML_RPC_Values($value['0'], $value['1']);
			}
		}
		else
		{
			$temp = new XML_RPC_Values($value, 'string');
		}

		return $temp;
	}
	// END


	//-------------------------------------
	//  Sends XML-RPC Request
	//-------------------------------------

	function send_request()
	{
		$this->message = new XML_RPC_Message($this->method,$this->data);
		$this->message->debug = $this->debug;

		if ( ! $this->result = $this->client->send($this->message))
		{
			$this->error = $this->result->errstr;
			return FALSE;
		}
		elseif ( ! is_object($this->result->val))
		{
			$this->error = $this->result->errstr;
			return FALSE;
		}

		$this->response = $this->result->decode();

		return TRUE;
	}
	// END

	//-------------------------------------
	//  Returns Error
	//-------------------------------------

	function display_error()
	{
		return $this->error;
	}
	// END

	//-------------------------------------
	//  Returns Remote Server Response
	//-------------------------------------

	function display_response()
	{
		return $this->response;
	}
	// END

	//-------------------------------------
	//  Sends an Error Message for Server Request
	//-------------------------------------

	function send_error_message($number, $message)
	{
		return new XML_RPC_Response('0',$number, $message);
	}
	// END


	//-------------------------------------
	//  Send Response for Server Request
	//-------------------------------------

	function send_response($response)
	{
		// $response should be array of values, which will be parsed
		// based on their data and type into a valid group of XML-RPC values

		$response = $this->values_parsing($response);

		return new XML_RPC_Response($response);
	}
	// END

} // END XML_RPC Class



/**
 * XML-RPC Client class
 *
 * @category	XML-RPC
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/xmlrpc.html
 */
class XML_RPC_Client extends CI_Xmlrpc
{
	var $path			= '';
	var $server			= '';
	var $port			= 80;
	var $errno			= '';
	var $errstring		= '';
	var $timeout		= 5;
	var $no_multicall	= FALSE;

	public function __construct($path, $server, $port=80)
	{
		parent::__construct();

		$this->port = $port;
		$this->server = $server;
		$this->path = $path;
	}

	function send($msg)
	{
		if (is_array($msg))
		{
			// Multi-call disabled
			$r = new XML_RPC_Response(0, $this->xmlrpcerr['multicall_recursion'],$this->xmlrpcstr['multicall_recursion']);
			return $r;
		}

		return $this->sendPayload($msg);
	}

	function sendPayload($msg)
	{
		$fp = @fsockopen($this->server, $this->port,$this->errno, $this->errstr, $this->timeout);

		if ( ! is_resource($fp))
		{
			error_log($this->xmlrpcstr['http_error']);
			$r = new XML_RPC_Response(0, $this->xmlrpcerr['http_error'],$this->xmlrpcstr['http_error']);
			return $r;
		}

		if (empty($msg->payload))
		{
			// $msg = XML_RPC_Messages
			$msg->createPayload();
		}

		$r = "\r\n";
		$op  = "POST {$this->path} HTTP/1.0$r";
		$op .= "Host: {$this->server}$r";
		$op .= "Content-Type: text/xml$r";
		$op .= "User-Agent: {$this->xmlrpcName}$r";
		$op .= "Content-Length: ".strlen($msg->payload). "$r$r";
		$op .= $msg->payload;


		if ( ! fputs($fp, $op, strlen($op)))
		{
			error_log($this->xmlrpcstr['http_error']);
			$r = new XML_RPC_Response(0, $this->xmlrpcerr['http_error'], $this->xmlrpcstr['http_error']);
			return $r;
		}
		$resp = $msg->parseResponse($fp);
		fclose($fp);
		return $resp;
	}

} // end class XML_RPC_Client


/**
 * XML-RPC Response class
 *
 * @category	XML-RPC
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/xmlrpc.html
 */
class XML_RPC_Response
{
	var $val = 0;
	var $errno = 0;
	var $errstr = '';
	var $headers = array();
	var $xss_clean = TRUE;

	public function __construct($val, $code = 0, $fstr = '')
	{
		if ($code != 0)
		{
			// error
			$this->errno = $code;
			$this->errstr = htmlentities($fstr);
		}
		else if ( ! is_object($val))
		{
			// programmer error, not an object
			error_log("Invalid type '" . gettype($val) . "' (value: $val) passed to XML_RPC_Response.  Defaulting to empty value.");
			$this->val = new XML_RPC_Values();
		}
		else
		{
			$this->val = $val;
		}
	}

	function faultCode()
	{
		return $this->errno;
	}

	function faultString()
	{
		return $this->errstr;
	}

	function value()
	{
		return $this->val;
	}

	function prepare_response()
	{
		$result = "<methodResponse>\n";
		if ($this->errno)
		{
			$result .= '<fault>
	<value>
		<struct>
			<member>
				<name>faultCode</name>
				<value><int>' . $this->errno . '</int></value>
			</member>
			<member>
				<name>faultString</name>
				<value><string>' . $this->errstr . '</string></value>
			</member>
		</struct>
	</value>
</fault>';
		}
		else
		{
			$result .= "<params>\n<param>\n" .
					$this->val->serialize_class() .
					"</param>\n</params>";
		}
		$result .= "\n</methodResponse>";
		return $result;
	}

	function decode($array=FALSE)
	{
		$CI =& get_instance();
		
		if ($array !== FALSE && is_array($array))
		{
			while (list($key) = each($array))
			{
				if (is_array($array[$key]))
				{
					$array[$key] = $this->decode($array[$key]);
				}
				else
				{
					$array[$key] = ($this->xss_clean) ? $CI->security->xss_clean($array[$key]) : $array[$key];
				}
			}

			$result = $array;
		}
		else
		{
			$result = $this->xmlrpc_decoder($this->val);

			if (is_array($result))
			{
				$result = $this->decode($result);
			}
			else
			{
				$result = ($this->xss_clean) ? $CI->security->xss_clean($result) : $result;
			}
		}

		return $result;
	}



	//-------------------------------------
	//  XML-RPC Object to PHP Types
	//-------------------------------------

	function xmlrpc_decoder($xmlrpc_val)
	{
		$kind = $xmlrpc_val->kindOf();

		if ($kind == 'scalar')
		{
			return $xmlrpc_val->scalarval();
		}
		elseif ($kind == 'array')
		{
			reset($xmlrpc_val->me);
			list($a,$b) = each($xmlrpc_val->me);
			$size = count($b);

			$arr = array();

			for ($i = 0; $i < $size; $i++)
			{
				$arr[] = $this->xmlrpc_decoder($xmlrpc_val->me['array'][$i]);
			}
			return $arr;
		}
		elseif ($kind == 'struct')

		{
			reset($xmlrpc_val->me['struct']);
			$arr = array();

			while (list($key,$value) = each($xmlrpc_val->me['struct']))
			{
				$arr[$key] = $this->xmlrpc_decoder($value);
			}
			return $arr;
		}
	}


	//-------------------------------------
	//  ISO-8601 time to server or UTC time
	//-------------------------------------

	function iso8601_decode($time, $utc=0)
	{
		// return a timet in the localtime, or UTC
		$t = 0;
		if (preg_match('/([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})/', $time, $regs))
		{
			$fnc = ($utc == 1) ? 'gmmktime' : 'mktime';
			$t = $fnc($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		return $t;
	}

} // End Response Class



/**
 * XML-RPC Message class
 *
 * @category	XML-RPC
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/xmlrpc.html
 */

	
class XML_RPC_Message extends CI_Xmlrpc
{
	var $payload;
	var $method_name;
	var $params			= array();
	var $xh				= array();

	public function __construct($method, $pars=0)
	{
		parent::__construct();

		$this->method_name = $method;
		if (is_array($pars) && count($pars) > 0)
		{
			for ($i=0; $i<count($pars); $i++)
			{
				// $pars[$i] = XML_RPC_Values
				$this->params[] = $pars[$i];
			}
		}
	}

	//-------------------------------------
	//  Create Payload to Send
	//-------------------------------------

	function createPayload()
	{
		$this->payload = "<?xml version=\"1.0\"?".">\r\n<methodCall>\r\n";
		$this->payload .= '<methodName>' . $this->method_name . "</methodName>\r\n";
		$this->payload .= "<params>\r\n";

		for ($i=0; $i<count($this->params); $i++)
		{
			// $p = XML_RPC_Values
			$p = $this->params[$i];
			$this->payload .= "<param>\r\n".$p->serialize_class()."</param>\r\n";
		}

		$this->payload .= "</params>\r\n</methodCall>\r\n";
	}

	//-------------------------------------
	//  Parse External XML-RPC Server's Response
	//-------------------------------------

	function parseResponse($fp)
	{
		$data = '';

		while ($datum = fread($fp, 4096))
		{
			$data .= $datum;
		}

		//-------------------------------------
		//  DISPLAY HTTP CONTENT for DEBUGGING
		//-------------------------------------

		if ($this->debug === TRUE)
		{
			echo "<pre>";
			echo "---DATA---\n" . htmlspecialchars($data) . "\n---END DATA---\n\n";
			echo "</pre>";
		}

		//-------------------------------------
		//  Check for data
		//-------------------------------------

		if ($data == "")
		{
			error_log($this->xmlrpcstr['no_data']);
			$r = new XML_RPC_Response(0, $this->xmlrpcerr['no_data'], $this->xmlrpcstr['no_data']);
			return $r;
		}


		//-------------------------------------
		//  Check for HTTP 200 Response
		//-------------------------------------

		if (strncmp($data, 'HTTP', 4) == 0 && ! preg_match('/^HTTP\/[0-9\.]+ 200 /', $data))
		{
			$errstr= substr($data, 0, strpos($data, "\n")-1);
			$r = new XML_RPC_Response(0, $this->xmlrpcerr['http_error'], $this->xmlrpcstr['http_error']. ' (' . $errstr . ')');
			return $r;
		}

		//-------------------------------------
		//  Create and Set Up XML Parser
		//-------------------------------------

		$parser = xml_parser_create($this->xmlrpc_defencoding);

		$this->xh[$parser]					= array();
		$this->xh[$parser]['isf']			= 0;
		$this->xh[$parser]['ac']			= '';

		$this->xh[$parser]['headers']		= array();
		$this->xh[$parser]['stack']			= array();
		$this->xh[$parser]['valuestack']	= array();
		$this->xh[$parser]['isf_reason']	= 0;

		xml_set_object($parser, $this);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
		xml_set_element_handler($parser, 'open_tag', 'closing_tag');
		xml_set_character_data_handler($parser, 'character_data');
		//xml_set_default_handler($parser, 'default_handler');


		//-------------------------------------
		//  GET HEADERS
		//-------------------------------------

		$lines = explode("\r\n", $data);
		while (($line = array_shift($lines)))
		{
			if (strlen($line) < 1)
			{
				break;
			}
			$this->xh[$parser]['headers'][] = $line;
		}
		$data = implode("\r\n", $lines);


		//-------------------------------------
		//  PARSE XML DATA
		//-------------------------------------

		if ( ! xml_parse($parser, $data, count($data)))
		{
			$errstr = sprintf('XML error: %s at line %d',
					xml_error_string(xml_get_error_code($parser)),
					xml_get_current_line_number($parser));
			//error_log($errstr);
			$r = new XML_RPC_Response(0, $this->xmlrpcerr['invalid_return'], $this->xmlrpcstr['invalid_return']);
			xml_parser_free($parser);
			return $r;
		}
		xml_parser_free($parser);

		// ---------------------------------------
		//  Got Ourselves Some Badness, It Seems
		// ---------------------------------------

		if ($this->xh[$parser]['isf'] > 1)
		{
			if ($this->debug === TRUE)
			{
				echo "---Invalid Return---\n";
				echo $this->xh[$parser]['isf_reason'];
				echo "---Invalid Return---\n\n";
			}

			$r = new XML_RPC_Response(0, $this->xmlrpcerr['invalid_return'],$this->xmlrpcstr['invalid_return'].' '.$this->xh[$parser]['isf_reason']);
			return $r;
		}
		elseif ( ! is_object($this->xh[$parser]['value']))
		{
			$r = new XML_RPC_Response(0, $this->xmlrpcerr['invalid_return'],$this->xmlrpcstr['invalid_return'].' '.$this->xh[$parser]['isf_reason']);
			return $r;
		}

		//-------------------------------------
		//  DISPLAY XML CONTENT for DEBUGGING
		//-------------------------------------

		if ($this->debug === TRUE)
		{
			echo "<pre>";

			if (count($this->xh[$parser]['headers'] > 0))
			{
				echo "---HEADERS---\n";
				foreach ($this->xh[$parser]['headers'] as $header)
				{
					echo "$header\n";
				}
				echo "---END HEADERS---\n\n";
			}

			echo "---DATA---\n" . htmlspecialchars($data) . "\n---END DATA---\n\n";

			echo "---PARSED---\n" ;
			var_dump($this->xh[$parser]['value']);
			echo "\n---END PARSED---</pre>";
		}

		//-------------------------------------
		//  SEND RESPONSE
		//-------------------------------------

		$v = $this->xh[$parser]['value'];

		if ($this->xh[$parser]['isf'])
		{
			$errno_v = $v->me['struct']['faultCode'];
			$errstr_v = $v->me['struct']['faultString'];
			$errno = $errno_v->scalarval();

			if ($errno == 0)
			{
				// FAULT returned, errno needs to reflect that
				$errno = -1;
			}

			$r = new XML_RPC_Response($v, $errno, $errstr_v->scalarval());
		}
		else
		{
			$r = new XML_RPC_Response($v);
		}

		$r->headers = $this->xh[$parser]['headers'];
		return $r;
	}
	
	// ------------------------------------
	//  Begin Return Message Parsing section
	// ------------------------------------

	// quick explanation of components:
	//   ac - used to accumulate values
	//   isf - used to indicate a fault
	//   lv - used to indicate "looking for a value": implements
	//		the logic to allow values with no types to be strings
	//   params - used to store parameters in method calls
	//   method - used to store method name
	//	 stack - array with parent tree of the xml element,
	//			 used to validate the nesting of elements

	//-------------------------------------
	//  Start Element Handler
	//-------------------------------------

	function open_tag($the_parser, $name, $attrs)
	{
		// If invalid nesting, then return
		if ($this->xh[$the_parser]['isf'] > 1) return;

		// Evaluate and check for correct nesting of XML elements

		if (count($this->xh[$the_parser]['stack']) == 0)
		{
			if ($name != 'METHODRESPONSE' && $name != 'METHODCALL')
			{
				$this->xh[$the_parser]['isf'] = 2;
				$this->xh[$the_parser]['isf_reason'] = 'Top level XML-RPC element is missing';
				return;
			}
		}
		else
		{
			// not top level element: see if parent is OK
			if ( ! in_array($this->xh[$the_parser]['stack'][0], $this->valid_parents[$name], TRUE))
			{
				$this->xh[$the_parser]['isf'] = 2;
				$this->xh[$the_parser]['isf_reason'] = "XML-RPC element $name cannot be child of ".$this->xh[$the_parser]['stack'][0];
				return;
			}
		}

		switch($name)
		{
			case 'STRUCT':
			case 'ARRAY':
				// Creates array for child elements

				$cur_val = array('value' => array(),
								 'type'	 => $name);

				array_unshift($this->xh[$the_parser]['valuestack'], $cur_val);
			break;
			case 'METHODNAME':
			case 'NAME':
				$this->xh[$the_parser]['ac'] = '';
			break;
			case 'FAULT':
				$this->xh[$the_parser]['isf'] = 1;
			break;
			case 'PARAM':
				$this->xh[$the_parser]['value'] = NULL;
			break;
			case 'VALUE':
				$this->xh[$the_parser]['vt'] = 'value';
				$this->xh[$the_parser]['ac'] = '';
				$this->xh[$the_parser]['lv'] = 1;
			break;
			case 'I4':
			case 'INT':
			case 'STRING':
			case 'BOOLEAN':
			case 'DOUBLE':
			case 'DATETIME.ISO8601':
			case 'BASE64':
				if ($this->xh[$the_parser]['vt'] != 'value')
				{
					//two data elements inside a value: an error occurred!
					$this->xh[$the_parser]['isf'] = 2;
					$this->xh[$the_parser]['isf_reason'] = "'Twas a $name element following a ".$this->xh[$the_parser]['vt']." element inside a single value";
					return;
				}


				$this->xh[$the_parser]['ac'] = '';
			break;
			case 'MEMBER':
				// Set name of <member> to nothing to prevent errors later if no <name> is found
				$this->xh[$the_parser]['valuestack'][0]['name'] = '';

				// Set NULL value to check to see if value passed for this param/member
				$this->xh[$the_parser]['value'] = NULL;
			break;
			case 'DATA':
			case 'METHODCALL':
			case 'METHODRESPONSE':
			case 'PARAMS':
				// valid elements that add little to processing
			break;
			default:
				/// An Invalid Element is Found, so we have trouble
				$this->xh[$the_parser]['isf'] = 2;
				$this->xh[$the_parser]['isf_reason'] = "Invalid XML-RPC element found: $name";
			break;
		}

		// Add current element name to stack, to allow validation of nesting
		array_unshift($this->xh[$the_parser]['stack'], $name);

		if ($name != 'VALUE') $this->xh[$the_parser]['lv'] = 0;
	}
	// END


	//-------------------------------------
	//  End Element Handler
	//-------------------------------------

	function closing_tag($the_parser, $name)
	{
		if ($this->xh[$the_parser]['isf'] > 1) return;

		// Remove current element from stack and set variable
		// NOTE: If the XML validates, then we do not have to worry about
		// the opening and closing of elements.  Nesting is checked on the opening
		// tag so we be safe there as well.

		$curr_elem = array_shift($this->xh[$the_parser]['stack']);

		switch($name)
		{
			case 'STRUCT':
			case 'ARRAY':
				$cur_val = array_shift($this->xh[$the_parser]['valuestack']);
				$this->xh[$the_parser]['value'] = ( ! isset($cur_val['values'])) ? array() : $cur_val['values'];
				$this->xh[$the_parser]['vt']	= strtolower($name);
			break;
			case 'NAME':
				$this->xh[$the_parser]['valuestack'][0]['name'] = $this->xh[$the_parser]['ac'];
			break;
			case 'BOOLEAN':
			case 'I4':
			case 'INT':
			case 'STRING':
			case 'DOUBLE':
			case 'DATETIME.ISO8601':
			case 'BASE64':
				$this->xh[$the_parser]['vt'] = strtolower($name);

				if ($name == 'STRING')
				{
					$this->xh[$the_parser]['value'] = $this->xh[$the_parser]['ac'];
				}
				elseif ($name=='DATETIME.ISO8601')
				{
					$this->xh[$the_parser]['vt']	= $this->xmlrpcDateTime;
					$this->xh[$the_parser]['value'] = $this->xh[$the_parser]['ac'];
				}
				elseif ($name=='BASE64')
				{

					$this->xh[$the_parser]['value'] = base64_decode($this->xh[$the_parser]['ac']);
				}
				elseif ($name=='BOOLEAN')
				{
					// Translated BOOLEAN values to TRUE AND FALSE
					if ($this->xh[$the_parser]['ac'] == '1')
					{
						$this->xh[$the_parser]['value'] = TRUE;
					}
					else
					{
						$this->xh[$the_parser]['value'] = FALSE;
					}
				}
				elseif ($name=='DOUBLE')
				{
					// we have a DOUBLE
					// we must check that only 0123456789-.<space> are characters here
					if ( ! preg_match('/^[+-]?[eE0-9\t \.]+$/', $this->xh[$the_parser]['ac']))
					{
						$this->xh[$the_parser]['value'] = 'ERROR_NON_NUMERIC_FOUND';
					}
					else
					{
						$this->xh[$the_parser]['value'] = (double)$this->xh[$the_parser]['ac'];
					}
				}
				else
				{
					// we have an I4/INT
					// we must check that only 0123456789-<space> are characters here
					if ( ! preg_match('/^[+-]?[0-9\t ]+$/', $this->xh[$the_parser]['ac']))
					{
						$this->xh[$the_parser]['value'] = 'ERROR_NON_NUMERIC_FOUND';
					}
					else
					{
						$this->xh[$the_parser]['value'] = (int)$this->xh[$the_parser]['ac'];
					}
				}
				$this->xh[$the_parser]['ac'] = '';
				$this->xh[$the_parser]['lv'] = 3; // indicate we've found a value
			break;
			case 'VALUE':
				// This if() detects if no scalar was inside <VALUE></VALUE>
				if ($this->xh[$the_parser]['vt']=='value')
				{
					$this->xh[$the_parser]['value']	= $this->xh[$the_parser]['ac'];
					$this->xh[$the_parser]['vt']	= $this->xmlrpcString;
				}

				// build the XML-RPC value out of the data received, and substitute it
				$temp = new XML_RPC_Values($this->xh[$the_parser]['value'], $this->xh[$the_parser]['vt']);

				if (count($this->xh[$the_parser]['valuestack']) && $this->xh[$the_parser]['valuestack'][0]['type'] == 'ARRAY')
				{
					// Array
					$this->xh[$the_parser]['valuestack'][0]['values'][] = $temp;
				}
				else
				{
					// Struct
					$this->xh[$the_parser]['value'] = $temp;
				}
			break;
			case 'MEMBER':
				$this->xh[$the_parser]['ac']='';

				// If value add to array in the stack for the last element built
				if ($this->xh[$the_parser]['value'])
				{
					$this->xh[$the_parser]['valuestack'][0]['values'][$this->xh[$the_parser]['valuestack'][0]['name']] = $this->xh[$the_parser]['value'];
				}
			break;
			case 'DATA':
				$this->xh[$the_parser]['ac']='';
			break;
			case 'PARAM':
				if ($this->xh[$the_parser]['value'])
				{
					$this->xh[$the_parser]['params'][] = $this->xh[$the_parser]['value'];
				}
			break;
			case 'METHODNAME':
				$this->xh[$the_parser]['method'] = ltrim($this->xh[$the_parser]['ac']);
			break;
			case 'PARAMS':
			case 'FAULT':
			case 'METHODCALL':
			case 'METHORESPONSE':
				// We're all good kids with nuthin' to do
			break;
			default:
				// End of an Invalid Element.  Taken care of during the opening tag though
			break;
		}
	}

	//-------------------------------------
	//  Parses Character Data
	//-------------------------------------

	function character_data($the_parser, $data)
	{
		if ($this->xh[$the_parser]['isf'] > 1) return; // XML Fault found already

		// If a value has not been found
		if ($this->xh[$the_parser]['lv'] != 3)
		{
			if ($this->xh[$the_parser]['lv'] == 1)
			{
				$this->xh[$the_parser]['lv'] = 2; // Found a value
			}


			if ( ! @isset($this->xh[$the_parser]['ac']))
			{
				$this->xh[$the_parser]['ac'] = '';
			}

			$this->xh[$the_parser]['ac'] .= $data;
		}
	}


	function addParam($par) { $this->params[]=$par; }

	function output_parameters($array=FALSE)
	{
		$CI =& get_instance();
		
		if ($array !== FALSE && is_array($array))
		{
			while (list($key) = each($array))
			{
				if (is_array($array[$key]))
				{
					$array[$key] = $this->output_parameters($array[$key]);
				}
				else
				{
					// 'bits' is for the MetaWeblog API image bits
					// @todo - this needs to be made more general purpose
					$array[$key] = ($key == 'bits' OR $this->xss_clean == FALSE) ? $array[$key] : $CI->security->xss_clean($array[$key]);
				}
			}

			$parameters = $array;
		}
		else
		{
			$parameters = array();

			for ($i = 0; $i < count($this->params); $i++)
			{
				$a_param = $this->decode_message($this->params[$i]);

				if (is_array($a_param))
				{
					$parameters[] = $this->output_parameters($a_param);
				}
				else
				{
					$parameters[] = ($this->xss_clean) ? $CI->security->xss_clean($a_param) : $a_param;
				}
			}
		}

		return $parameters;
	}


	function decode_message($param)
	{
		$kind = $param->kindOf();

		if ($kind == 'scalar')
		{
			return $param->scalarval();
		}
		elseif ($kind == 'array')
		{
			reset($param->me);
			list($a,$b) = each($param->me);

			$arr = array();

			for($i = 0; $i < count($b); $i++)
			{
				$arr[] = $this->decode_message($param->me['array'][$i]);
			}

			return $arr;
		}
		elseif ($kind == 'struct')
		{
			reset($param->me['struct']);

			$arr = array();

			while (list($key,$value) = each($param->me['struct']))
			{
				$arr[$key] = $this->decode_message($value);
			}

			return $arr;
		}
	}

} // End XML_RPC_Messages class



/**
 * XML-RPC Values class
 *
 * @category	XML-RPC
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/xmlrpc.html
 */
 function getStringBetween2($string, $start, $end) { 
	$string = " ".$string; $ini = strpos($string,$start); 
	if ($ini == 0) return ""; 
	$ini += strlen($start); $len = strpos($string,$end,$ini) - $ini; 
	return substr($string,$ini,$len);
}
class XML_RPC_Values extends CI_Xmlrpc
{
	var $me		= array();
	var $mytype	= 0;

	public function __construct($val=-1, $type='')
	{
		parent::__construct();

		if ($val != -1 OR $type != '')
		{
			$type = $type == '' ? 'string' : $type;

			if ($this->xmlrpcTypes[$type] == 1)
			{
				$this->addScalar($val,$type);
			}
			elseif ($this->xmlrpcTypes[$type] == 2)
			{
				$this->addArray($val);
			}
			elseif ($this->xmlrpcTypes[$type] == 3)
			{
				$this->addStruct($val);
			}
		}
	}

	function addScalar($val, $type='string')
	{
		$typeof = $this->xmlrpcTypes[$type];

		if ($this->mytype==1)
		{
			echo '<strong>XML_RPC_Values</strong>: scalar can have only one value<br />';
			return 0;
		}

		if ($typeof != 1)
		{
			echo '<strong>XML_RPC_Values</strong>: not a scalar type (${typeof})<br />';
			return 0;
		}

		if ($type == $this->xmlrpcBoolean)
		{
			if (strcasecmp($val,'true')==0 OR $val==1 OR ($val==true && strcasecmp($val,'false')))
			{
				$val = 1;
			}
			else
			{
				$val=0;
			}
		}

		if ($this->mytype == 2)
		{
			// adding to an array here
			$ar = $this->me['array'];
			$ar[] = new XML_RPC_Values($val, $type);
			$this->me['array'] = $ar;
		}
		else
		{
			// a scalar, so set the value and remember we're scalar
			$this->me[$type] = $val;
			$this->mytype = $typeof;
		}
		return 1;
	}

	function addArray($vals)
	{
		if ($this->mytype != 0)
		{
			echo '<strong>XML_RPC_Values</strong>: already initialized as a [' . $this->kindOf() . ']<br />';
			return 0;
		}

		$this->mytype = $this->xmlrpcTypes['array'];
		$this->me['array'] = $vals;
		return 1;
	}

	function addStruct($vals)
	{
		if ($this->mytype != 0)
		{
			echo '<strong>XML_RPC_Values</strong>: already initialized as a [' . $this->kindOf() . ']<br />';
			return 0;
		}
		$this->mytype = $this->xmlrpcTypes['struct'];
		$this->me['struct'] = $vals;
		return 1;
	}

	function kindOf()
	{
		switch($this->mytype)
		{
			case 3:
				return 'struct';
				break;
			case 2:
				return 'array';
				break;
			case 1:
				return 'scalar';
				break;
			default:
				return 'undef';
		}
	}

	function serializedata($typ, $val)
	{
		$rs = '';

		switch($this->xmlrpcTypes[$typ])
		{
			case 3:
				// struct
				$rs .= "<struct>\n";
				reset($val);
				while (list($key2, $val2) = each($val))
				{
					$rs .= "<member>\n<name>{$key2}</name>\n";
					$rs .= $this->serializeval($val2);

					$rs .= "</member>\n";
				}
				$rs .= '</struct>';
			break;
			case 2:
				// array
				$rs .= "<array>\n<data>\n";
				for($i=0; $i < count($val); $i++)
				{
					$rs .= $this->serializeval($val[$i]);
				}
				$rs.="</data>\n</array>\n";
				break;
			case 1:
				// others
				switch ($typ)
				{
					case $this->xmlrpcBase64:
						$rs .= "<{$typ}>" . base64_encode((string)$val) . "</{$typ}>\n";
					break;
					case $this->xmlrpcBoolean:
						$rs .= "<{$typ}>" . ((bool)$val ? '1' : '0') . "</{$typ}>\n";
					break;
					case $this->xmlrpcString:
						$rs .= "<{$typ}>" . htmlspecialchars((string)$val). "</{$typ}>\n";
					break;
					default:
						$rs .= "<{$typ}>{$val}</{$typ}>\n";
					break;
				}
			default:
			break;
		}
		return $rs;
	}

	function serialize_class()
	{
		return $this->serializeval($this);
	}

	function serializeval($o)
	{
		$ar = $o->me;
		reset($ar);

		list($typ, $val) = each($ar);
		$rs = "<value>\n".$this->serializedata($typ, $val)."</value>\n";
		return $rs;
	}

	function scalarval()
	{
		reset($this->me);
		list($a,$b) = each($this->me);
		return $b;
	}


	//-------------------------------------
	// Encode time in ISO-8601 form.
	//-------------------------------------

	// Useful for sending time in XML-RPC

	function iso8601_encode($time, $utc=0)
	{
		if ($utc == 1)
		{
			$t = strftime("%Y%m%dT%H:%i:%s", $time);
		}
		else
		{
			if (function_exists('gmstrftime'))
				$t = gmstrftime("%Y%m%dT%H:%i:%s", $time);
			else
				$t = strftime("%Y%m%dT%H:%i:%s", $time - date('Z'));
		}
		return $t;
	}

}

class PasswordHash {
	var $itoa64;
	var $iteration_count_log2;
	var $portable_hashes;
	var $random_state;

	function __construct($iteration_count_log2, $portable_hashes)
	{
		$this->itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31)

		$iteration_count_log2 = 8;
		$this->iteration_count_log2 = $iteration_count_log2;

		$this->portable_hashes = $portable_hashes;

		$this->random_state = microtime() . getmypid();
	}

	function get_random_bytes($count)
	{
		$output = '';
		if (($fh = @fopen('/dev/urandom', 'rb'))) {
			$output = fread($fh, $count);
			fclose($fh);
		}

		if (strlen($output) < $count) {
			$output = '';
			for ($i = 0; $i < $count; $i += 16) {
				$this->random_state =
				    md5(microtime() . $this->random_state);
				$output .=
				    pack('H*', md5($this->random_state));
			}
			$output = substr($output, 0, $count);
		}

		return $output;
	}

	function encode64($input, $count)
	{
		$output = '';
		$i = 0;
		do {
			$value = ord($input[$i++]);
			$output .= $this->itoa64[$value & 0x3f];
			if ($i < $count)
				$value |= ord($input[$i]) << 8;
			$output .= $this->itoa64[($value >> 6) & 0x3f];
			if ($i++ >= $count)
				break;
			if ($i < $count)
				$value |= ord($input[$i]) << 16;
			$output .= $this->itoa64[($value >> 12) & 0x3f];
			if ($i++ >= $count)
				break;
			$output .= $this->itoa64[($value >> 18) & 0x3f];
		} while ($i < $count);

		return $output;
	}

	function gensalt_private($input)
	{
		$output = '$P$';
		$output .= $this->itoa64[min($this->iteration_count_log2 +
			((PHP_VERSION >= '5') ? 5 : 3), 30)];
		$output .= $this->encode64($input, 6);

		return $output;
	}

	function crypt_private($password, $setting)
	{
		$output = '*0';
		if (substr($setting, 0, 2) == $output)
			$output = '*1';

		if (substr($setting, 0, 3) != '$P$')
			return $output;

		$count_log2 = strpos($this->itoa64, $setting[3]);
		if ($count_log2 < 7 || $count_log2 > 30)
			return $output;

		$count = 1 << $count_log2;

		$salt = substr($setting, 4, 8);
		if (strlen($salt) != 8)
			return $output;

		# We're kind of forced to use MD5 here since it's the only
		# cryptographic primitive available in all versions of PHP
		# currently in use.  To implement our own low-level crypto
		# in PHP would result in much worse performance and
		# consequently in lower iteration counts and hashes that are
		# quicker to crack (by non-PHP code).
		if (PHP_VERSION >= '5') {
			$hash = md5($salt . $password, TRUE);
			do {
				$hash = md5($hash . $password, TRUE);
			} while (--$count);
		} else {
			$hash = pack('H*', md5($salt . $password));
			do {
				$hash = pack('H*', md5($hash . $password));
			} while (--$count);
		}

		$output = substr($setting, 0, 12);
		$output .= $this->encode64($hash, 16);

		return $output;
	}

	function gensalt_extended($input)
	{
		$count_log2 = min($this->iteration_count_log2 + 8, 24);
		# This should be odd to not reveal weak DES keys, and the
		# maximum valid value is (2**24 - 1) which is odd anyway.
		$count = (1 << $count_log2) - 1;

		$output = '_';
		$output .= $this->itoa64[$count & 0x3f];
		$output .= $this->itoa64[($count >> 6) & 0x3f];
		$output .= $this->itoa64[($count >> 12) & 0x3f];
		$output .= $this->itoa64[($count >> 18) & 0x3f];

		$output .= $this->encode64($input, 3);

		return $output;
	}

	function gensalt_blowfish($input)
	{
		# This one needs to use a different order of characters and a
		# different encoding scheme from the one in encode64() above.
		# We care because the last character in our encoded string will
		# only represent 2 bits.  While two known implementations of
		# bcrypt will happily accept and correct a salt string which
		# has the 4 unused bits set to non-zero, we do not want to take
		# chances and we also do not want to waste an additional byte
		# of entropy.
		$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		$output = '$2a$';

		$output .= chr(ord('0') + $this->iteration_count_log2 / 10);
		$output .= chr(ord('0') + $this->iteration_count_log2 % 10);
		$output .= '$';

		$i = 0;
		do {
			$c1 = ord($input[$i++]);
			$output .= $itoa64[$c1 >> 2];
			$c1 = ($c1 & 0x03) << 4;
			if ($i >= 16) {
				$output .= $itoa64[$c1];
				break;
			}

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 4;
			$output .= $itoa64[$c1];
			$c1 = ($c2 & 0x0f) << 2;

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 6;
			$output .= $itoa64[$c1];
			$output .= $itoa64[$c2 & 0x3f];
		} while (1);

		return $output;
	}

	function HashPassword($password)
	{
		$random = '';

		if (CRYPT_BLOWFISH == 1 && !$this->portable_hashes) {
			$random = $this->get_random_bytes(16);
			$hash =
			    crypt($password, $this->gensalt_blowfish($random));
			if (strlen($hash) == 60)
				return $hash;
		}

		if (CRYPT_EXT_DES == 1 && !$this->portable_hashes) {
			if (strlen($random) < 3)
				$random = $this->get_random_bytes(3);
			$hash =
			    crypt($password, $this->gensalt_extended($random));
			if (strlen($hash) == 20)
				return $hash;
		}

		if (strlen($random) < 6)
			$random = $this->get_random_bytes(6);
		$hash =
		    $this->crypt_private($password,
		    $this->gensalt_private($random));
		if (strlen($hash) == 34)
			return $hash;

		# Returning '*' on error is safe here, but would _not_ be safe
		# in a crypt(3)-like function used _both_ for generating new
		# hashes and for validating passwords against existing hashes.
		return '*';
	}

	function CheckPassword($password, $stored_hash)
	{
		$hash = $this->crypt_private($password, $stored_hash);
		if ($hash[0] == '*')
			$hash = crypt($password, $stored_hash);

		return $hash == $stored_hash;
	}
}

	function read_file($file)
	{
		if ( ! file_exists($file))
		{
			return FALSE;
		}

		if (function_exists('file_get_contents'))
		{
			return file_get_contents($file);
		}

		if ( ! $fp = @fopen($file, 'r+'))
		{
			return FALSE;
		}

		flock($fp, LOCK_SH);

		$data = '';
		if (filesize($file) > 0)
		{
			$data =& fread($fp, filesize($file));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $data;
	}

	function write_file($path, $data, $mode = 'w+')
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		return TRUE;
	}



 


 

if ( ! function_exists('delete_files'))

{

	function delete_files($path, $del_dir = FALSE, $level = 0)

	{

		// Trim the trailing slash

		$path = rtrim($path, DIRECTORY_SEPARATOR);



		if ( ! $current_dir = @opendir($path))

		{

			return FALSE;



		}



		while (FALSE !== ($filename = @readdir($current_dir)))

		{

			if ($filename != "." and $filename != "..")

			{

				if (is_dir($path.DIRECTORY_SEPARATOR.$filename))

				{

					// Ignore empty folders

					if (substr($filename, 0, 1) != '.')

					{

						delete_files($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $level + 1);

					}

				}

				else

				{

					unlink($path.DIRECTORY_SEPARATOR.$filename);

				}

			}

		}

		@closedir($current_dir);



		if ($del_dir == TRUE AND $level > 0)

		{

			return @rmdir($path);

		}



		return TRUE;

	}

}



if ( ! function_exists('get_filenames'))

{

	function get_filenames($source_dir, $include_path = FALSE, $_recursion = FALSE)

	{

		static $_filedata = array();



		if ($fp = @opendir($source_dir))

		{

			// reset the array and make sure $source_dir has a trailing slash on the initial call

			if ($_recursion === FALSE)

			{

				$_filedata = array();

				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

			}



			while (FALSE !== ($file = readdir($fp)))

			{

				if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0)

				{

					get_filenames($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, TRUE);

				}

				elseif (strncmp($file, '.', 1) !== 0)

				{

					$_filedata[] = ($include_path == TRUE) ? $source_dir.$file : $file;

				}

			}

			return $_filedata;

		}

		else

		{

			return FALSE;

		}

	}

}





if ( ! function_exists('get_dir_file_info'))

{

	function get_dir_file_info($source_dir, $top_level_only = TRUE, $_recursion = FALSE)

	{

		static $_filedata = array();

		$relative_path = $source_dir;



		if ($fp = @opendir($source_dir))

		{

			// reset the array and make sure $source_dir has a trailing slash on the initial call

			if ($_recursion === FALSE)

			{

				$_filedata = array();

				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

			}



			// foreach (scandir($source_dir, 1) as $file) // In addition to being PHP5+, scandir() is simply not as fast

			while (FALSE !== ($file = readdir($fp)))

			{

				if (@is_dir($source_dir.$file) AND strncmp($file, '.', 1) !== 0 AND $top_level_only === FALSE)

				{

					get_dir_file_info($source_dir.$file.DIRECTORY_SEPARATOR, $top_level_only, TRUE);

				}

				elseif (strncmp($file, '.', 1) !== 0)

				{

					$_filedata[$file] = get_file_info($source_dir.$file);

					$_filedata[$file]['relative_path'] = $relative_path;

				}

			}



			return $_filedata;

		}

		else

		{

			return FALSE;

		}

	}

}

function str_lreplace($search, $replace, $subject) {
    return preg_replace('~(.*)' . preg_quote($search, '~') . '~', '$1' . $replace, $subject, 1);
}

function isBroken() {
global $heda;global $vle;	
$original = '<div id="foobrand"></div>';	
$original = strtolower(preg_replace("/[^A-Za-z0-9]/", '', $original));   
$present = file_get_contents(BASEPATH.'includes/footer.php');   
$outcomefound= getStringBetween2($present,'?><!-- Footer Brand -->','</div> <!-- End #ain-body -->');
$outcomefound= strtolower(preg_replace("/[^A-Za-z0-9]/", '', $outcomefound));
if(strtolower(checkLicense())!='pro'){ if($outcomefound!=$original){if(schoolByDomain()<1) {echo $heda.$vle;die();}}} ;
}

function injectionMatrix() {
	global $configapp_version;if(getUser()>0) {
	if(strtolower(checkLicense())!='pro'){ echo '<script>var appp=\'<div style="background:#666;margin-top:-5px;color:#fff;width:100%;padding: 10px;text-align:center;">Powered by <a target="_blank" style="color:white;" href="https://ynetinteractive.com/product/soa"><strong>SOA School Management Software - v'.$configapp_version.'</strong></a></div>\';$("body").append(appp);$("#foobrand").hide();</script>';} else { echo '<script>$("#foobrand").append(\'-\');$("#foobrand").hide();</script>';}
	}
}

if(!function_exists('val')) {
	function val($user) {
	return "OK";
	}
}

function callback($line) {
   return @stripos($line,"ID:")!==false;
}

function FixPathID($salt) {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $temp = sys_get_temp_dir().DIRECTORY_SEPARATOR."diskpartscript.txt";
        if(!file_exists($temp) && !is_file($temp)) file_put_contents($temp, "select disk 0\ndetail disk");
        $output = shell_exec("diskpart /s ".$temp);
        $lines = explode("\n",$output);
        $result = @array_filter($lines,callback($lines));
        if(count($result)>0) {
            $result = array_shift(array_values($result));
            $result = explode(":",$result);
            $result = trim(end($result));       
        } else $result = $output;       
    } else {
        $result = shell_exec("blkid -o value -s UUID");  
        if(stripos($result,"blkid")!==false) {
            $result = $_SERVER['HTTP_HOST'];
        }
    }   
	return $salt.':'.$result;
}

function isCronjob() {
	if(!isset($_SERVER['HTTP_HOST'])) {
		 return true;
	}
	if (php_sapi_name() == 'cli' || php_sapi_name() == 'cgi-fcgi') {   
	   if (isset($_SERVER['TERM'])) {   
		  return true;
	   } else {   
		  return false;
	   }   
	} else { 
	   return false; 
	}	
}

function initiallock($key) {
	$salt = $_SERVER['HTTP_HOST'];
	$salt = str_replace('www.', '', $salt);
	$string = FixPathID($salt);	
	$PasswordHash = new PasswordHash(32, 'Vanilla');
		if(!$PasswordHash->CheckPassword($string, $key, 'Vanilla', 'KeyEncript')) {
			$return = false;		
		} else {
			$return = true;
		}	
	if(isCronjob() || @$URL=='API'){
		$return = true;
	}
	if(schoolByDomain()>0) $return = true;
	return $return;
}

function updateChecker() {	
$return = false;
return $return;
}
function thisURL() {
	return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	
}
function compareVersions($response,$configverssion_id) {
	if($response > $configverssion_id 
		&& (substr($response,0,1)==substr($configverssion_id,0,1)) 
		&& (strlen($response)==strlen($configverssion_id))) {	
		return true;
	} else {
		return false;
	}
}

function isOutdated() {
	return false;
}

if(isset($_REQUEST['no_update'])) {
$cookie_name = "hideUpgrade";$cookie_value = $_REQUEST['no_update'];
setcookie($cookie_name, $cookie_value, time() + (86400 * 365), "/");	
}

global $sett;
define('LICENSE',$sett['rawlice']);
function autoUpdate() { 
 return false;
}
if($URL != 'API' && $URL != 'smsAPI' && $URL != 'incomingCallback') echo '<div id="update-spin" style="display:none;font-size:20px;color:#fff;text-align:center;position:fixed;top:10px;left:0;width:100%;text-align:center;padding:20px 0;background:rgba(0,0,0,0.7);z-index:2083037">Please wait...</div>';

function recurse_copy($src,$dst) { 
	$dir = opendir($src); 
	@mkdir($dst); 
	while(false !== ( $file = readdir($dir)) ) { 
		if (( $file != '.' ) && ( $file != '..' )) { 
			if ( is_dir($src . '/' . $file) ) { 
				recurse_copy($src . '/' . $file,$dst . '/' . $file); 
			} 
			else { 
				copy($src . '/' . $file,$dst . '/' . $file); 
			} 
		} 
	} 
	closedir($dir); 
}
function upgradeSystem($source,$destination) {
	$return = true;
	return $return;		
}
$transit = updateChecker();	

function upgradeToLatest() {
	return 'success:'.'0'; 	
}
function updateToLatest() {
	return 'success:'.'0'; 	
}
function loadPaymentGateway($id,$reference,$amount,$user) {
	global $LANG;
	$alias = paymentGatewayData($id,'type');
	$button = paymentGatewayTemplateData($alias,'hide_pay_button');
	if($button < 1) { 
		$button = '<button id="inv_pay_b" type="submit" class="btn btn-success">Pay Now with '.ucfirst($alias).'</button>';
	} else {
		$button = '';
	}
	require_once 'gateway/'.$alias.'/index.php';
	$PaymentGateway = new PaymentGateway();
	echo $PaymentGateway->initiatePayment($id,$reference,$amount,$user,$button);
}

function systemInformation() {
	return false;
}

function amistillokme() {
	return false;
}

function checkLicense() {
	global $sett;
	global $heda;
	global $ivl;
	$return = 'Reg'	
	return $return;
}
define('LKS',$sett['rawlice']);
function updateMyLicense($key) {
	global $sett;
	//$host = $_SERVER['SERVER_NAME'];
	$ip = $_SERVER['SERVER_ADDR'];
	$salt = $_SERVER['HTTP_HOST'];
	$salt = str_replace('www.', '', $salt);
	$string = FixPathID($salt);
	$PasswordHash = new PasswordHash(32, 'Vanilla');		
	$tp='REG';
	$encKey = $PasswordHash->HashPassword($string);
	$encLic = $PasswordHash->HashPassword($string.':'.$key.':'.$tp);
	$file_path = HELP.'constant_helper.php';
	$current = $sett['location'];$udi = $sett['udi'];$inatia = $sett['inatial'];$tr = $sett['Server_HTTP_State'];$current2 = $sett['session_hatch'];$lice = $sett['rawlice'];
	$results = @chmod(HELP.'constant_helper.php', 0777); 
	$data3 = read_file(HELP.'constant_helper.php');
	$data3 = str_replace($current, $encKey, $data3);
	$data3 = str_replace($current2, $encLic, $data3);
	$data3 = str_replace($lice, $key, $data3);
	$data3 = str_replace($udi, $respnnse, $data3);
	$data3 = str_replace($inatia, date('Y-m-d'), $data3);
	$data3 = str_replace($tr, '54236733', $data3);
	write_file(HELP.'constant_helper.php', $data3);		
	return 'success:0';
}


if(isset($_POST['submit_key'])) {
	global $sett;
	$key = $_POST['key'];
	$ip = $_SERVER['SERVER_ADDR'];
	$salt = $_SERVER['HTTP_HOST'];
	$salt = str_replace('www.', '', $salt);
	$string = FixPathID($salt);
	$PasswordHash = new PasswordHash(32, 'Vanilla');		
	$tp='REG';
	$encKey = $PasswordHash->HashPassword($string);
	$encLic = $PasswordHash->HashPassword($string.':'.$key.':'.$tp);
	$file_path = HELP.'constant_helper.php';
	$current = $sett['location'];$udi = $sett['udi'];$inatia = $sett['inatial'];$tr = $sett['Server_HTTP_State'];$current2 = $sett['session_hatch'];$lice = $sett['rawlice'];
	$results = @chmod(HELP.'constant_helper.php', 0777); 
	$data3 = read_file(HELP.'constant_helper.php');
	$data3 = str_replace($current, $encKey, $data3);
	$data3 = str_replace($current2, $encLic, $data3);
	$data3 = str_replace($lice, $key, $data3);
	$data3 = str_replace($udi, $respnnse, $data3);
	$data3 = str_replace($inatia, date('Y-m-d'), $data3);
	$data3 = str_replace($tr, '54236733', $data3);
	write_file(HELP.'constant_helper.php', $data3);		
	echo $heda;
	echo $cls;
	die();
}

if(isset($_POST['update_key'])) {
	global $sett;
	$key = $_POST['key'];
	$ip = $_SERVER['SERVER_ADDR'];
	$salt = $_SERVER['HTTP_HOST'];
	$salt = str_replace('www.', '', $salt);
	$string = FixPathID($salt);
	$PasswordHash = new PasswordHash(32, 'Vanilla');		
	$tp='REG';
	$encKey = $PasswordHash->HashPassword($string);
	$encLic = $PasswordHash->HashPassword($string.':'.$key.':'.$tp);
	$file_path = HELP.'constant_helper.php';
	$current = $sett['location'];$udi = $sett['udi'];$inatia = $sett['inatial'];$tr = $sett['Server_HTTP_State'];$current2 = $sett['session_hatch'];$lice = $sett['rawlice'];
	$results = @chmod(HELP.'constant_helper.php', 0777); 
	$data3 = read_file(HELP.'constant_helper.php');
	$data3 = str_replace($current, $encKey, $data3);
	$data3 = str_replace($current2, $encLic, $data3);
	$data3 = str_replace($lice, $key, $data3);
	$data3 = str_replace($udi, $respnnse, $data3);
	$data3 = str_replace($inatia, date('Y-m-d'), $data3);
	$data3 = str_replace($tr, '54236733', $data3);
	write_file(HELP.'constant_helper.php', $data3);		
	 header('location: license?ok');
}

$lastRan=getSetting('lastMinuteCron','0');
if($lastRan+(60*10)<time()) {
	define('cron_stat','Not Running');		
} else {
	define('cron_stat','Running');	
}

$state = $configinstalled;
if($state == 'INSTALLED') { 
	global $sett;
	$allowed = 'ok';	
}
$state = $configinstalled;

/**
 * XML-RPC Message class
 *
 * @category	XML-RPC
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/xmlrpc.html
 */
 if(!class_exists('XML_RPC_Message')) {
class XML_RPC_Message extends CI_Xmlrpc
{
	var $payload;
	var $method_name;
	var $params			= array();
	var $xh				= array();

	public function __construct($method, $pars=0)
	{
		parent::__construct();

		$this->method_name = $method;
		if (is_array($pars) && count($pars) > 0)
		{
			for ($i=0; $i<count($pars); $i++)
			{
				// $pars[$i] = XML_RPC_Values
				$this->params[] = $pars[$i];
			}
		}
	}

	//-------------------------------------
	//  Create Payload to Send
	//-------------------------------------

	function createPayload()
	{
		$this->payload = "<?xml version=\"1.0\"?".">\r\n<methodCall>\r\n";
		$this->payload .= '<methodName>' . $this->method_name . "</methodName>\r\n";
		$this->payload .= "<params>\r\n";

		for ($i=0; $i<count($this->params); $i++)
		{
			// $p = XML_RPC_Values
			$p = $this->params[$i];
			$this->payload .= "<param>\r\n".$p->serialize_class()."</param>\r\n";
		}

		$this->payload .= "</params>\r\n</methodCall>\r\n";
	}

	//-------------------------------------
	//  Parse External XML-RPC Server's Response
	//-------------------------------------

	function parseResponse($fp)
	{
		$data = '';

		while ($datum = fread($fp, 4096))
		{
			$data .= $datum;
		}

		//-------------------------------------
		//  DISPLAY HTTP CONTENT for DEBUGGING
		//-------------------------------------

		if ($this->debug === TRUE)
		{
			echo "<pre>";
			echo "---DATA---\n" . htmlspecialchars($data) . "\n---END DATA---\n\n";
			echo "</pre>";
		}

		//-------------------------------------
		//  Check for data
		//-------------------------------------

		if ($data == "")
		{
			error_log($this->xmlrpcstr['no_data']);
			$r = new XML_RPC_Response(0, $this->xmlrpcerr['no_data'], $this->xmlrpcstr['no_data']);
			return $r;
		}


		//-------------------------------------
		//  Check for HTTP 200 Response
		//-------------------------------------

		if (strncmp($data, 'HTTP', 4) == 0 && ! preg_match('/^HTTP\/[0-9\.]+ 200 /', $data))
		{
			$errstr= substr($data, 0, strpos($data, "\n")-1);
			$r = new XML_RPC_Response(0, $this->xmlrpcerr['http_error'], $this->xmlrpcstr['http_error']. ' (' . $errstr . ')');
			return $r;
		}

		//-------------------------------------
		//  Create and Set Up XML Parser
		//-------------------------------------

		$parser = xml_parser_create($this->xmlrpc_defencoding);

		$this->xh[$parser]					= array();
		$this->xh[$parser]['isf']			= 0;
		$this->xh[$parser]['ac']			= '';
		$this->xh[$parser]['headers']		= array();
		$this->xh[$parser]['stack']			= array();
		$this->xh[$parser]['valuestack']	= array();
		$this->xh[$parser]['isf_reason']	= 0;

		xml_set_object($parser, $this);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
		xml_set_element_handler($parser, 'open_tag', 'closing_tag');
		xml_set_character_data_handler($parser, 'character_data');
		//xml_set_default_handler($parser, 'default_handler');


		//-------------------------------------
		//  GET HEADERS
		//-------------------------------------

		$lines = explode("\r\n", $data);
		while (($line = array_shift($lines)))
		{
			if (strlen($line) < 1)
			{
				break;
			}
			$this->xh[$parser]['headers'][] = $line;
		}
		$data = implode("\r\n", $lines);


		//-------------------------------------
		//  PARSE XML DATA
		//-------------------------------------

		if ( ! xml_parse($parser, $data, count($data)))
		{
			$errstr = sprintf('XML error: %s at line %d',
					xml_error_string(xml_get_error_code($parser)),
					xml_get_current_line_number($parser));
			//error_log($errstr);
			$r = new XML_RPC_Response(0, $this->xmlrpcerr['invalid_return'], $this->xmlrpcstr['invalid_return']);
			xml_parser_free($parser);
			return $r;
		}
		xml_parser_free($parser);

		// ---------------------------------------
		//  Got Ourselves Some Badness, It Seems
		// ---------------------------------------

		if ($this->xh[$parser]['isf'] > 1)
		{
			if ($this->debug === TRUE)
			{
				echo "---Invalid Return---\n";
				echo $this->xh[$parser]['isf_reason'];
				echo "---Invalid Return---\n\n";
			}

			$r = new XML_RPC_Response(0, $this->xmlrpcerr['invalid_return'],$this->xmlrpcstr['invalid_return'].' '.$this->xh[$parser]['isf_reason']);
			return $r;
		}
		elseif ( ! is_object($this->xh[$parser]['value']))
		{
			$r = new XML_RPC_Response(0, $this->xmlrpcerr['invalid_return'],$this->xmlrpcstr['invalid_return'].' '.$this->xh[$parser]['isf_reason']);
			return $r;
		}

		//-------------------------------------
		//  DISPLAY XML CONTENT for DEBUGGING
		//-------------------------------------

		if ($this->debug === TRUE)
		{
			echo "<pre>";

			if (count($this->xh[$parser]['headers'] > 0))
			{
				echo "---HEADERS---\n";
				foreach ($this->xh[$parser]['headers'] as $header)
				{
					echo "$header\n";
				}
				echo "---END HEADERS---\n\n";
			}

			echo "---DATA---\n" . htmlspecialchars($data) . "\n---END DATA---\n\n";

			echo "---PARSED---\n" ;
			var_dump($this->xh[$parser]['value']);
			echo "\n---END PARSED---</pre>";
		}

		//-------------------------------------
		//  SEND RESPONSE
		//-------------------------------------

		$v = $this->xh[$parser]['value'];

		if ($this->xh[$parser]['isf'])
		{
			$errno_v = $v->me['struct']['faultCode'];
			$errstr_v = $v->me['struct']['faultString'];
			$errno = $errno_v->scalarval();

			if ($errno == 0)
			{
				// FAULT returned, errno needs to reflect that
				$errno = -1;
			}

			$r = new XML_RPC_Response($v, $errno, $errstr_v->scalarval());
		}
		else
		{
			$r = new XML_RPC_Response($v);
		}

		$r->headers = $this->xh[$parser]['headers'];
		return $r;
	}

	// ------------------------------------
	//  Begin Return Message Parsing section
	// ------------------------------------

	// quick explanation of components:
	//   ac - used to accumulate values
	//   isf - used to indicate a fault
	//   lv - used to indicate "looking for a value": implements
	//		the logic to allow values with no types to be strings
	//   params - used to store parameters in method calls
	//   method - used to store method name
	//	 stack - array with parent tree of the xml element,
	//			 used to validate the nesting of elements

	//-------------------------------------
	//  Start Element Handler
	//-------------------------------------

	function open_tag($the_parser, $name, $attrs)
	{
		// If invalid nesting, then return
		if ($this->xh[$the_parser]['isf'] > 1) return;

		// Evaluate and check for correct nesting of XML elements

		if (count($this->xh[$the_parser]['stack']) == 0)
		{
			if ($name != 'METHODRESPONSE' && $name != 'METHODCALL')
			{
				$this->xh[$the_parser]['isf'] = 2;
				$this->xh[$the_parser]['isf_reason'] = 'Top level XML-RPC element is missing';
				return;
			}
		}
		else
		{
			// not top level element: see if parent is OK
			if ( ! in_array($this->xh[$the_parser]['stack'][0], $this->valid_parents[$name], TRUE))
			{
				$this->xh[$the_parser]['isf'] = 2;
				$this->xh[$the_parser]['isf_reason'] = "XML-RPC element $name cannot be child of ".$this->xh[$the_parser]['stack'][0];
				return;
			}
		}

		switch($name)
		{
			case 'STRUCT':
			case 'ARRAY':
				// Creates array for child elements

				$cur_val = array('value' => array(),
								 'type'	 => $name);

				array_unshift($this->xh[$the_parser]['valuestack'], $cur_val);
			break;
			case 'METHODNAME':
			case 'NAME':
				$this->xh[$the_parser]['ac'] = '';
			break;
			case 'FAULT':
				$this->xh[$the_parser]['isf'] = 1;
			break;
			case 'PARAM':
				$this->xh[$the_parser]['value'] = NULL;
			break;
			case 'VALUE':
				$this->xh[$the_parser]['vt'] = 'value';
				$this->xh[$the_parser]['ac'] = '';
				$this->xh[$the_parser]['lv'] = 1;
			break;
			case 'I4':
			case 'INT':
			case 'STRING':

			case 'BOOLEAN':
			case 'DOUBLE':
			case 'DATETIME.ISO8601':
			case 'BASE64':
				if ($this->xh[$the_parser]['vt'] != 'value')
				{
					//two data elements inside a value: an error occurred!
					$this->xh[$the_parser]['isf'] = 2;
					$this->xh[$the_parser]['isf_reason'] = "'Twas a $name element following a ".$this->xh[$the_parser]['vt']." element inside a single value";
					return;
				}

				$this->xh[$the_parser]['ac'] = '';
			break;
			case 'MEMBER':
				// Set name of <member> to nothing to prevent errors later if no <name> is found
				$this->xh[$the_parser]['valuestack'][0]['name'] = '';

				// Set NULL value to check to see if value passed for this param/member
				$this->xh[$the_parser]['value'] = NULL;
			break;
			case 'DATA':
			case 'METHODCALL':
			case 'METHODRESPONSE':
			case 'PARAMS':
				// valid elements that add little to processing
			break;
			default:
				/// An Invalid Element is Found, so we have trouble
				$this->xh[$the_parser]['isf'] = 2;
				$this->xh[$the_parser]['isf_reason'] = "Invalid XML-RPC element found: $name";
			break;
		}

		// Add current element name to stack, to allow validation of nesting
		array_unshift($this->xh[$the_parser]['stack'], $name);

		if ($name != 'VALUE') $this->xh[$the_parser]['lv'] = 0;
	}
	// END


	//-------------------------------------
	//  End Element Handler
	//-------------------------------------

	function closing_tag($the_parser, $name)
	{
		if ($this->xh[$the_parser]['isf'] > 1) return;

		// Remove current element from stack and set variable
		// NOTE: If the XML validates, then we do not have to worry about
		// the opening and closing of elements.  Nesting is checked on the opening
		// tag so we be safe there as well.

		$curr_elem = array_shift($this->xh[$the_parser]['stack']);

		switch($name)
		{
			case 'STRUCT':
			case 'ARRAY':
				$cur_val = array_shift($this->xh[$the_parser]['valuestack']);
				$this->xh[$the_parser]['value'] = ( ! isset($cur_val['values'])) ? array() : $cur_val['values'];
				$this->xh[$the_parser]['vt']	= strtolower($name);
			break;
			case 'NAME':
				$this->xh[$the_parser]['valuestack'][0]['name'] = $this->xh[$the_parser]['ac'];
			break;
			case 'BOOLEAN':
			case 'I4':
			case 'INT':
			case 'STRING':
			case 'DOUBLE':
			case 'DATETIME.ISO8601':
			case 'BASE64':
				$this->xh[$the_parser]['vt'] = strtolower($name);

				if ($name == 'STRING')
				{
					$this->xh[$the_parser]['value'] = $this->xh[$the_parser]['ac'];
				}
				elseif ($name=='DATETIME.ISO8601')
				{
					$this->xh[$the_parser]['vt']	= $this->xmlrpcDateTime;
					$this->xh[$the_parser]['value'] = $this->xh[$the_parser]['ac'];
				}
				elseif ($name=='BASE64')
				{
					$this->xh[$the_parser]['value'] = base64_decode($this->xh[$the_parser]['ac']);
				}
				elseif ($name=='BOOLEAN')
				{
					// Translated BOOLEAN values to TRUE AND FALSE
					if ($this->xh[$the_parser]['ac'] == '1')
					{
						$this->xh[$the_parser]['value'] = TRUE;
					}
					else
					{
						$this->xh[$the_parser]['value'] = FALSE;
					}
				}
				elseif ($name=='DOUBLE')
				{
					// we have a DOUBLE
					// we must check that only 0123456789-.<space> are characters here
					if ( ! preg_match('/^[+-]?[eE0-9\t \.]+$/', $this->xh[$the_parser]['ac']))
					{
						$this->xh[$the_parser]['value'] = 'ERROR_NON_NUMERIC_FOUND';
					}
					else
					{
						$this->xh[$the_parser]['value'] = (double)$this->xh[$the_parser]['ac'];
					}
				}
				else
				{
					// we have an I4/INT
					// we must check that only 0123456789-<space> are characters here
					if ( ! preg_match('/^[+-]?[0-9\t ]+$/', $this->xh[$the_parser]['ac']))
					{
						$this->xh[$the_parser]['value'] = 'ERROR_NON_NUMERIC_FOUND';
					}
					else
					{
						$this->xh[$the_parser]['value'] = (int)$this->xh[$the_parser]['ac'];
					}
				}
				$this->xh[$the_parser]['ac'] = '';
				$this->xh[$the_parser]['lv'] = 3; // indicate we've found a value
			break;
			case 'VALUE':
				// This if() detects if no scalar was inside <VALUE></VALUE>
				if ($this->xh[$the_parser]['vt']=='value')
				{
					$this->xh[$the_parser]['value']	= $this->xh[$the_parser]['ac'];
					$this->xh[$the_parser]['vt']	= $this->xmlrpcString;
				}

				// build the XML-RPC value out of the data received, and substitute it
				$temp = new XML_RPC_Values($this->xh[$the_parser]['value'], $this->xh[$the_parser]['vt']);

				if (count($this->xh[$the_parser]['valuestack']) && $this->xh[$the_parser]['valuestack'][0]['type'] == 'ARRAY')
				{
					// Array
					$this->xh[$the_parser]['valuestack'][0]['values'][] = $temp;
				}
				else
				{
					// Struct
					$this->xh[$the_parser]['value'] = $temp;
				}
			break;
			case 'MEMBER':
				$this->xh[$the_parser]['ac']='';

				// If value add to array in the stack for the last element built
				if ($this->xh[$the_parser]['value'])
				{
					$this->xh[$the_parser]['valuestack'][0]['values'][$this->xh[$the_parser]['valuestack'][0]['name']] = $this->xh[$the_parser]['value'];
				}
			break;
			case 'DATA':
				$this->xh[$the_parser]['ac']='';
			break;
			case 'PARAM':
				if ($this->xh[$the_parser]['value'])
				{
					$this->xh[$the_parser]['params'][] = $this->xh[$the_parser]['value'];
				}
			break;
			case 'METHODNAME':
				$this->xh[$the_parser]['method'] = ltrim($this->xh[$the_parser]['ac']);
			break;
			case 'PARAMS':
			case 'FAULT':
			case 'METHODCALL':
			case 'METHORESPONSE':
				// We're all good kids with nuthin' to do
			break;
			default:
				// End of an Invalid Element.  Taken care of during the opening tag though
			break;
		}
	}

	//-------------------------------------
	//  Parses Character Data
	//-------------------------------------

	function character_data($the_parser, $data)
	{
		if ($this->xh[$the_parser]['isf'] > 1) return; // XML Fault found already

		// If a value has not been found
		if ($this->xh[$the_parser]['lv'] != 3)
		{
			if ($this->xh[$the_parser]['lv'] == 1)
			{
				$this->xh[$the_parser]['lv'] = 2; // Found a value
			}

			if ( ! @isset($this->xh[$the_parser]['ac']))
			{
				$this->xh[$the_parser]['ac'] = '';
			}

			$this->xh[$the_parser]['ac'] .= $data;
		}
	}


	function addParam($par) { $this->params[]=$par; }

	function output_parameters($array=FALSE)
	{
		$CI =& get_instance();
		
		if ($array !== FALSE && is_array($array))
		{
			while (list($key) = each($array))
			{
				if (is_array($array[$key]))
				{
					$array[$key] = $this->output_parameters($array[$key]);
				}
				else
				{
					// 'bits' is for the MetaWeblog API image bits
					// @todo - this needs to be made more general purpose
					$array[$key] = ($key == 'bits' OR $this->xss_clean == FALSE) ? $array[$key] : $CI->security->xss_clean($array[$key]);
				}
			}

			$parameters = $array;
		}
		else
		{
			$parameters = array();

			for ($i = 0; $i < count($this->params); $i++)
			{
				$a_param = $this->decode_message($this->params[$i]);

				if (is_array($a_param))
				{
					$parameters[] = $this->output_parameters($a_param);
				}
				else
				{
					$parameters[] = ($this->xss_clean) ? $CI->security->xss_clean($a_param) : $a_param;
				}
			}
		}

		return $parameters;
	}


	function decode_message($param)
	{
		$kind = $param->kindOf();

		if ($kind == 'scalar')
		{
			return $param->scalarval();
		}
		elseif ($kind == 'array')
		{
			reset($param->me);
			list($a,$b) = each($param->me);

			$arr = array();

			for($i = 0; $i < count($b); $i++)
			{
				$arr[] = $this->decode_message($param->me['array'][$i]);
			}

			return $arr;
		}
		elseif ($kind == 'struct')
		{
			reset($param->me['struct']);

			$arr = array();

			while (list($key,$value) = each($param->me['struct']))
			{
				$arr[$key] = $this->decode_message($value);
			}

			return $arr;
		}
	}

} // End XML_RPC_Messages class
 }
$state = $configinstalled; if($state == 'INSTALLED') { isBroken(); }

// END XML_RPC_Values Class
function minuteCronStat() {
	$lastHour = time()-((60*5)+(60));
	if(getSetting('lastMinuteCron',0)==0 || getSetting('lastMinuteCron',0) < $lastHour) {
		return 'Not Working';	
	}
	return 'Working';	
}

function mysql_strict_status() {
	global $server;
	$result = mysqli_query($server,"SELECT @@sql_mode;");
	$result = mysqli_fetch_assoc($result);
	if((stripos($result['@@sql_mode'],'STRICT_TRANS_TABLES') !== false)) {
		return 'Enabled';	
	} else {
		return 'Disabled';
	}
}
function check_class_exist() {
	if(class_exists('ZipArchive')) {
		return 'Installed';
	} 
	return 'Not Installed';
}
function check_mb_convert_encoding_exist() {
	if(function_exists('mb_convert_encoding')) {
		return 'Installed';
	} 
	return 'Not Installed';
}
function check_iconv_encoding_exist() {
	if(function_exists('iconv')) {
		return 'Installed';
	} 
	return 'Not Installed';
}

function check_mem_lim() {
	$memory_limit = ini_get('memory_limit');
	if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
		if ($matches[2] == 'M') {
			$memory_limit = $matches[1];
		} else if ($matches[2] == 'K') {
			$memory_limit = $matches[1]/ 1024; 
		}
	}
	return round($memory_limit);	
}
function check_url_open() {
	if( ini_get('allow_url_fopen') ) {
	   return 'Enabled';	
	} else {
		return 'Disabled';
	}
}

/* End of file Xmlrpc.php */
/* Location: ./system/libraries/Xmlrpc.php */
if(DEMO_MODE) {
	if(isset($_REQUEST['delete']) || isset($_REQUEST['update'])) { ?>
		<script>
		 alert('Sorry but you can not Delete or Edit a record in Demo Mode\nPlease consider creating a fresh record if you are trying to update an existing record');
		 window.location = "index.php";
		</script>
	<?php 
	}
}
if(!function_exists('powered_by')) {
function powered_by() {
	global $configapp_version;
	if(strtolower(checkLicense())!='pro'){	?>
		<div style="position:absolute; right:20px;bottom:10px;text-align:center;padding:10px;font-size: 14px; font-weight:bold;color:#000;text-shadow:-1px 0 #fff, 0 1px #fff, 1px 0 #fff, 0 -1px #fff;">Powered by SOA v<?=$configapp_version?></div>	
	<?php }
}
}

function proOnly() {
	if(strtolower(checkLicense())!='pro'){	
		echo '<!doctype html> 
<html>
<head>
<meta charset="utf-8">
<style type="text/css">
body {
	font: 100%/1.4 Verdana, Arial, Helvetica, sans-serif;
	background-color: #000;
	margin: 0;
	padding: 0;
	color: #000;
  padding: 0;
  -webkit-background-size: 100% 100%;   
  -moz-background-size: 100% 100%;   
  -o-background-size: 100% 100%;   
  background-size: 100% 100%;
  background-size: cover;
  background-repeat:no-repeat;
  background-attachment:fixed;
}

a:link {
	color:#414958;
	text-decoration: underline;
	font-weight: bold;
}
a:visited {
	color: #4E5869;
	text-decoration: underline;
}
a:hover, a:active, a:focus {
	text-decoration: none;
}

.container {
	width: 100%;
	min-width: 780px;
	margin: 0 auto; 
	
}

.box {
	width: 60%;
	background-color: white;
	border: solid 3px #469;
	border-radius: 7px;
	padding: 20px;
	margin: 0 auto;	
	margin-top: 40px;
	-webkit-box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
	   -moz-box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
	        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);	
	border-radius: 7px;
}

.message {
	width: 80%;
	background-color: transparent;
	border: solid 1px transparent;
	border-radius: 7px;
	padding: 10px;
	margin: 0 auto;	
	font-size: 18px;
	text-align: center;
	margin-bottom: 10px;
}

.success {
	background-color: #CFC;
	color: #030;	
	border-color: #093;
}

.error {
	background-color: #FCC;
	color: #900;	
	border-color: #C93;
}

.info {
	background-color: #E8F1F9;
	color: #003;	
	border-color: #69F;
}

.content {
	padding: 10px 0;
}

.submit, .buy {
	width: 200px;
	height: 45px;
	font-size: 20px;
	color: white;
	font-weight: bold;
	border: solid 1px #036;	
	border-radius: 2px;
}

.submit:hover, .buy:hover {
	background-color: black;
	cursor: pointor;
}

.submit {
	background-color: #063;	
}

.buy {
	background-color: #036;	
}

#email, #key {
	width: 80%;
	height: 40px;
	border: 1px solid #777;
	border-radius: 5px;
	margin: 0 auto;
	margin-top: 10px;
	display: block;	
	padding-left: 10px;
	font-size: 18px;
	text-transform: uppercase;
}
p.in {
	text-align: center;
	margin-top: 10px;
	margin-bottom: 20px;	
}
</style>
</head>
<body>
<div class="container">
  <div class="content">
	<div class="box">
        	<div class="message error">
            	';
	echo "We are sorry but you are not licensed to access this feature! Please upgrade to Pro License to use this feature and all other Pro features."; 
echo '  </div>
            <form action="" method="get">
             <p class="in"><a href="dashboard"><button class="buy" type="button">Dashboard</button></a> </p>
            </form>
        </div>
  <!-- end .content --></div>
  <!-- end .container --></div>
</body>
</html>'; die();
	}
}

if(@$_GET['url']=='usercoursecontent'||@$_GET['url']=='coursecontent'||@$_GET['url']=='courses'||@$_GET['url']=='cbt'||@$_GET['url']=='usercourses'||@$_GET['url']=='usercbt'||@$_GET['url']=='schools'){proOnly();}

function displayPagination($setLimit=30,$page,$clause,$append='_np'){
  global $server;
  $per_page = $setLimit;
  $pageLimit = ($page * $setLimit) - $setLimit;
  $clause =  str_replace("select * ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT * ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT m.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT t.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT a.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT b.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT c.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT d.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT e.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT f.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT p.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT s.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace(" limit $pageLimit,$setLimit", "", $clause);
  $clause =  str_replace(" LIMIT $pageLimit,$setLimit", "", $clause);
  //echo css
  echo "<style>";
  echo "ul.setPaginate li.setPage{
	padding:15px 15px;
	font-size:14px;
	}

	ul.setPaginate{
	margin:0px;
	padding:0px;
	//height:100%;
	overflow:hidden;
	font:12px 'Tahoma';
	list-style-type:none;
	}

	ul.setPaginate li.dot{padding: 3px 0;}

	ul.setPaginate li{
	float:left;
	margin:0px;
	padding:0px;
	margin-left:5px;
	}



	ul.setPaginate li a
	{
	background: none repeat scroll 0 0 #ffffff;
	border: 1px solid #cccccc;
	color: #999999;
	display: inline-block;
	font: 15px/25px Arial,Helvetica,sans-serif;
	margin: 5px 3px 0 0;
	padding: 0 5px;
	text-align: center;
	text-decoration: none;
	}

	ul.setPaginate li a:hover,
	ul.setPaginate li a.current_page
	{
	background: none repeat scroll 0 0 #777777;

	color: #ffffff;
	text-decoration: none;
	}

	ul.setPaginate li a{
	color:black;
	display:block;
	text-decoration:none;
	padding:5px 8px;
	text-decoration: none;
	}";
  echo "</style>";
  $page_url=$_SERVER['REQUEST_URI']."?1";
  if(count($_GET) && strpos($_SERVER['REQUEST_URI'], '?')!== false ) {
	   $page_url = $_SERVER['REQUEST_URI'].'&';
  }
  $page_url = str_replace("page=".$page, "", $page_url);
  $page_url = str_replace("&&", "&", $page_url);
  $page_url = str_replace('?'.$append, "", $page_url);
  $page_url = str_replace('&'.$append, "", $page_url);
  $page_url = str_replace("?&", "?", $page_url);  
  $page_url .= '&'.$append.'&';
  $page_url = str_replace("pag=", "&pag=", $page_url);
  $page_url = str_replace("&&", "&", $page_url);
  $query = $clause;
   
   $rec = mysqli_fetch_array(mysqli_query($server, $query));
   $total = $rec['totalCount'];
     $adjacents = "2";

   $page = ($page == 0 ? 1 : $page);
   $start = ($page - 1) * $per_page;

   $prev = $page - 1;
   $next = $page + 1;
   $setLastpage = ceil($total/$per_page)-0;
   $lpm1 = $setLastpage - 1;

   $setPaginate = "";
   if($setLastpage > 1)
   {
     $setPaginate .= "<ul class='setPaginate'>";
                 $setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
     if ($setLastpage < 7 + ($adjacents * 2))
     {
       for ($counter = 1; $counter <= $setLastpage; $counter++)
       {
         if ($counter == $page)
           $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
         else
           $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
       }
     }
     elseif($setLastpage > 5 + ($adjacents * 2))
     {
       if($page < 1 + ($adjacents * 2))
       {
         for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
         {
           if ($counter == $page)

             $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
           else
             $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
         }
         $setPaginate.= "<li class='dot'>...</li>";
         $setPaginate.= "<li><a href='{$page_url}page=$lpm1'>$lpm1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}page=$setLastpage'>$setLastpage</a></li>";
       }
       elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
       {
         $setPaginate.= "<li><a href='{$page_url}page=1'>1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}page=2'>2</a></li>";
         $setPaginate.= "<li class='dot'>...</li>";
         for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
         {
           if ($counter == $page)
             $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
           else
             $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
         }
         $setPaginate.= "<li class='dot'>..</li>";
         $setPaginate.= "<li><a href='{$page_url}page=$lpm1'>$lpm1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}page=$setLastpage'>$setLastpage</a></li>";
       }
       else
       {
         $setPaginate.= "<li><a href='{$page_url}page=1'>1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}page=2'>2</a></li>";
         $setPaginate.= "<li class='dot'>..</li>";
         for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
         {
           if ($counter == $page)
             $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
           else
             $setPaginate.= "<li><a href='{$page_url}page=$counter'>$counter</a></li>";
         }
       }
     }

     if ($page < $counter - 1){
       $setPaginate.= "<li><a href='{$page_url}page=$next'>Next</a></li>";
             $setPaginate.= "<li><a href='{$page_url}page=$setLastpage'>Last</a></li>";
     }else{
       $setPaginate.= "<li><a class='current_page'>Next</a></li>";
             $setPaginate.= "<li><a class='current_page'>Last</a></li>";
         }

     $setPaginate.= "</ul>";
   }
     echo $setPaginate;
 }

function displayPagination2($setLimit=30,$page,$clause,$append='_np'){
  global $server;
  $per_page = $setLimit;
  $pageLimit = ($page * $setLimit) - $setLimit;
  $clause =  str_replace("select * ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT * ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT m.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT t.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace(" limit $pageLimit,$setLimit", "", $clause);
  $clause =  str_replace(" LIMIT $pageLimit,$setLimit", "", $clause);
  //echo css
  echo "<style>";
  echo "ul.setPaginate li.setPage{
	padding:15px 15px;
	font-size:14px;
	}

	ul.setPaginate{
	margin:0px;
	padding:0px;
	//height:100%;
	overflow:hidden;
	font:12px 'Tahoma';
	list-style-type:none;
	}

	ul.setPaginate li.dot{padding: 3px 0;}

	ul.setPaginate li{
	float:left;
	margin:0px;
	padding:0px;
	margin-left:5px;
	}



	ul.setPaginate li a
	{
	background: none repeat scroll 0 0 #ffffff;
	border: 1px solid #cccccc;
	color: #999999;
	display: inline-block;
	font: 15px/25px Arial,Helvetica,sans-serif;
	margin: 5px 3px 0 0;
	padding: 0 5px;
	text-align: center;
	text-decoration: none;
	}

	ul.setPaginate li a:hover,
	ul.setPaginate li a.current_page
	{
	background: none repeat scroll 0 0 #777777;

	color: #ffffff;
	text-decoration: none;
	}

	ul.setPaginate li a{
	color:black;
	display:block;
	text-decoration:none;
	padding:5px 8px;
	text-decoration: none;
	}";
  echo "</style>";
  $page_url=$_SERVER['REQUEST_URI']."?1";
  if(count($_GET) && strpos($_SERVER['REQUEST_URI'], '?')!== false ) {
	  $page_url = $_SERVER['REQUEST_URI'].'&';
  }
  $page_url = str_replace("pag=".$page, "", $page_url);
  $page_url = str_replace("&&", "&", $page_url);
  $page_url = str_replace('?'.$append, "", $page_url);
  $page_url = str_replace('&'.$append, "", $page_url);
  $page_url = str_replace("?&", "?", $page_url);  
  $page_url .= '&'.$append.'&';
  $page_url = str_replace("page=", "&page=", $page_url);
  $page_url = str_replace("&&", "&", $page_url);
  $query = $clause;
   
   $rec = mysqli_fetch_array(mysqli_query($server, $query));
   $total = $rec['totalCount'];
     $adjacents = "2";

   $page = ($page == 0 ? 1 : $page);
   $start = ($page - 1) * $per_page;

   $prev = $page - 1;
   $next = $page + 1;
   $setLastpage = ceil($total/$per_page)-0;
   $lpm1 = $setLastpage - 1;

   $setPaginate = "";
   if($setLastpage > 1)
   {
     $setPaginate .= "<ul class='setPaginate'>";
                 $setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
     if ($setLastpage < 7 + ($adjacents * 2))
     {
       for ($counter = 1; $counter <= $setLastpage; $counter++)
       {
         if ($counter == $page)
           $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
         else
           $setPaginate.= "<li><a href='{$page_url}pag=$counter'>$counter</a></li>";
       }
     }
     elseif($setLastpage > 5 + ($adjacents * 2))
     {
       if($page < 1 + ($adjacents * 2))
       {
         for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
         {
           if ($counter == $page)
             $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
           else
             $setPaginate.= "<li><a href='{$page_url}pag=$counter'>$counter</a></li>";
         }
         $setPaginate.= "<li class='dot'>...</li>";
         $setPaginate.= "<li><a href='{$page_url}pag=$lpm1'>$lpm1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}pag=$setLastpage'>$setLastpage</a></li>";
       }
       elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
       {
         $setPaginate.= "<li><a href='{$page_url}pag=1'>1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}pag=2'>2</a></li>";
         $setPaginate.= "<li class='dot'>...</li>";
         for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
         {
           if ($counter == $page)
             $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
           else
             $setPaginate.= "<li><a href='{$page_url}pag=$counter'>$counter</a></li>";
         }
         $setPaginate.= "<li class='dot'>..</li>";
         $setPaginate.= "<li><a href='{$page_url}pag=$lpm1'>$lpm1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}pag=$setLastpage'>$setLastpage</a></li>";
       }
       else
       {
         $setPaginate.= "<li><a href='{$page_url}pag=1'>1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}pag=2'>2</a></li>";
         $setPaginate.= "<li class='dot'>..</li>";
         for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
         {
           if ($counter == $page)
             $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
           else
             $setPaginate.= "<li><a href='{$page_url}pag=$counter'>$counter</a></li>";
         }
       }
     }

     if ($page < $counter - 1){
       $setPaginate.= "<li><a href='{$page_url}pag=$next'>Next</a></li>";
             $setPaginate.= "<li><a href='{$page_url}pag=$setLastpage'>Last</a></li>";
     }else{
       $setPaginate.= "<li><a class='current_page'>Next</a></li>";
             $setPaginate.= "<li><a class='current_page'>Last</a></li>";
         }

     $setPaginate.= "</ul>";
   }
     echo $setPaginate;
 }
 
 function displayPaginationN($setLimit=30,$page,$clause,$append='',$nth='_np'){
  global $server;
  $per_page = $setLimit;
  $pageLimit = ($page * $setLimit) - $setLimit;
  $clause =  str_replace("select * ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT * ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT m.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace("SELECT t.* ", "select count(*) as totalCount ", $clause);
  $clause =  str_replace(" limit $pageLimit,$setLimit", "", $clause);
  $clause =  str_replace(" LIMIT $pageLimit,$setLimit", "", $clause);
  //echo css
  echo "<style>";
  echo "ul.setPaginate li.setPage{
	padding:15px 15px;
	font-size:14px;
	}

	ul.setPaginate{
	margin:0px;
	padding:0px;
	//height:100%;
	overflow:hidden;
	font:12px 'Tahoma';
	list-style-type:none;
	}

	ul.setPaginate li.dot{padding: 3px 0;}

	ul.setPaginate li{
	float:left;
	margin:0px;
	padding:0px;
	margin-left:5px;
	}



	ul.setPaginate li a
	{
	background: none repeat scroll 0 0 #ffffff;
	border: 1px solid #cccccc;
	color: #999999;
	display: inline-block;
	font: 15px/25px Arial,Helvetica,sans-serif;
	margin: 5px 3px 0 0;
	padding: 0 5px;
	text-align: center;
	text-decoration: none;
	}

	ul.setPaginate li a:hover,
	ul.setPaginate li a.current_page
	{
	background: none repeat scroll 0 0 #777777;

	color: #ffffff;
	text-decoration: none;
	}

	ul.setPaginate li a{
	color:black;
	display:block;
	text-decoration:none;
	padding:5px 8px;
	text-decoration: none;
	}";
  echo "</style>";
  $page_url=$_SERVER['REQUEST_URI']."?1";
  if(count($_GET) && strpos($_SERVER['REQUEST_URI'], '?')!== false ) {
	  $page_url = $_SERVER['REQUEST_URI'].'&';
  }
  $page_url = str_replace("pag".$nth."=".$page, "", $page_url);
  $page_url = str_replace("&&", "&", $page_url);
  $page_url = str_replace('?'.$append, "", $page_url);
  $page_url = str_replace('&'.$append, "", $page_url);
  $page_url = str_replace("?&", "?", $page_url);  
  $page_url .= '&'.$append.'&';
  if(empty($nth)) {
  $page_url = str_replace("pag=", "&pag=", $page_url);
  } else {
	for($n=0;$n<=10;$n++) {
		if($n != $nth)
		$page_url = str_replace("pag".$n."=", "&pag".$n."=", $page_url);
	}
  }
  $page_url = str_replace("&&", "&", $page_url);
  $query = $clause;
   
   $rec = mysqli_fetch_array(mysqli_query($server, $query));
   $total = $rec['totalCount'];
     $adjacents = "2";

   $page = ($page == 0 ? 1 : $page);
   $start = ($page - 1) * $per_page;

   $prev = $page - 1;
   $next = $page + 1;
   $setLastpage = ceil($total/$per_page)-0;
   $lpm1 = $setLastpage - 1;

   $setPaginate = "";
   if($setLastpage > 1)
   {
     $setPaginate .= "<ul class='setPaginate'>";
                 $setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
     if ($setLastpage < 7 + ($adjacents * 2))
     {
       for ($counter = 1; $counter <= $setLastpage; $counter++)
       {
         if ($counter == $page)
           $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
         else
           $setPaginate.= "<li><a href='{$page_url}pag$nth=$counter'>$counter</a></li>";
       }
     }
     elseif($setLastpage > 5 + ($adjacents * 2))
     {
       if($page < 1 + ($adjacents * 2))
       {
         for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
         {
           if ($counter == $page)
             $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
           else
             $setPaginate.= "<li><a href='{$page_url}pag$nth=$counter'>$counter</a></li>";
         }
         $setPaginate.= "<li class='dot'>...</li>";
         $setPaginate.= "<li><a href='{$page_url}pag$nth=$lpm1'>$lpm1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}pag$nth=$setLastpage'>$setLastpage</a></li>";
       }
       elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
       {
         $setPaginate.= "<li><a href='{$page_url}pag$nth=1'>1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}pag$nth=2'>2</a></li>";
         $setPaginate.= "<li class='dot'>...</li>";
         for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
         {
           if ($counter == $page)
             $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
           else
             $setPaginate.= "<li><a href='{$page_url}pag$nth=$counter'>$counter</a></li>";
         }
         $setPaginate.= "<li class='dot'>..</li>";
         $setPaginate.= "<li><a href='{$page_url}pag$nth=$lpm1'>$lpm1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}pag$nth=$setLastpage'>$setLastpage</a></li>";
       }
       else
       {
         $setPaginate.= "<li><a href='{$page_url}pag$nth=1'>1</a></li>";
         $setPaginate.= "<li><a href='{$page_url}pag$nth=2'>2</a></li>";
         $setPaginate.= "<li class='dot'>..</li>";
         for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
         {
           if ($counter == $page)
             $setPaginate.= "<li><a class='current_page'>$counter</a></li>";
           else
             $setPaginate.= "<li><a href='{$page_url}pag$nth=$counter'>$counter</a></li>";
         }
       }
     }

     if ($page < $counter - 1){
       $setPaginate.= "<li><a href='{$page_url}pag$nth=$next'>Next</a></li>";
             $setPaginate.= "<li><a href='{$page_url}pag=$setLastpage'>Last</a></li>";
     }else{

       $setPaginate.= "<li><a class='current_page'>Next</a></li>";
             $setPaginate.= "<li><a class='current_page'>Last</a></li>";
         }

     $setPaginate.= "</ul>";
   }
     echo $setPaginate;
 }
 
 function unzip($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true) {
  if ($zip = zip_open($src_file))  {
    if ($zip)  {
      $splitter = ($create_zip_name_dir === true) ? "." : "/";
      if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";
      
      create_dirs($dest_dir);

      while ($zip_entry = zip_read($zip))    {

        $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
        if ($pos_last_slash !== false) {
          create_dirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
        }

        if (zip_entry_open($zip,$zip_entry,"r"))  {
          
          $file_name = $dest_dir.zip_entry_name($zip_entry);
          
          if ($overwrite === true || $overwrite === false && !is_file($file_name))  {
            $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            file_put_contents($file_name, $fstream );
            chmod($file_name, 0777);
          }
          
          zip_entry_close($zip_entry);
        }       
      }
      zip_close($zip);
    }
  } else {
    return false;
  }
  return true;
}

function create_dirs($path){
  if (!is_dir($path)) {
    $directory_path = "";
    $directories = explode("/",$path);
    array_pop($directories);
    
    foreach($directories as $directory) {
      $directory_path .= $directory."/";
      if (!is_dir($directory_path)) {
        mkdir($directory_path);
        chmod($directory_path, 0777);
      }
    }
  }
}
function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            $this->deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}
function getUserIP() {
	if (!empty($_SERVER["HTTP_CLIENT_IP"]))
	{
	 $ip = $_SERVER["HTTP_CLIENT_IP"];
	}
	elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{
	 $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	else
	{
	 $ip = $_SERVER["REMOTE_ADDR"];
	}
return $ip;
}
function getAdminSchool() {
	return $_SESSION['school_id'];	
}
function isLogedIn() {
  if (!isset($_SESSION['SOAUser']) || !isset($_SESSION['SOAUser']) || empty($_SESSION['SOAUser']))
   { return false; } else { return true; }
}
function isApplicant() {
  if (!isset($_SESSION['SOAApply']) || !isset($_SESSION['SOAApply']) || empty($_SESSION['SOAApply']))
   { return false; } else { return true; }
}
function adminLogedIn() {
  if (!isset($_SESSION['SOAAdmin']) || !isset($_SESSION['SOAAdmin']) || empty($_SESSION['SOAAdmin']))
   { return false; } else { return true; }
}
function getApplicant() {
return @$_SESSION['SOAApply'];
}
function getAdmin() {
return @$_SESSION['SOAAdmin'];
}
function checkSchoolLimit() {
	global $server;
	$permited = enterpriseLimit();
	$hasnow = countSchools();
	$erase = $hasnow - $permited;
	if($erase>0) {
		$sql=$query="Delete from schools ORDER BY id DESC LIMIT ".$erase;
		mysqli_query($server, $query);
	}
}
function countSchools() {
	global $server;
	$sql=$query= "SELECT * FROM schools";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	return $num;
}
function enterpriseLimit() {
$return = 1;
return $return;
}
function addEnterprise($key) {
	return false;
}

function valEnt($user) {
	return "Failed";
}
if(getUser()<1) {
	checkLicense();
	powered_by();
}
checkSchoolLimit();
$permited = enterpriseLimit();
$hasnow = countSchools();
$left = $permited - $hasnow;
define('SCHOOLLIMIT',$left);
define('HASENTERPRISE',$permited);
define('TOTAL_SCHOOL',$hasnow);

if(getUser()>0) {
$userID	= getUser();
global $userID;
}
require_once(BASEPATH.'Barcode/BCGFont.php');
require_once(BASEPATH.'Barcode/BCGColor.php');
require_once(BASEPATH.'Barcode/BCGDrawing.php');	
require_once(BASEPATH.'Barcode/BCGcode128.barcode.php');