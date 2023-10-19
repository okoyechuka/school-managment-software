<?php 
function home_base_url(){   
	$base_url = (isset($_SERVER['HTTPS']) &&
	$_SERVER['HTTPS']!='off') ? 'https://' : 'http://';
	$tmpURL = dirname(__FILE__);
	$tmpURL = str_replace(chr(92),'/',$tmpURL);
	$tmpURL = str_replace($_SERVER['DOCUMENT_ROOT'],'',$tmpURL);
	$tmpURL = ltrim($tmpURL,'/');
	$tmpURL = rtrim($tmpURL, '/');

    if (strpos($tmpURL,'/')){
       $tmpURL = explode('/',$tmpURL);
       $tmpURL1 = $tmpURL[0];
	   $tmpURL2 = $tmpURL[1];
	   $tmpURL = $tmpURL1;
	   if(!empty($tmpURL2)) $tmpURL .= '/'.$tmpURL2;
      }
 
    if ($tmpURL !== $_SERVER['HTTP_HOST'])
      $base_url .= $_SERVER['HTTP_HOST'].'/'.$tmpURL.'/';
    else
      $base_url .= $tmpURL.'/';

	$base_url = str_replace('//','/',$base_url);
	$base_url = str_replace('http:/','http://',$base_url);
	$base_url = str_replace('https:/','https://',$base_url);
return str_replace(dirname(__FILE__),'',$base_url); 
}
$local_path = dirname(__FILE__).'/';
$sSoftware = strtolower( $_SERVER["SERVER_SOFTWARE"] );
function getSlashes() {
	$sSoftware = strtolower( $_SERVER["SERVER_SOFTWARE"] );
	if ( strpos($sSoftware, "microsoft-iis") !== false )
		return "\\";
	else
		return "/";
}

if ( strpos($sSoftware, "microsoft-iis") !== false ) {
	$local_path = str_replace(getSlashes(), '/', dirname(__FILE__)).'/';
}	
function get_domain() {		
return $_SERVER['HTTP_HOST'];
}
$remote_path = home_base_url();		
if((strpos($remote_path, '127.0.0.1') !== false) || (strpos($remote_path, 'localhost') !== false)) {
$find = str_replace(' ','',":\ ");
@$local_path = end(explode($find,$local_path));
define('DOCUMENT_ROOT', '/'.$local_path);
}else{
define('DOCUMENT_ROOT', '/'.$local_path);
}
?>