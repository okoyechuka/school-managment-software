<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
global $configapp_version; global $URL; global $userID; global $LANG; global $server; global $school_id
# ---------------------------------------------------------------------
#  Add all footer HTML here
#----------------------------------------------------------------------
?>
<?php if(getUser()>0) {		
$_SESSION['myctRoomID'] = getUser();$small = '';
if(userRole(getUser())==4) {$small='<small style="color:#fff;font-size:13px;"> - '.className(getTeacherClass(userProfile(getUser()))).'</small>';}
if(userRole(getUser())==6) {$small='<small style="color:#fff;font-size:13px;"> - '.className(getClass(userProfile(getUser()),getSetting('current_session'))).'</small>';}
if(userRole(getUser())<5||userRole(getUser())==6) {?>
<div class="round hollow text-center" style="position:fixed;bottom:15px;right:5px;z-index:1000"> <a href="javascript:{}"  title="Chat Room" id="addClass"><span class="fa fa-comment"></span><d class="hide-xxs"> Chat</d></a><span id="nc">0</span></div>
<div class="popup-box chat-popup" id="qnimate">
	<div class="popup-head">
		<div class="popup-head-left pull-left">Chat-Room <?=$small?></div>
		<div class="popup-head-right pull-right">
			<button data-widget="remove" id="removeClass" class="chat-header-button pull-right" type="button"><i class="fa fa-times"></i></button>
		</div>
	</div>
	<div class="popup-messages" id="cthk">
		<div class="direct-chat-messages" >
        	<div id="jiners" class="formContent"><?php if(userRole(getUser())<4) echo '<h1 style="text-align:center;"><i class="fa fa-exclamation-circle"></i></h1><p style="text-align:center;font-size:13px;color:#000;">Only teachers and students with assigned class can join a chat room!</p>';?></div>
         	<div id="loadHistory" style="padding-bottom:15px;"></div>
        </div>
	</div>
	<div class="popup-messages-footer" id="typers">
    	<form action="#" onsubmit="return sendChat()" autocomplete="off">
        <?php if(userRole(getUser())==4||userRole(getUser())==6) {?>
			<input type="text" style="min-width:95%" id="status_message" placeholder="Type a message..."  name="message" /><?php } ?>
        </form>
	</div>
</div>
<?php }
if(isset($_GET['editAccount'])) {
		$sql = "SELECT * FROM users WHERE id = '$userID'";
		$result = mysqli_query($server,$sql);
		$num = mysqli_num_rows($result);
		$row = mysqli_fetch_assoc($result);
		$name = $row['name'];
		$phone = $row['phone'];
		$staff_id = $row['profile_id'];
		$username = $row['username'];
		$email = $row['email'];
?>
    <div id="add-new">
        <div id="add-new-head">Update My Account Details
        <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">X</div></a></div>
         <div class="inside">
        <form method="post" action="" enctype="multipart/form-data">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="left" valign="middle">Full Name:</td>
            <td  align="left" valign="middle">
                <input type="text" name="name" id="firstName" value="<?php echo $name; ?>" readonly placeholder="">        </td>
          </tr>
          <tr>
            <td align="left" valign="middle">Username:</td>
            <td  align="left" valign="middle">
                <input type="text"readonly name="username" id="username" value="<?php echo $username; ?>" placeholder="">
            </td>
          </tr>
          <tr>
            <td align="left" valign="middle">Email:</td>
            <td align="left" valign="middle">
                <input type="text" name="email" id="email" value="<?php echo $email; ?>" required placeholder="Enter your Email Address">
            </td>
          </tr>
          <tr>
            <td align="left" valign="middle">Mobile Phone:</td>
            <td align="left" valign="middle">
                <input type="text" name="phone" id="lastName" value="<?php echo $phone; ?>" maxlength="200" placeholder="Mobile Phone Number (include country code)">
            </td>
          </tr>
          <tr>
            <td align="left" valign="middle">Change Password:</td>
            <td align="left" valign="middle">
                <input type="text"  name="password" id="password" maxlength="200" placeholder="This will change the your current password">
            </td>
          </tr>
          <tr>
            <td align="left" valign="top">&nbsp;</td>
            <td width="69%" align="left" valign="top">
            <input type="hidden" name="staff" value="<?php echo $staff_id; ?>" />
            <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="updateProfile" value="1" type="submit">Save Changes</button>
        </form>
            <div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Updating...</div>
            </td>
          </tr>
       </table>
       </div>
     </div>
<?php }

if(isset($_POST['updateProfile'])) {
	$password = mysqli_real_escape_string($server,$_POST['password']);
	$email = mysqli_real_escape_string($server,$_POST['email']);
	$phone = mysqli_real_escape_string($server,$_POST['phone']);
	$profile_id = mysqli_real_escape_string($server,$_POST['staff']);


		mysqli_query($server,"UPDATE `users` SET
		`phone` = '$phone',
		`email` = '$email'
		 WHERE `id` = '$userID'") or die(mysqli_error($server));

	//update staff
	if(userRole($userID) == 4) {
	mysqli_query($server,"UPDATE `teachers` SET
		`phone` = '$phone',
		`email` = '$email'
	 WHERE `id` = '$profile_id'") or die(mysqli_error($server));
	} else {
	mysqli_query($server,"UPDATE `staffs` SET
		`phone` = '$phone',
		`email` = '$email'
	 WHERE `id` = '$profile_id'") or die(mysqli_error($server));
	}

	$message = "Your account details was successfully updated.";
	$class="green";

	if(!empty($password)) {
		$salt = genRandomPassword(32);
		$crypt = getCryptedPassword($password, $salt);
		$password2 = $crypt.':'.$salt;
		mysqli_query($server,"UPDATE `users` SET
		`password` = '$password2'
		 WHERE `id` = '$userID'") or die(mysqli_error($server));
		$message = "Your account details was successfully updated.";
		$class="green";
	}
}
# ---------------------------------------------------------------------
#  Do Not Modify Beyond This Line
#----------------------------------------------------------------------
?><!-- Footer Brand -->
<div id="foobrand"></div>
</div> <!-- End #ain-body -->
</div>
<?php } ?>
</body>
<?php global $hooks;$hooks->do_action('FooterEvent'); ?>