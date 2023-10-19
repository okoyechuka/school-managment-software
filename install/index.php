<?php 
define('ENVIRONMENT', 'production');
define('PRODUCT', 'SOA');
ob_start();
ini_set('max_execution_time', 340); 
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
$system_path = '../system';
$application_folder = 'hooks';

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
	}
	else
	{
		if ( ! is_dir(BASEPATH.$application_folder.'/'))
		{
			exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);
		}

		define('APPPATH', BASEPATH.$application_folder.'/');
	}

include_once('../root.php');

include_once('../system/core/Config.php');
$state=$configinstalled;
if($state=='INSTALLED') { header('location: ../index.php');	}
function GetIonCubeLoaderVersion(){
    ob_start();
    phpinfo(INFO_GENERAL);
    $aux = str_replace('&nbsp;', ' ', ob_get_clean());
    if($aux !== false)  {
        $pos = mb_stripos($aux, 'ionCube PHP Loader');
        if($pos !== false)  {
            $aux = mb_substr($aux, $pos + 18);  $aux = mb_substr($aux, mb_stripos($aux, ' v') + 2);
            $version = '';  $c = 0; $char = mb_substr($aux, $c++, 1);
            while(mb_strpos('0123456789.', $char) !== false)  {
                $version .= $char; $char = mb_substr($aux, $c++, 1);
            }
            return $version;
        }
    }
    return false;
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
	if(class_exists('ZipArchive')) { return 'Installed'; }  return 'Not Installed'; 
}
function check_mb_convert_encoding_exist() {
	if(function_exists('mb_convert_encoding')) {return 'Installed';	} 
	return 'Not Installed';
}
function check_iconv_encoding_exist() {
	if(function_exists('iconv')) {return 'Installed';	} 
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
	if( ini_get('allow_url_fopen') ) { return 'Enabled';	
	} else { return 'Disabled';	}
}
function getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false) {
global $confighost;
global $configdatabase;
global $configuser;
global $configpassword;
global $configinstalled;
global $configapp_store;
global $configverssion_id;
global $configapp_version;
global $configapp_name;
global $configversion_date;		
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
define('noInst','<p style="background:rgba(255,242,242,1); border: 1px solid #800;padding:5px;color: rgba(153,0,0,1)">One or more requirements are missing on your server. Please fix the missing features before you can proceed</p>');	
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

function dbs($host,$username,$password,$database,$pass,$email) {
	$er = '';
	$server = mysqli_connect($host,$username,$password);
	if (mysqli_connect_errno()) { $er = mysqli_connect_error(); }
	$dbselect = mysqli_select_db($server, $database);
	if (!$dbselect) { $er .= mysqli_error($server); }
	$templine = '';
	$filename = 'db.sql';
	$lines = file($filename);
	foreach ($lines as $line){
	if (substr($line, 0, 2) == '--' || $line == '')
		continue;
		$templine .= $line;
		if (substr(trim($line), -1, 1) == ';'){
			$query = mysqli_query($server,$templine);
			if (!$query) { 	$er .= mysqli_error($server); }
			$templine = '';
		}
	}
	mysqli_close($server);	
	$server = mysqli_connect($host,$username,$password);
	if (mysqli_connect_errno()) { $er = mysqli_connect_error(); }
	$dbselect = mysqli_select_db($server, $database);
	
	$password2 = $pass;
	$salt = genRandomPassword(32);
	$crypt = getCryptedPassword($password2, $salt);
	$pass = $crypt.':'.$salt;	
	$last_login_date = date('Y-m-d h:i:s');
	
	//create default school
	$query = mysqli_query($server, "INSERT INTO `schools` (`id`,`name`, `address`, `city`, `state`, `country_id`, `phone1`, `phone2`, `email`, `logo`, `currency_id`, `local_council`, `portal_welcome_message`, `domain`, `current_session`, `current_term`, `graduate_class_id`, `SMS_username`, `SMS_password`, `defaultTimeZone`, `SMS_sender`, `simplepay_id`, `paypal_id`, `gtpay_id`, `webpay_id`, `voguepay_id`, `pin_enabled`,`register_pin_enabled`, `nextscheduled`,`theme`) VALUES
('1','', '', '', '', '70', '', '', '', 'logo.png', 1, '', 'Welcome to our school', '', 1, 1, 2, '', '', '', '', '', '', '', '', '','0','1', '".time()."','default_blue.css');");
	if (!$query) { 	$er .= mysqli_error($server); } else {$school_id = 1 ;}
	
	//create supper admin
	$query = mysqli_query($server, "INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `role_id`, `last_login`, `profile_id`, `school_id`, `phone`, `is_supper`) VALUES
('1', 'admin', '$pass', 'Supper Admin', '$email', '1', '$last_login_date', '0', '$school_id', '','1');");
	if (!$query) { 	$er .= mysqli_error($server); }
	
	//Update Config file
	$file_path = '../system/core/Config.php';
	$resultsd = @chmod($file_path, 0777);
	include($file_path);		
	$h = $confighost ;
	$d = $configdatabase;
	$u = $configuser;
	$p = $configpassword ;
	$i = $configinstalled;
	$data3 = read_file($file_path);
	$data3 = str_replace($h, $host, $data3);
	$data3 = str_replace($d, $database, $data3);
	$data3 = str_replace($u, $username, $data3);
	$data3 = str_replace($p, $password, $data3);
	$data3 = str_replace($i, 'INSTALLED', $data3);
	write_file($file_path, $data3);	

	mysqli_close($server);	

	if(empty($er)) {
		return true;	
	} else {
		return $er;	
	}

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=PRODUCT?> Installation Wizard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style type="text/css">
body {
	font: 100%/1.4 Verdana, Arial, Helvetica, sans-serif;
	background-color: #333;
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


.green-text {
	color: green;
}
.red-text {
	color: red;
}
a:link {
	color:#414958;
	text-decoration: underline;
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
	border: solid 3px #096;
	border-radius: 5px;
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
	border: solid 1px #000;	
	border-radius: 5px;
}

.submit:hover, .buy:hover {
	background-color: black;
}

.submit {
	background-color: #063;	
}

.buy {
	background-color: #066;	
}

#email, #key, #key1, #key2, #key3, #key4 {
	width: 80%;
	height: 40px;
	border: 1px solid #777;
	border-radius: 5px;
	margin: 0 auto;
	margin-top: 10px;
	display: block;	
	padding-left: 10px;
	font-size: 18px;
	text-transform: none;
}

p.in {
	text-align: center;
	margin-top: 10px;
	margin-bottom: 20px;	
}

iframe {
	border: 0;
border-color: white;
margin-bottom: 10px;	
}
</style>
</head>

<?php 
function showSuc($pass) {
?>	
<body>
<div class="container">
  <div class="content">
	<div class="box">

            <form action="../index.php" method="post">
              <h1 align="center"><?=PRODUCT?></h1>
              <h3 align="center"> <?=PRODUCT?> Installer</h3>
              <p><strong>Congratulations!</strong></p>
              <p>You have successfully installed <?=PRODUCT?>. Your admin/staff portal can be accessed at <a href="<?php echo home_base_url().'admin.php'; ?>"><?php echo home_base_url().'admin.php'; ?></a>, your parents/students portal can be accessed at <a href="<?php echo home_base_url().'index.php'; ?>"><?php echo home_base_url().'index.php'; ?></a> and your students application portal can be accessed at <a href="<?php echo home_base_url().'apply.php'; ?>"><?php echo home_base_url().'apply.php'; ?></a>.</p>
              <p>Click on "Finish" to login and setup your script.</p>
              <p><strong>Admin Username:</strong> <?='admin'?></p>
              <p><strong>Admin Password:</strong> <?=$pass?></p>
              <p>Remember to change your admin password and update your settings once you login. <br>Setup a Cron Job to run the following scripts once every minute: <strong><?=$_SERVER['DOCUMENT_ROOT']?>/crons.php</strong>
              <p>Please read the SOA Quick-Start Manual before you start using this software.</p>
              <p>Remember to change your admin password and update your settings once you login.</p>
              <div align="center">
              <input type="hidden" name="path" value="../install">
<p class="in"> <button class="buy" name="finish">Finish</button></p>
      </form>
    </div>
  <!-- end .content --></div>
  <!-- end .container --></div>
</body>
<?php	
die();
}
function showError($error) {
?>	
<body>
<div class="container">
  <div class="content">
	<div class="box">

            <form action="" method="post">
              <h1 align="center"><?=PRODUCT?></h1>
              <h3 align="center"> <?=PRODUCT?> Installer</h3>
        	<div class="message error">
            	<?php echo $error; ?>
            </div> 
             	<input name="host" type="text" id="key" required value="Host name (e.g. localhost)" onfocus="if(this.value  == 'Host name (e.g. localhost)') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Host name (e.g. localhost)'; } ">
                
            	<input name="username" type="text" id="key1" required value="Username"  onfocus="if(this.value  == 'Username') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Username'; } ">
                
            	<input name="password" type="text" id="key2"  value="Password"  onfocus="if(this.value  == 'Password') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Password'; } ">

            	<input name="database" type="text" id="key3" required value="Database"  onfocus="if(this.value  == 'Database') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Database'; } ">
                
                <input name="email" type="email" id="key4" required value="Email Address" onfocus="if(this.value  == 'Email Address') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Email Address'; } ">
                                                               
<p class="in"> <button class="buy" name="checkdb">Proceed</button></p>
      </form>
    </div>
  <!-- end .content --></div>
  <!-- end .container --></div>
</body>
<?php	
die();
}
 
if(isset($_POST['checkdb'])) {
$error = '';
$_SESSION['email'] = $_POST['email'];
$dc = @mysqli_connect($_POST['host'],$_POST['username'],$_POST['password']);	
	if($dc) {
		$ds = @mysqli_select_db($dc,$_POST['database']);
		if($ds) {
			$pass = rand(199999, 999999);
			$is = dbs($_POST['host'],$_POST['username'],$_POST['password'],$_POST['database'],$pass,$_POST['email']);
			if($is) {
				showSuc($pass);	
			}else {
				$error = 'I\'m unable to complete the setup!<br>'.$is;	
			}				
		} else {
			$error = 'I\'m unable to select your database; "'.$_POST['database'].'". Please make sure this database exists and is accessible by the user "'.$_POST['username'].'"';
		}	
	} else {
		$error = 'I\'m unable to connect to your host using your connection details. Please check the information you 	provided and try again';
	}
	
	if(!empty($error)) {
		showError($error);	
	}
	
}

if(isset($_POST['ok'])) {
?>
<body>
<div class="container">
  <div class="content">
	<div class="box">

            <form action="" method="post">
              <h1 align="center"><?=PRODUCT?></h1>
              <h3 align="center"> <?=PRODUCT?> Installer</h3>
              <p>I need your database connection details in order to setup a detabase for myself. A fresh database is recommended. </p>
            	<input name="host" type="text" id="key" required value="Host name (e.g. localhost)" onfocus="if(this.value  == 'Host name (e.g. localhost)') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Host name (e.g. localhost)'; } ">
                
            	<input name="username" type="text" id="key1" required value="Username" onfocus="if(this.value  == 'Username') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Username'; } ">
                
            	<input name="password" type="text" id="key2"  value="Password" onfocus="if(this.value  == 'Password') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Password'; } ">

            	<input name="database" type="text" id="key3" required value="Database"  onfocus="if(this.value  == 'Database') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Database'; } ">
                
                <input name="email" type="email" id="key4" required value="Email Address" onfocus="if(this.value  == 'Email Address') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Email Address'; } ">
                                                               
<p class="in"> <button class="buy" name="checkdb">Proceed</button></p>
      </form>
    </div>
  <!-- end .content --></div>
  <!-- end .container --></div>
</body>
<?php	
die();
}
if(isset($_GET['install'])) {
//make sure all is fine
$proceed = 1;
if(PHP_VERSION < 5.6) $proceed = -1;
if(!function_exists('mysqli_connect')) $proceed = -1;
if(GetIonCubeLoaderVersion()<10) $proceed = -1;
if(check_class_exist()!='Installed') $proceed = -1;
if(check_url_open()!='Enabled') $proceed = -1;	
?>
<body>
<div class="container">
  <div class="content">
	<div class="box">

            <form action="" method="post">
              <h1 align="center"><?=PRODUCT?></h1>
              <h3 align="center"> Welcome to <?=PRODUCT?> Installer</h3>
              <p>Before you continue, ensure entire <?=PRODUCT?> directory and sub directories are writeable (0755), that your server is running PHP 5.6.3 or higher, MySQL 5.6 or higher, and that "cURL" & "allow_url_fopen" are enabled.</p>
              <h4>Your Server Status</h4>
              <table style="width:90%;min-width:300px;">
                	<tbody>
                    	<tr class="">
                			<td><b>PHP Version</b></td>
                            <td><?=PHP_VERSION?> <?=PHP_VERSION>=5.6?"<i class='fa fa-check-circle green-text fa-1x'></i>":"<i class='fa fa-times-circle red-text fa-1x'></i>"?></td>        	
                        </tr>
                        <tr class="">
                			<td><b>MySQLi Extension</b></td>
                            <td><?=function_exists('mysqli_connect')?'Installed':'Not Installed'?> <?=function_exists('mysqli_connect')?"<i class='fa fa-check-circle green-text fa-1x'></i>":"<i class='fa fa-times-circle red-text fa-1x'></i>"?></td>        	
                        </tr> 
                    	<tr class="">
                			<td><b>Ioncube Loader Version</b></td>
                            <td><?=GetIonCubeLoaderVersion()?> <?=GetIonCubeLoaderVersion()>10?"<i class='fa fa-check-circle green-text fa-1x'></i>":"<i class='fa fa-times-circle red-text fa-1x'></i>"?></td>        	
                        </tr>
                        <tr class="">
                			<td><b>ZIP Archive Perl Module</b></td>
                            <td><?=check_class_exist()?> <?=check_class_exist()=='Installed'?"<i class='fa fa-check-circle green-text fa-1x'></i>":"<i class='fa fa-times-circle red-text fa-1x'></i>"?></td>        	
                        </tr>
                        <tr class="">
                            <td><b>Multibyte String Extension</b></td>
                            <td><?=check_mb_convert_encoding_exist()?> <?=check_mb_convert_encoding_exist()=='Installed'?"<i class='fa fa-check-circle green-text fa-1x'></i>":"<i class='fa fa-times-circle red-text fa-1x'></i>"?></td>        	
                        </tr>
                        <tr class="">
                            <td><b>Iconv Extension</b></td>
                            <td><?=check_iconv_encoding_exist()?> <?=check_iconv_encoding_exist()=='Installed'?"<i class='fa fa-check-circle green-text fa-1x'></i>":"<i class='fa fa-times-circle red-text fa-1x'></i>"?></td>        	
                        </tr>
                        <tr class="">
                            <td><b>PHP Memory Limit</b></td>
                            <td><?=check_mem_lim()?>M <?=check_mem_lim()>=128?"<i class='fa fa-check-circle green-text fa-1x'></i>":"<i class='fa fa-times-circle red-text fa-1x'></i>"?></td>        	
                        </tr>
                        <tr class="">
                            <td><b>File_Get_Content</b></td>
                            <td><?=check_url_open()?> <?=check_url_open()=='Enabled'?"<i class='fa fa-check-circle green-text fa-1x'></i>":"<i class='fa fa-times-circle red-text fa-1x'></i>"?></td>        	
                        </tr>
                    </tbody>
                </table>
              <div align="center">
              <iframe src="license.htm" width="90%" align="middle"></iframe></div>
             <input required name="agree" type="checkbox" value="Yes"> I accept Software License Agreement
                <?=$proceed<1?noInst:''?>
                <p class="in"> <button class="buy" <?=$proceed<1?'disabled style="background:#bbb;"':''?> name="ok">Proceed</button></p>
      </form>
    </div>
  <!-- end .content --></div>
  <!-- end .container --></div>
</body>
</html>
<?php 
die();
}
?>
<body>
<div class="container">
  <div class="content">
	<div class="box">

              <h1 <?=PRODUCT?></h1>
              <h3 align="center"> Welcome to <?=PRODUCT?> Installer</h3>
              <p>Use this tool only if you want to run a fresh installation. <br>
              Make sure you have read the Quick-Start manual and that your server met all system requirements before you continue</p>
              <div align="center">
			<p class="in"> <a href="index.php?install=1"><button class="buy" style="width: 250px;"  name="ok">Start Fresh Installation</button></a> </p>
		    </div>
  <!-- end .content --></div>
  <!-- end .container --></div>
</body>
</html>