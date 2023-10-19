<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); global $configapp_version; global $configversion_date; global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		hotel.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			4/03/2015
*/

global $server;
$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(!isOwner($userID)) {
header('location: admin.php');
}
$currentSession = getSetting('current_session');
$currentTerm = getSetting('current_term');

if(isset($_GET['ok'])) {
	showMessage("Congratulations!<br>Your new SOA license has been activated.",'');
}
if(isset($_GET['u'])) {
	showMessage("Oops!<br>The license key you supplied has already been used.",'');
}
if(isset($_GET['iv'])) {
	showMessage("Oops!<br>The license key you supplied is not valid.",'');
}
if(isset($_GET['e'])) {
	showMessage("Oops!<br>We are not able to validate your license. Please try again.",'');
}
?>
<div class="wrapper">
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        <table  width="98%">
        	<tr>
            	<td width="40%"><h4>Current Version</h4></td><td><h4><?=$configapp_version?></h4></td>
            </tr>
            <tr>
            	<td><h4>Last Updated</h4></td><td><h4><?=$configversion_date;?></h4></td>
            </tr>
            <tr>
            	<td><h4>License Key</h4></td><td><h4><?=DEMO_MODE==true?"***********":@LKS?></h4></td>
            </tr>
            <tr>
            	<td><h4>License Type</h4></td><td><h4><?=@LICENSE_TYPE?></h4></td>
            </tr>
            <tr>
            	<td><h4>Cron Status</h4></td><td><h4><?=@cron_stat?></h4></td>
            </tr>
        </table>
        
        <hr>
        <h2>Server Information</h2> 
        <table  width="98%">
        	<tr><td width="40%"><h4>PHP Version</h4></td><td><h4><?=PHP_VERSION?></h4></td></tr>
			<tr><td ><h4>MySQL Version</h4></td><td><h4><?=mysqli_get_server_info($server)?></h4></td></tr>
			<tr><td ><h4>Server Software</h4></td><td><h4><?=$_SERVER['SERVER_SOFTWARE']?></h4></td></tr>
			<tr><td ><h4>Roor Directory</h4></td><td><h4><?=str_replace('/system/','',BASEPATH)?></h4></td></tr>
			<tr><td ><h4>Cron Status</h4></td><td><h4><?=minuteCronStat()?></h4></td></tr>
            
            <tr><td><h4>MySQL Strict-mode Status</h4></td><td><h4><?=mysql_strict_status()?></h4></td></tr> 
			<tr><td><h4>Multibyte String Extension</h4></td><td><h4><?=check_mb_convert_encoding_exist()?> </h4></td></tr>
			<tr><td><h4>Iconv Extension</h4></td><td><h4><?=check_iconv_encoding_exist()?></h4></td></tr>
			<tr><td class="fw-600"><h4>PHP Memory Limit</h4></td><td><h4><?=check_mem_lim()?>M</h4> </td></tr>
        </table>
        <hr>
        <h2>Update License</h2> 
        <p>Eter your new license key below to update or upgrade your product license.</p>
        <form action="" method="POST">
        <table  width="98%">
        	<tr class="box">
            	<td width="60%"><input style="height: 35px;min-width: 95%;font-size:20px;padding:5px;" name="key" type="text" id="key" required <?=DEMO_MODE==true?"readonly":""?> value="Your New License Key" maxlength="60" onfocus="if(this.value  == \'Your New License Key\') { this.value = \'\'; } " onblur="if(this.value == \'\') { this.value = \'Your New License Key\'; } "></td><td><button type="submit" class="submit btn btn-success btn-lg" onclick="return confirm(\'Your new license will be applied to this SOA installation. Click OK to confirm\')" name="update_key">Update License</button></td>
            </tr>
        </table>
        </form>
        <p><br></p>
        </div>
    </div>
</div>
