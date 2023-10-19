<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;


global $server;

if(isset($_REQUEST['msg'])) {
	$message = 'This seem to be your first time. Please configure your SMS gateway so you can start sending messages';
	$class = 'blue';
}

if(userRole($userID) > 2) {
header('location: admin.php');
}

if(isset($_POST['save-setting'])) {
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		if($key != 'save-setting' && $key != 'sendMessage') {
			$value = mysqli_real_escape_string($server,$value);
			saveSettings($key,$value,0) ;	
		}
	}
	showMessage("Your SMS Gateway settings have been successfully updated.<br><a href='admin/admindashboard'></a> to apply these settings now", '');
}
?>

<div class="wrapper">
	<div class="inner-left" style="width: 100%;">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
    <form method="post" action="" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" class="tr-heading" colspan="2"  valign="middle"><g>Main Settings</g></td>
      </tr>

      <tr>
        <td align="left" valign="middle">API Base URL:</td>
        <td  align="left" valign="middle">
        	<input type="text" required name="base_url" value="<?php echo getSetting('base_url'); ?>">
        </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">API HTTP Request Type:</td>
        <td  align="left" valign="middle">
            <select required name="request_type" style="width: 90%;" >
               <option value="GET" <?php if(getSetting('request_type')== 'GET') { echo 'selected';} ?> >GET</option>
               <option value="POST" <?php if(getSetting('request_type')== 'POST') { echo 'selected';} ?> >POST</option>
			</select>
        </td>
      </tr>
      
       <tr>
        <td align="left" valign="middle">Is Authentication Required?</td>
        <td  align="left" valign="middle">
            <select required name="authentication" style="width: 90%;" >
               <option value="0" <?php if(getSetting('authentication')== '0') { echo 'selected';} ?> >No</option>
               <option value="1" <?php if(getSetting('authentication')== '1') { echo 'selected';} ?> >Yes</option>
			</select>
        </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">API Username or Key:</td>
        <td  align="left" valign="middle">
        	<input type="text" name="username" value="<?php echo getSetting('username'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">API Password or Token:</td>
        <td  align="left" valign="middle">
        	<input type="text" name="password" value="<?php echo getSetting('password'); ?>">
        </td>
      </tr>
      
       <tr>
        <td align="left" valign="middle">Should I Use Basic Authentication?</td>
        <td  align="left" valign="middle">
            <select required name="base64_encode" style="width: 90%;" >
               <option value="0" <?php if(getSetting('base64_encode')== '0') { echo 'selected';} ?> >No</option>
               <option value="1" <?php if(getSetting('base64_encode')== '1') { echo 'selected';} ?> >Yes</option>
			</select>
        </td>
      </tr>
      
       <tr>
        <td align="left" valign="middle">Is JSON Encoded Posts Body Required:</td>
        <td  align="left" valign="middle">
            <select required name="json_encode" style="width: 90%;" >
               <option value="0" <?php if(getSetting('json_encode')== '0') { echo 'selected';} ?> >No</option>
               <option value="1" <?php if(getSetting('json_encode')== '1') { echo 'selected';} ?> >Yes</option>
			</select>
        </td>
      </tr>
      
       <tr>
        <td align="left" valign="middle">API Success Ward:</td>
        <td  align="left" valign="middle">
        	<input type="text" name="success_word" required value="<?php echo getSetting('success_word'); ?>">
        </td>
      </tr>
      
       <tr>
        <td align="left" valign="middle">API Success Logic:</td>
        <td  align="left" valign="middle">
            <select required name="success_logic" style="width: 90%;" >
               <option value="contain" <?php if(getSetting('success_logic')== 'contain') { echo 'selected';} ?> >Contains the Success Word</option>
               <option value="notcontain" <?php if(getSetting('success_logic')== 'notcontain') { echo 'selected';} ?> >Does Not Contain Success Word</option>
			</select>
        </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">Sender ID Parameter Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="sender_field" required="required" placeholder="eg. From" value="<?php echo getSetting('sender_field'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Recipient Parameter Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="recipient_field" required="required" placeholder="eg. To" value="<?php echo getSetting('recipient_field'); ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Message Parameter Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="message_field" required="required" placeholder="eg. Body" value="<?php echo getSetting('message_field'); ?>">
        </td>
      </tr>

	  <tr>
        <td align="left" class="tr-heading" colspan="2"  valign="middle"><g>Additional Settings</g></td>
      </tr>
      	
	  <tr>
        <td align="left" valign="middle">Additional Parameter 1 Field:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="param1_field" value="<?php echo getSetting('param1_field'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Additional Parameter 1 Value:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="param1_value" value="<?php echo getSetting('param1_value'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Additional Parameter 2 Field:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="param2_field" value="<?php echo getSetting('param2_field'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Additional Parameter 2 Value:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="param2_value" value="<?php echo getSetting('param2_value'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Additional Parameter 3 Field:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="param3_field" value="<?php echo getSetting('param3_field'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Additional Parameter 3 Value:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="param3_value" value="<?php echo getSetting('param3_value'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Additiona Parameter 4 Field:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="param3_field" value="<?php echo getSetting('param4_field'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Additional Parameter 4 Value:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="param4_value" value="<?php echo getSetting('param4_value'); ?>">
        </td>
      </tr>   

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="save-setting" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="sendMessage" value="1" type="submit">Update Settings</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Changes...</div>
        </td>
      </tr>
    </table>

  </div>

</div>
