<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

global $server;

if(userRole($userID) > 2) {
	header('location: index.php');
}

if(isset($_POST['textMessage'])) {
	foreach ($_POST as $key => $value ){
		${$key} = mysqli_real_escape_string($server, $value) ;
	}
	switch($email_to) {
	case 'e':
	$sql=$query = "SELECT email FROM users WHERE school_id = '$school_id'";
	break;

	case 'm':
	$sql=$query = "SELECT email FROM users WHERE role_id = '2' AND school_id = '$school_id'";
	break;

	case 'p':
	$sql=$query = "SELECT email FROM users WHERE role_id = '5' AND school_id = '$school_id'";
	break;

	case 's':
	$sql=$query = "SELECT email FROM users WHERE role_id = '6' AND school_id = '$school_id'";
	break;

	case 't':
	$sql=$query = "SELECT email FROM users WHERE role_id = '4' AND school_id = '$school_id'";
	break;

	case 'o':
	$sql=$query = "SELECT email FROM users WHERE role_id = '3' OR role_id = '7' OR  role_id = '8' OR role_id = '9' AND school_id = '$school_id'";
	break;

	default:
	$sql=$query = "SELECT email FROM users WHERE id = '$email_to' AND school_id = '$school_id'";
	break;
	}
	if(isset($class)) {
		$sql=$query = "SELECT u.email FROM users u JOIN student_class s ON u.profile_id = s.student_id WHERE s.class_id = '$class' ";
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
	$email_to = $phonelist;
	$emailFrom = mysqli_real_escape_string($server, getSetting('smtpUsername'));
	$emailSender = mysqli_real_escape_string($server, getSetting('name'));
	$to = rtrim($email_to,',');
	$to = mysqli_real_escape_string($server, $to);
	mysqli_query($server, "INSERT INTO emails (`customer_id`, `subject`, `message`, `recipient`, `status`, `address`, `sender`)  VALUES ('$school_id', '$subject', '$textMessage',  '$to','queued', '$emailFrom', '$emailSender');") or die (mysqli_error($server));
	
	$_SESSION['message'] = 'Your email message was successfully queued and will be processed shortly.';
	$_SESSION['color'] = 'green';
	header('location: email?done');
}

//set message
if(isset($_GET['done'])) {
	$message = 	$_SESSION['message'];
	$class = $_SESSION['color'];
}

if(!isset($_REQUEST['new']) && !isset($_REQUEST['id']) && !isset($_REQUEST['class'])) {
	$sql=$query = "SELECT * FROM emails WHERE customer_id = '$school_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
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
    	<a href="admin/email?new"><button type="button" class="submit">Compose Email</button></a>
    </div>
    <div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="5%" scope="col">ID</th>
                <th width="15%" scope="col">Date</th>
                <th width="35%" scope="col">Subject</th>
                <th width="15%" scope="col">Sender</th>
                <th width="15%" scope="col">Recipients</th>
                <th width="10%" scope="col">Status</th>
              </tr>
               <?php		while($row=mysqli_fetch_assoc($result)){	?>            
              <tr class="inner">
                <td width=""> <?php echo $row['id']; ?></td>
                <td width=""> <?php echo $row['date']; ?></td>
                <td width=""> <?php echo $row['subject']; ?></td>
                <td width=""> <?php echo $row['emailSender']; ?></td>
                <td width=""> <?php echo substr_count($row['recipient'], ",") +1;; ?></td>
                <td width=""> <?php echo ucfirst($row['status']); ?></td>
              </tr>
              <?php	} ?>
              </table>
<?php displayPagination($setLimit,$page,$sql) ?>
        </div>
    </div>
</div>    
<?php } else {
$subject = '';
$textMessage = '';
?>
<div class="wrapper">
	<div class="inner-left" style="width: 100%;">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
    <form method="post" action="" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <!-- Sender ID -->
        <td align="left" valign="middle"><strong>Send Email To</strong>:</td>
        <td align="left" valign="middle">
        <select name='email_to' id='to' style="width:90%">
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
      <tr>
        <td align="left" valign="middle"><strong>Email Subject</strong>:</td>
        <td  align="left" valign="middle">
        	<input type="test"  name="subject" id="senderID" value="" required="required" placeholder="Type your email subject here">
        </td>
      </tr>

      <!-- Message -->
      <tr>
        <td align="left" valign="top"><strong>Message</strong>:<br><small>Rich-text formats are HTML supported<small></td>
        <td  align="left" valign="top">
            	<textarea id="textMessage" class="ckeditor" name="textMessage"  style="height: 200px;" required ><?php echo $textMessage; ?></textarea>
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <button class="submit btn-success" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="sendMessage" value="1" type="submit">Send Mail</button>
        <button class="submit btn-warning" type="reset">Cancel</button>
        <a href="admin/email"><button type="button" class="btn btn-danger">Back to Email Log</button></a>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Sending Mail...</div>
        </td>
      </tr>
    </table>
  </div>
</div>
<?php } ?>