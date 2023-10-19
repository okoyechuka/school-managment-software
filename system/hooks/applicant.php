<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		student.php
Description:	This is the students page
Developer: 		Ynet Interactive
Date: 			10/11/2014
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

if(userRole($userID) > 2) {
header('location: admin.php');
}


if(isset($_REQUEST['sms']))
{
$senderID = '';
?>

<div id="add-new">
   <div id="add-new-head">Send SMS to Applicants
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
            <form action="admin/pplicant" method="post">
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Send SMS to: </td>
        <td align="left" width="60%" valign="middle">
        <select name='email_to' id='to' >
            <option value='1' > All Applicants</option>
            <option value='2' > Accepted Applicants</option>
            <option value='3' > Rejected Applicants</option>
          </select>
		</td>
      </tr>
      <tr>
        <td align="left" valign="middle">Session: </td>
        <td  align="left" valign="middle">
        <select name='session_id' id='e1' >
            <?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY start_date DESC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
		                $g_id = $row['id'];
		                $title0 = $row['title'];
		            		?>
			               <option value="<?php echo $g_id; ?>" <?php if(getSetting('current_session') == $g_id) {echo 'selected';} ?>><?php echo $title0; ?></option>
										 $i++
            				<?php
								}
			 if($num < 1) { ?>
               <option value="" >You need to Create a Session first</option>
             <?php }
			 ?>
          </select>
		</td>
      </tr>
      <tr>
        <td align="left" valign="middle">Sender ID </td>
        <td  align="left" valign="middle"><input type="text" onkeyup="senderLenght();" name="senderID" id="senderID" value="<?php echo $senderID; ?>" maxlength="14" required="required" placeholder="Maximun of 14 alpha-numeric characters"> </td>
      </tr>
      <tr>
        <td align="left" valign="top">Message: <br><small>160 Characters = 1 SMS<small></td>
        <td  align="left" valign="top">
		<textarea onBlur="count2(this,this.form.countBox2,1000);" onKeyUp="count2(this,this.form.countBox2,1000);" id="message" name="textMessage"  required ><?php echo @$textMessage; ?></textarea>
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
		var pages = field.value.length /158;
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
        <td align="left" valign="middle"></td>
        <td  align="left" valign="middle">
		<button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="sendMessage" value="1" type="submit">Send Message</button>
        <button class="submit" type="reset">Cancel</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Sending SMS...</div>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      </table>
      </form>

</div>
</div>


 <?php
}

if(isset($_POST['textMessage'])) {

	foreach ($_POST as $key => $value ){
		${$key} = $value ;
	}

	switch($email_to) {
	case '1':
	$sSQL = "SELECT phone FROM applicants WHERE school_id = '$school_id' AND application_number != '' AND session_id = '$sesstion_id'";
	break;

	case '2':
	$sSQL = "SELECT phone FROM applicants WHERE status = 'Accepted' AND school_id = '$school_id' AND application_number != '' AND session_id = '$sesstion_id'";
	break;

	case '3':
	$sSQL = "SELECT phone FROM applicants WHERE status = 'Pending' AND school_id = '$school_id' AND application_number != '' AND session_id = '$sesstion_id'";
	break;
	}
	 $MailtoDelimiter = ",";
		$rphonelist = mysqli_query($server, $sSQL);
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

	$recipientList = $nn = preg_replace("/[^0-9+,]/", "", $recipientList );
	$recipientList =  str_replace(' ', '', $recipientList);
	//$recipientList = strtr ($recipientList, array ('+' => ''));
			$batchSize = '300';
			$recipientcount = explode(',',$recipientList);

			if(count($recipientcount) > $batchSize){
				for($q=0; $q < count($recipientcount); $q+=$batchSize){
					$tempRecipientsList = array();
					for($w=0; $w<$batchSize; $w++){
						if(!empty($recipientcount[$q+$w])) {
							$tempRecipientsList[] = $recipientcount[$q+$w];
						}
					}
					$recipientList2 = implode(',',$tempRecipientsList);
					$sendingResponse = sendMessage($senderID,$recipientList2,$textMessage);
				}
			}
			else
			{
			$sendingResponse = sendMessage($senderID,$recipientList,$textMessage);
			}

			if(strpos($sendingResponse,'Message Sent Successfully') !== false) {
				$_SESSION['message'] = 'Your message was successfully sent.';
				$_SESSION['color'] = 'green';
			} else {
				$_SESSION['message'] = $sendingResponse;
				$_SESSION['color'] = 'red';
			}
			header('location: '.BASE.'applicant?done='.$source.'');

}

//set message
if(isset($_GET['done'])) {
	$message = 	$_SESSION['message'];
	$class = $_SESSION['color'];

}

if(isset($_REQUEST['view']))
{
	$student = $_REQUEST['view'];
		//get students profile
		$sql=$query="SELECT * FROM applicants WHERE id = '$student'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		$name = $row['first_name'];
		$id = $row['id'];
		if(empty($picture)) {
			$picture = 'media/uploads/no-body.png';
		}

?>
<div id="add-new">
   <div id="add-new-head"><?php echo $name; ?>'s Application Details
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
        	<div class="action-box">
            <form action="admin/admit" method="post">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit" type="button">Print Credentials</button></a>
            <?php if(userRole($userID) < 3) {?>
            	<input type="hidden" name="first_name" value="<?php echo $row['first_name'] ?>" />
                <input type="hidden" name="last_name" value="<?php echo $row['last_name'] ?>" />
                <input type="hidden" name="other_name" value="<?php echo $row['other_name'] ?>" />
                <input type="hidden" name="sex" value="<?php echo $row['sex'] ?>" />
                <input type="hidden" name="date_of_birth" value="<?php echo $row['date_of_birth'] ?>" />
                <input type="hidden" name="address" value="<?php echo $row['address'] ?>" />
                <input type="hidden" name="city" value="<?php echo $row['city'] ?>" />
                <input type="hidden" name="local_council" value="<?php echo $row['local_council'] ?>" />
                <input type="hidden" name="state" value="<?php echo $row['state'] ?>" />
                <input type="hidden" name="country" value="<?php echo $row['country'] ?>" />
                <input type="hidden" name="nationality" value="<?php echo $row['nationality'] ?>" />
                <input type="hidden" name="state_origin" value="<?php echo $row['state_origin'] ?>" />
                <input type="hidden" name="photo" value="<?php echo $row['photo'] ?>" />
                <input type="hidden" name="phone" value="<?php echo $row['phone'] ?>" />
                <input type="hidden" name="applicant_id" value="<?php echo $row['id'] ?>" />
                <input type="hidden" name="transfer_student" value="<?php echo $row['id'] ?>" />
           		<?php if($row['status'] !='Admitted') { ?>  <button type="submit" class="submit">Admit <?php echo $name; ?></button> <?php } ?>
            <?php if($row['status'] =='Pending') { ?><a href="admin/applicant?reject=<?php echo $id; ?>&done"><button type="button" class="submit">Reject Application</button></a><?php } ?>
            <?php }?>
            </form>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle"><img src="<?php echo $picture; ?>" style="width: 70%; height: auto; border: 2px solid #999; max-width: 200px;"/></td>
        <td width="60%" align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Application Status: </td>
        <td  align="left" valign="middle"><?php echo $row['status']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Application Date: </td>
        <td  align="left" valign="middle"><?php echo date('d M, Y', strtotime($row['application_date'])); ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Application Number: </td>
        <td  align="left" valign="middle"><?php echo $row['application_number']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Full Name:</td>
        <td  align="left" valign="middle"><?php echo $row['last_name'].' '.$row['first_name'].' '.$row['other_name']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Gender:</td>
        <td  align="left" valign="middle"><?php echo $row['sex']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Date of Birth:</td>
        <td  align="left" valign="middle"><?php echo $row['date_of_birth']; ?></td>
      </tr>

      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2" class="tr-heading" valign="middle"><strong>Contact Information</strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Residence Address:</td>
        <td  align="left" valign="middle"><?php echo $row['address'].', '.$row['city']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Local Council / LGA:</td>
        <td  align="left" valign="middle"><?php echo $row['local_council']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">State of Residence:</td>
        <td  align="left" valign="middle"><?php echo $row['state']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">State of Origin:</td>
        <td  align="left" valign="middle"><?php echo $row['state_origin']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Country of Residence:</td>
        <td  align="left" valign="middle"><?php echo $row['country']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Nationality:</td>
        <td  align="left" valign="middle"><?php echo $row['nationality']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Phone:</td>
        <td  align="left" valign="middle"><?php echo $row['phone']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2" class="tr-heading" valign="middle"><strong>Other Information</strong></td>
      </tr>
      <?php if(!empty($row['application_type'])) { ?>
      <tr>
        <td align="left" valign="middle">Application Type:</td>
        <td align="left" valign="middle"><?php echo $row['application_type']; ?> Student</td>
      </tr>
      <?php } 
	  if(strtolower($row['application_type'])=='transfer') { ?>
      <tr>
        <td align="left" valign="middle">Current School:</td>
        <td align="left" valign="middle"><?php echo $row['school_name']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">School Address:</td>
        <td  align="left" valign="middle"><?php echo $row['school_address']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Current Class:</td>
        <td  align="left" valign="middle"><?php echo $row['current_class']; ?> </td>
      </tr>
      <?php } ?>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      </table>
  </div>

</div>
</div>
 <?php
}

 if(isset($_REQUEST['delete']) && !empty($_REQUEST['delete'])) {
	$student = filterinp($_REQUEST['delete']);

	$sql=$query="DELETE FROM `applicants` WHERE `id` = '$student';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$message = 'The selected applicantion has been deleted from the system.';
	$class = 'green';
}

 if(isset($_REQUEST['rejected']) && !empty($_REQUEST['rejected'])) {
	$student = $_REQUEST['rejected'];

	$sql=$query="UPDATE `applicants` SET `status` =  'Rejected' WHERE `id` = '$student';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$message = 'The selected applicant has been rejected.';
	$class = 'green';
}
 if(isset($_REQUEST['accepted']) && !empty($_REQUEST['accepted'])) {
	$student = $_REQUEST['accepted'];

	$sql=$query="UPDATE `applicants` SET `status` =  'Accepted' WHERE `id` = '$student';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$message = 'The selected applicant has been accepted.';
	$class = 'green';
}

if(isset($_POST['accept'])) {
	foreach($_POST['applicant'] as $num => $applicant) {
		$sql=$query="UPDATE `applicants` SET `status` =  'Accepted' WHERE `id` = '$applicant';";
		mysqli_query($server, $query) or die(mysqli_error($server));
	}
	$message = 'The selected applicants were succesfully Accepted. <br>Click the Admit button beside each applicant to start enrollment.';
	$class = 'green';
}

if(isset($_POST['reject'])) {
	foreach($_POST['applicant'] as $num => $applicant) {
		$sql=$query="UPDATE `applicants` SET `status` =  'Rejected' WHERE `id` = '$applicant';";
		mysqli_query($server, $query) or die(mysqli_error($server));
	}
	$message = 'The selected applicants were succesfully rejected.';
	$class = 'green';
}

$addQuery = '';
if(isset($_GET['session_id'])) {
	$sesstion_id = filterinp($_GET['session_id']);
	$addQuery = ' AND session_id = '.$sesstion_id.' ';
	if($_GET['session_id'] < 1) {
		$addQuery = '';
	}
} else {
	$addQuery = ' AND session_id = '.$currentSession.' ';
}

if(isset($_GET['keyword']))
{
$school_id = $school_id;

$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', mysqli_real_escape_string($server,$_GET['keyword']));
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "first_name LIKE '%$term%' OR last_name LIKE '%$term%' OR other_name LIKE '%$term%' OR application_number LIKE '%$term%' OR state LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "first_name LIKE '%%' OR address LIKE '%%' OR sex LIKE '%%' OR state LIKE '%%' OR last_name LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query= "select * FROM applicants WHERE school_id = '$school_id' AND application_number != '' AND $filter $addQuery ";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM applicants WHERE school_id = '$school_id' AND application_number != '' $addQuery ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "No applicant records found for the current school session!";
		$class="blue";
		}
}

?>

<script>
$(document).ready(function() {
    $('#selectall').click(function(event) {  //on click
        if(this.checked) { // check select status
            $('.checkbox1').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"
            });
        }else{
            $('.checkbox1').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"
            });
        }
    });

});
</script>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    <form action="" method="get">
    	<select name="session_id" style="margin-right: 5px;">
        	<option value="<?php echo $currentSession; ?>"><?php echo sessionName($currentSession); ?></option>
            <option value="0">All Sessions</option>
            <option value="" selected disabled>Select Session</option>
        </select>
        <input type="search" name="keyword" placeholder="Search Applicants"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
    <div id="search-pan" style="text-align: left;">
	<form onsubmit="return confirm('Are you sure you want to accept all selected applicants?\nThis action will mark every other applicants as Rejected.');" action="admin/applicant" method="post" id="applicantAccept">
        <?php if(userRole($userID)<3) { ?>
        <button type="submit" class="submit" name="accept">Accept Selected</button>
        <button type="submit" class="submit" name="reject">Reject Selected</button>
        <?php } ?>
        <a href="admin/applicant?sms"><button type="button" class="submit">Send SMS</button></a>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="6.5%" scope="col"><input type="checkbox" id="selectall"/></th>
                <th width="25%" scope="col">Full Name</th>
                <th width="15%" scope="col">Applicant Number</th>
                <th width="10%" scope="col">Date</th>
                <th width="10%" scope="col">Type</th>
                <th width="10%" scope="col">Status</th>
                <th width="20%" scope="col">Action</th>
              </tr>
             </table>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
						$id = $row['id'];
						$name = $row['last_name'].' '.$row['first_name'].' '.$row['other_name'];
						$application_number = $row['application_number'];
						$date = date('d/m/Y', strtotime($row['application_date']));
						$type = $row['application_type'];
						$status = $row['status'];
						if($status=='Accepted') { $tag1='<green>'; $tag2='<green>';}
						if($status=='Admitted') { $tag1='<green>'; $tag2='<green>';}
						if($status=='Rejected') { $tag1='<red>'; $tag2='</red>';}
						if($status=='Pending') { $tag1='<blue>'; $tag2='</blue>';}

						?>
             <div class="virtualpage hidepeice">
              <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner">
                <td align="center" width="6.5%"> <input class="checkbox1" type="checkbox" name="applicant[]" value="<?php echo $id; ?>"></td>
                <td width="25%"> <?php echo $name; ?></td>
                <td width="15%"> <?php echo $application_number; ?></td>
                <td width="10%"> <?php echo $date; ?></td>
                <td width="10%"> <?php echo $type; ?></td>
                <td width="10%"> <?php echo $tag1.$status.$tag2 ?></td>
                <td width="20%" valign="middle">
                <a href="admin/applicant?view=<?php echo $id;?>"><button type="button">View</button></a>
				<?php
				if(userRole($userID)<3 && $status !== 'Admitted') { ?>
			          <a href="admin/applicant?view=<?php echo $id;?>"><button type="button">Admit</button></a>
			         <a href="admin/applicant?delete=<?php echo $id;?>"><button class="danger" type="button">Delete</button></a>
			      <?php  ?>
			                </td>
			              </tr>
			              </table>
			              </div>
		              	<?php
									}
							$i++;
						} ?>
    </form>

<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
</div>
