<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

if(userRole($userID) > 2) {
	header('location: admin.php');
}

$base_url = smsGatewayData($school_id,'base_url'); 
$success_word = smsGatewayData($school_id,'success_word'); 
$success_logic= smsGatewayData($school_id,'success_logic'); 

if(empty($base_url) || $success_word=='0' || empty($success_logic)) {
	header('location: smsgateway?msg');
}
//reset forms
$customer = $userID;
$recipientList = '';
$senderID = getSetting('SMS_sender');
$country = '';
$textMessage = '';
$date = date('Y-m-d');
$hour = date('H');
$mins = date('i');
$schedule = '';

if(isset($_POST['textMessage'])) {
	foreach ($_POST as $key => $value ){
		${$key} = mysqli_real_escape_string($server,$value) ;
	}

	switch($email_to) {
	case 'e':
	$sql=$query = "SELECT phone FROM users WHERE school_id = '$school_id'";
	break;

	case 'm':
	$sql=$query = "SELECT phone FROM users WHERE role_id = '2' AND school_id = '$school_id'";
	break;

	case 'p':
	$sql=$query = "SELECT phone FROM users WHERE role_id = '5' AND school_id = '$school_id'";
	break;

	case 's':
	$sql=$query = "SELECT phone FROM users WHERE role_id = '6' AND school_id = '$school_id'";
	break;

	case 't':
	$sql=$query = "SELECT phone FROM users WHERE role_id = '4' AND school_id = '$school_id'";
	break;

	case 'o':
	$sql=$query = "SELECT phone FROM users WHERE role_id = '3' OR role_id = '7' OR  role_id = '8' OR role_id = '9' AND school_id = '$school_id'";
	break;

	default:
	$sql=$query = "SELECT phone FROM users WHERE id = '$email_to' AND school_id = '$school_id'";
	break;
	}
	if(isset($class)) {
		$sql=$query = "SELECT u.phone FROM users u JOIN student_class s ON u.profile_id = s.student_id WHERE s.class_id = '$class' ";
	}
	 $MailtoDelimiter = ",";
		$rphonelist = mysqli_query($server, $query);
		$phonelist = '';
		while (list ($phone) = mysqli_fetch_row($rphonelist)) {
		    $sPhone = $phone;
		    if($sPhone) {
	        	if($phonelist) {
	            	$phonelist .= $MailtoDelimiter;
				}
	        	if (!stristr($phonelist, $sPhone)) {
	            	$phonelist .= $sPhone;
				}
	    	}
		}
	$recipientList = $phonelist;

	if(empty($recipientList)) {
		$_SESSION['message'] = 'Oops! You must provide atleast 1 recipient.';
		$_SESSION['color'] = 'yellow';
		$error = 1;
    }
	$now = date('Y-m-d H:i:s', time());
	$recipientList = $nn = preg_replace("/[^0-9+,]/", "", $recipientList );
	$recipientList = mysqli_real_escape_string($server, $recipientList);
	//save to DB for queuing
	mysqli_query($server, "INSERT INTO sentmessages (`message`,`recipients`, `customer_id`, `date`, `sender_id`, `status`, `to_count`) VALUES ('$textMessage','$recipientList', '$school_id', '$now', '$senderID', 'queued', '1');") or die(mysqli_error($server));
	
	$_SESSION['message'] = 'Your message was successfully queued and will be processed shortly.';
	$_SESSION['color'] = 'green';
	header('location: sms?done=1');
}

//set message
if(isset($_GET['done'])) {
	$message = 	$_SESSION['message'];
	$class = $_SESSION['color'];
}

if(!isset($_REQUEST['new']) && !isset($_REQUEST['id']) && !isset($_REQUEST['class'])) {
	if(isset($_REQUEST['v'])) {
		$message_id = filterinp($_REQUEST['v']);
		$sql=$query = "SELECT * FROM messagedetails WHERE message_id = '$message_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")	{
		$message = "No records found! Maybe the message is still being processed";
		$class="blue";
	}
?>
    <div class="wrapper">
            <div id="mess" style="position: relative; top: 0;">
                <?php if(!empty($message)) { showMessage($message,$class); } ?>
            </div>
        <div id="search-pan">
        	<a href="admin/sms"><button type="button" class="submit">Back to SMS Log</button></a>
        </div>
        <div class="panel" style="border-color: transparent;">
            <div class="panel-body">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <th width="5%" scope="col">ID</th>
                    <th width="15%" scope="col">Phone</th>
                    <th width="40%" scope="col">Message</th>
                    <th width="15%" scope="col">Status</th>
                    <th width="15%" scope="col">Error</th>
                  </tr>
                   <?php		while($row=mysqli_fetch_assoc($result)){	?>            
                  <tr class="inner">
                    <td width=""> <?php echo $row['id']; ?></td>
                    <td width=""> <?php echo $row['recipient']; ?></td>
                    <td width=""> <?php echo $row['message']; ?></td>
                    <td width=""> <?php echo ucfirst($row['status']); ?></td>
                    <td width=""> <?php echo strip_tags($row['notice']); ?></td>
                  </tr>
                  <?php	} ?>
                  </table>
    <?php displayPagination($setLimit,$page,$sql) ?>
            </div>
        </div>
    </div>    
<?php		
	} else {
	$sql=$query = "SELECT * FROM sentmessages WHERE customer_id = '$school_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")	{
		$message = "No sent message records found!";
		$class="blue";
	}
?>
<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<a href="admin/sms?new"><button type="button" class="submit">Compose SMS</button></a>
    </div>
    <div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="5%" scope="col">ID</th>
                <th width="15%" scope="col">Date</th>
                <th width="15%" scope="col">Sender ID</th>
                <th width="35%" scope="col">Message</th>
                <th width="10%" scope="col">Recipients</th>
                <th width="10%" scope="col">Status</th>
                <th width="10%" scope="col"></th>
              </tr>
               <?php		while($row=mysqli_fetch_assoc($result)){	?>            
              <tr class="inner">
                <td width=""> <?php echo $row['id']; ?></td>
                <td width=""> <?php echo $row['date']; ?></td>
                <td width=""> <?php echo $row['sender_id']; ?></td>
                <td width=""> <?php echo $row['message']; ?></td>
                <td width=""> <?php echo substr_count($row['recipients'], ",") +1;; ?></td>
                <td width=""> <?php echo ucfirst($row['status']); ?></td>
                <td width=""> <a href="admin/sms?v=<?=$row['id']?>"><button>View</button></a></td>
              </tr>
              <?php	} ?>
              </table>
<?php displayPagination($setLimit,$page,$sql) ?>
        </div>
    </div>
</div>    
<?php } ?>
<?php } else {
if($senderID == 'not available')
$senderID = '';
?>
<script>
//function to chect sender id lenght
	function senderLenght() {
		var sender = document.getElementById('senderID').value.lenght;
		if (sender > 1)	{
			alert('Your Sender ID cannt be longer than 11 characters!');
		} 
	}
</script>
<div class="wrapper">
	<div class="inner-left" style="width: 100%;">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
    <form method="post" action="" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <!-- Destination -->
        <td align="left" valign="middle"><strong>Send SMS To</strong>:</td>
        <td  align="left" valign="middle">
         <select name='email_to' id='to' style="width:100%">
        	<?php if(isset($_REQUEST['id'])) {?>
            <option value='<?php echo $_REQUEST['id'];?>' > <?php echo userFullName($_REQUEST['id'])?></option>
            <?php } elseif(isset($_REQUEST['class'])) {?>
            <option value='<?php echo $_REQUEST['class'];?>' > <?php echo className($_REQUEST['class'])?></option>
            <?php } else {?>
        	<option value='e' > Every Body</option>
            <option value='m' > Managers</option>
            <option value='t' > Teachers</option>
            <option value='s' > Students</option>
            <option value='p' > Parents</option>
            <option value='o' > Other Staffs</option>
            <?php } ?>
            </select>
            <?php if(isset($_REQUEST['class'])) {?>
            	<input type="hidden" name="class" value="<?php echo $_REQUEST['class']; ?>" />
            <?php } ?>
        </td>
      </tr>
      <!-- Sender ID -->
      <tr>
        <td align="left" valign="middle"><strong>Sender ID</strong>:</td>
        <td  align="left" valign="middle">
        	<input type="text" onkeyup="senderLenght();" name="senderID" id="senderID" value="<?php echo $senderID; ?>" maxlength="11" required="required" placeholder="Maximun of 11 alpha-numeric characters">
        </td>
      </tr>
      <!-- To Numbers surce -->
      <!-- Message -->
      <tr>
        <td align="left" valign="top"><strong>Message</strong>:<br><small>160 Characters = 1 SMS<small></td>
        <td align="left" valign="top">
            	<textarea onBlur="count2(this,this.form.countBox2,1000);" onKeyUp="count2(this,this.form.countBox2,1000);" id="message" name="textMessage"  required ><?php echo $textMessage; ?></textarea><br>
			<input readonly onFocus="this.blur();" name="countBox2" value = "0 Characters Used" id="countBox2" />
<script>
//count message lenght
	function count2(field,countfield,maxlimit) {
		var draftBox = document.getElementById('draftBox');
		var draftRecipient = document.getElementById('draftRecipient');
		var field2 = document.getElementById('recipientList');
	if (field.value.lenght > 1000) {
		field.value = field.value.substring(0,1000);
		field.blur();
		return false;
	} else {
		var pages = field.value.length /160;
			if(pages < 1) { var page = '1';	}
			if(pages > 1) { var page = '2';	}
			if(pages > 2) {	var page = '3';	}
			if(pages > 3) {	var page = '4';	}
			if(pages > 4) {	var page = '5';	}
			if(pages > 5) {	var page = '6';	}
			if(pages > 6) {	var page = '7';	}

		countfield.value = field.value.length + " of 1000 Characters Used ("+page+" SMS)";
		}
		draftBox.value = field.value;
		draftRecipient.value = field2.value;
	}
</script>
        </td>
      </tr>

      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td  align="left" valign="top">
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="sendMessage" value="1" type="submit">Send Message</button>
        <button class="submit btn-warning" type="reset">Cancel</button>
        <a href="admin/sms"><button type="button" class="btn btn-danger">Back to SMS Log</button></a>
	</form>

     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Sending...</div>
        </td>
      </tr>
    </table>

  </div>
</div>
<?php } ?>