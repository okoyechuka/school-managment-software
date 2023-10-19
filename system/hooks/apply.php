<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

$currentSession = getSetting('current_session');
$currentTerm = getSetting('current_term');

global $server;

$state = '';
$date = date('Y-m-d');
global $hooks;
$hooks->do_action('ApplicantDashboardBefore'); 
			
if(isset($_POST['applicant'])) {
	$applicant = mysqli_real_escape_string($server, $_POST['applicant']);
	foreach ($_POST as $key => $value ){
		if(($key !== "applicant") && ($key !== "save") && ($key !== "admit") && ($key !== "photo")) {
			$value = mysqli_real_escape_string($server, $value);
			$sql=$query="UPDATE applicants SET `$key` =  '$value' WHERE id = '$applicant'";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}
	$upload_path = 'media/uploads/';
	$file1 = $_FILES['photo']['name'];
	$filename1 = date("d-m-Y").$_FILES['photo']['name'];
	$ext = strtolower(end(explode(".", $_FILES['photo']['name'])));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
	} else {
		$applicant = $_POST['applicant'];
		//update photo if set
		if($file1 !=="") {
			move_uploaded_file($_FILES['photo']['tmp_name'],$upload_path . $filename1);
			$sql=$query="UPDATE `applicants` SET `photo` =  '$filename1' WHERE `id` = '$applicant'";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
		$application_number = getApplicationNumber($school_id,date('Y'));
		if(!isset($_POST['application_number'])) {
			$sql=$query="UPDATE `applicants` SET `application_number` =  '$application_number' WHERE `id` = '$applicant'";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}

		$sql=$query="UPDATE `applicants` SET `session_id` =  '$currentSession' WHERE `id` = '$applicant'";
		mysqli_query($server, $query) or die(mysqli_error($server));
		$sql=$query="UPDATE `applicants` SET `application_date` =  '$date' WHERE `id` = '$applicant'";
		mysqli_query($server, $query) or die(mysqli_error($server));
		$sql=$query="UPDATE `applicants` SET `status` =  'Pending' WHERE `id` = '$applicant'";
		mysqli_query($server, $query) or die(mysqli_error($server));

		$message = 'Congratulations!<br>Your application was successfully submited. Further instructions will be communicated to you later. Your application number is "'.$application_number.'". Please keep this for future reference.';
		$class = 'green';
	}
}
?>
<div class="wrapper">
	<div class="inner-left" style="width: 100%;">
    <?php if(!empty($message)) { showMessage($message,$class); } ?> 
<?php

if(isset($_GET['status'])) {
//display confirmation
	$sql=$query="SELECT * FROM applicants WHERE id = '$userID'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);

	$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}
	$status = '<strong>Awaiting Approval</strong>';
	if($row['status'] == 'Accepted') {
		$status = '<blue>Application Accepted</blue>';
	}
	if($row['status'] == 'Rejected') {
		$status = '<red>Application Rejected</red>';
	}
	if($row['status'] == 'Admitted') {
		$status = '<green>Admitted</green>';
	}
	if(empty($row['application_number'])) {
		showMessage('You are yet to submit your application!<br>You can only view application status after your application has been saved.','yellow');
	}else {
?>
   	<div class="action-box" style="float: right;">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print Slip</button></a>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      	<td colspan="2">
                <div id="sch-name2" style="text-align: center; color: #036; font-weight:bold; font-size:18px;"><?php echo getSetting('name'); ?><br /><black style="font-size: 14px; color: black;"><?php echo getSetting('address').', '.getSetting('city').', '.getSetting('state'); ?></black><br /></div>
                <hr>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><img src="media/uploads/<?php echo $picture; ?>" style="width: 70%; height: auto; border: 2px solid #999;"/></td>
        <td  align="left" valign="top">
        	<table width="100%">
 	          	<tr>
                	<td width="30%"><strong>Applicant's Full Name:</strong></td>
                    <td><?php echo $row['first_name'].' '.$row['last_name'].' '.$row['other_name']; ?></td>
                </tr>

                <tr>
                	<td><strong>Application Number: </strong></td>
                    <td><?php echo $row['application_number']; ?></td>
                </tr>
                <tr>
                	<td><strong>Application Date: </strong></td>
                    <td><?php echo $row['application_date']; ?></td>
                </tr>
                <tr>
                	<td><strong>Application Status:</strong></td>
                    <td><?php echo $status; ?></td>
                </tr>
            </table>
        </td>
      </tr>
      </table>
            <hr />
      </div>
<?php
		}
} else {
	$sql=$query="SELECT * FROM applicants WHERE id = '$userID'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);

	$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}
	if(!empty($row['application_number']) && !isset($_GET['edit'])) {
		//display profile
	?>
        	<div class="action-box" style="float: right;">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print Credentials</button></a>
            <?php if($date < getSetting('register_close_date')) {?>
            <a href="index.php?edit=<?php echo $student; ?>&done"><button class="submit">Update Application</button></a>
            <?php } ?>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle"><img src="media/uploads/<?php echo $picture; ?>" style="width: 70%; height: auto; border: 2px solid #999;"/></td>
        <td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><strong>Full Name</strong>:</td>
        <td  align="left" valign="middle"><?php echo $row['last_name'].' '.$row['first_name'].' '.$row['other_name']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><strong>Gender</strong>:</td>
        <td  align="left" valign="middle"><?php echo $row['sex']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><strong>Date of Birth</strong>:</td>
        <td  align="left" valign="middle"><?php echo $row['date_of_birth']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left"  colspan="2" class="tr-heading" valign="middle"><strong>Contact Information</strong></td>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><strong>Residence Address</strong>:</td>
        <td  align="left" valign="middle"><?php echo $row['address'].', '.$row['city']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><strong>Local Council / LGA</strong>:</td>
        <td  align="left" valign="middle"><?php echo $row['local_council']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><strong>State of Residence</strong>:</td>
        <td  align="left" valign="middle"><?php echo $row['state']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><strong>State of Origin</strong>:</td>
        <td  align="left" valign="middle"><?php echo $row['state_origin']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><strong>Country of Residence</strong>:</td>
        <td  align="left" valign="middle"><?php echo countryName($row['country']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><strong>Nationality</strong>:</td>
        <td  align="left" valign="middle"><?php echo countryName($row['nationality']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><strong>Phone</strong>:</td>
        <td  align="left" valign="middle"><?php echo $row['phone']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left"  colspan="2" class="tr-heading"  valign="middle"><strong>Other Information</strong></td>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle"><strong>Application Number</strong>:</td>
        <td  align="left" valign="middle"><?php echo $row['application_number']; ?> </td>
      </tr>

   </table>
</div>
<?php
	} else {
		if(time() >= strtotime(getSetting('register_close_date')) && strtotime(getSetting('register_close_date'))>0) {
			showMessage('Sorry but submission of new students application has closed as of '.date('d F Y',strtotime(getSetting('register_close_date'))).'.<br>Kindly contact the school administration for further information.','yellow');
		} else {
			//display application form
?>
    <form method="post" action="apply.php" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->

      <tr>
        <td  colspan="2" class="tr-heading" align="left" valign="middle"><strong>Personal Information</strong></td>
        </td>
       </tr>
       <tr>
        <td align="left" valign="middle"><img src="media/uploads/<?php echo $picture; ?>" style="width: 70%; height: auto; border: 2px solid #999;"/>
        <input type="file" accept="image/gif,image/png,image/jpg" style="width: 70%" name="photo" id="photo" ></td>
        <td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" valign="middle">First Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="first_name" id="senderID" required="required" placeholder="" value="<?php echo @$row['first_name']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Last Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="last_name" id="senderID" required="required" placeholder="" value="<?php echo @$row['last_name']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Other Names:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="other_name" id="senderID"  placeholder="Other name or Initial" value="<?php echo @$row['other_name']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Gender:</td>
        <td  align="left" valign="middle">
        <select name="sex" id="sex" style="width:90%" >
               <option <?php if(@$row['sex'] == 'Male') { echo 'selected';} ?> value="Male"><?php echo 'Male'; ?></option>
               <option <?php if(@$row['sex'] == 'Female') { echo 'selected';} ?> value="Female"><?php echo 'Female'; ?></option>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Date of Birth:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="date_of_birth" id="date_of_birth" required="required" placeholder="" value="<?php echo @$row['date_of_birth']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td  colspan="2" class="tr-heading" align="left" valign="middle"><strong>Contact Information</strong></td>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Residence Address:</td>
        <td  align="left" valign="middle">
        	<input type="text" name="address" id="address" required="required" placeholder="" value="<?php echo @$row['address']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">City:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="city" id="city" required="required" placeholder="" value="<?php echo @$row['city']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Local Council / LGA <small>(Optional)</small>:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="local_council" id="council" placeholder="Local Government Area " value="<?php echo @$row['local_council']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">State of Residence:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="state" id="state" required="required" placeholder="" value="<?php echo @$row['state']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">State of Origin:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="state_origin" id="state_origin" required="required" placeholder="" value="<?php echo @$row['state_origin']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Country of Residence:</td>
        <td  align="left" valign="middle">
        	<select name="country" required="required" style="width: 90%;"><?php echo getCountryList($row['country']); ?></select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Nationality:</td>
        <td  align="left" valign="middle">
        	<select name="nationality"  required="required" style="width: 90%;"><?php echo getCountryList($row['nationality']); ?></select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Phone Number:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="phone" id="phone"  placeholder="" value="<?php echo @$row['phone']; ?>">
        </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="applicant" value="<?php echo $row['id']; ?>" />
        <?php if(!empty($row['application_number'])) { ?>
        <input type="hidden" name="application_number" value="<?php $row['application_number']; ?>" />
        <?php } ?>
        <input type="hidden" name="save" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Submit Application</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Application...</div>
        </td>
      </tr>
    </table>
	</form>
<?php
		}
	}
}

global $hooks;
$hooks->do_action('ApplicantDashboardAfter'); 
?>
  </div>
</div>
