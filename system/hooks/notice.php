<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;


global $server;

if(userRole($userID) > 2 && userRole($userID) != 4) {
header('location: adashboard');
}

if(isset($_POST['text'])) {
	foreach ($_POST as $key => $value ){
		${$key} = mysqli_real_escape_string($value) ;
	}
	if($user_id > 0) {
		$user = $user_id;
	}
	if($class_id > 0) {
		$class = $class_id;
	}

	switch($email_to) {
	case 'e':
	$role_id = 0;
	$class_id = 0;
	$user_id = 0;
	break;

	case 'm':
	$role_id = 2;
	$class_id = 0;
	$user_id = 0;
	break;

	case 'p':
	$role_id = 5;
	$class_id = 0;
	$user_id = 0;
	break;

	case 's':
	$role_id = 6;
	$class_id = 0;
	$user_id = 0;
	break;

	case 't':
	$role_id = 4;
	$class_id = 0;
	$user_id = 0;
	break;

	case 'o':
	$role_id = 9;
	$class_id = 0;
	$user_id = 0;
	break;

	default:
	$role_id = 0;
	$class_id = 0;
	$user_id = 0;
	break;
	}
	if(isset($class)) {
	$role_id = 6;
	$class_id = $class;
	$user_id = 0;
	}
	if(isset($user)) {
	$role_id = 0;
	$class_id = 0;
	$user_id = $user;
	}
	$text = nl2br(mysqli_real_escape_string($server,$_POST['text']));
	$date = date('Y-m-d');
	$sql=$query="INSERT INTO notice (`id`, `school_id`, `title`, `date`, `text`, `role_id`, `user_id`, `class_id`) 	VALUES (NULL, '$school_id', '$title', '$date', '$text', '$role_id', '$user_id', '$class_id');";
	mysqli_query($server, $query) or die (mysqli_error($server));

	$_SESSION['message'] = 'Your message was successfully sent.';
	$_SESSION['color'] = 'green';
	header('location: notice?done');
}


//set message
if(isset($_GET['done'])) {
	$message = 	$_SESSION['message'];
	$class = $_SESSION['color'];

}
$subject = '';
$textMessage = '';
?>

<div class="wrapper">
	<div class="inner-left" style="width:100%;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
    <form method="post" action="" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
<?php if(userRole($userID) < 3) {  ?>
      <!-- send to role ID -->
        <td  align="left" valign="middle">Send by Role:</td>
        <td  align="left" valign="middle">
        <select name='email_to' id='to' >
        	<option value='e' > Every Body</option>
            <option value='m' > Managers</option>
            <option value='t' > Teachers</option>
            <option value='s' > Students</option>
            <option value='p' > Parents</option>
            <option value='o' > Other Staffs</option>
        </select>
        </td>
      </tr>

      <!-- send to class ID -->
        <td align="left" valign="middle">Send to Class:</td>
        <td align="left" valign="middle">
        <select name='class_id' id='e1' style="width: 98%">
        	<option value='0' > Select Class</option>
			<?php
                $sql=$query="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $c_id = $row['id'];
				$title = $row['title'];
            ?>
            <option value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php
									$i++;
								} ?>
        </select>
        </td>
      </tr>

      <!-- send to class ID -->
        <td align="left" valign="middle">Send to Selected User:</td>
        <td align="left" valign="middle">
        <select name='user_id' id='e2' style="width: 98%;">
        	<option value='0' > Select User</option>
			<?php
                $sql=$query="SELECT * FROM users WHERE school_id = '$school_id' ORDER BY name ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $c_id = $row['id'];
				$title = $row['name'];
            ?>
            <option value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php
									$i++;
								} ?>
        </select>
        </td>
      </tr>
<?php } else { ?>
	<input type="hidden" name="class_id" value="<?php echo getTeacherClass(adminData('profile_id', $userID)); ?>" />
    <input type="hidden" name="email_to" value="0" />
    <input type="hidden" name="user_id" value="0" />
<?php } ?>
      <tr>
        <td align="left" valign="middle">Subject:</td>
        <td  align="left" valign="middle">
        	<input type="test"  name="title" id="senderID" value="" required="required" placeholder="Type your subject here">
        </td>
      </tr>

      <!-- Message -->
      <tr>
        <td align="left" valign="top">Message:</td>
        <td  align="left" valign="top">
            	<textarea id="message"  name="text"  style="height: 300px;" required ></textarea>
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="sendMessage" value="1" type="submit">Create Notice</button>
        <button class="submit" type="reset">Cancel</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Creating Notice...</div>
        </td>
      </tr>
    </table>

  </div>
</div>