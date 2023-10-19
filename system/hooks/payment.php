<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		payment.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			28/02/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');


if(userRole($userID) > 3 && userRole($userID) != 7) {
header('location: admin.php');
}

	$session = getSetting('current_session');
	$term = getSetting('current_term');
	$class="0";

if (getSetting('current_session') < 1) {
$message = 'You have not defined the current accademic session yet!. <br>You must fix this before you can manage fees. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/generalsetting" title="Difine Active Session">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
}

if(isset($_REQUEST['delete']))
{
	$delete = filterinp($_REQUEST['delete']);
	if(isset($_REQUEST['yes'])) {
		$sql=$query = "DELETE FROM fees WHERE id = '$delete' ";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));

		$message = "The fee was successfully deleted.";
		$class="green";
	} else {
		$message = "Are you sure you want to delete the selected fee? <br><a href='admin/fee?delete=".$id."&yes=1'>Yes I'm sure</a> <a href='admin/fee'>Cancel</a>";
		$class="yellow";
	}
}


//view
if(isset($_GET['view']))
{

	if($_GET['view'] > 0) {
		$student_id = filterinp($_GET['view']);
		$fee_id = filterinp($_GET['fee_id']);
		$term_id = filterinp($_GET['term_id']);
		$session_id = filterinp($_GET['session_id']);

	$sql=$query="SELECT * FROM fee_paid WHERE fee_id = '$fee_id' AND session_id = '$session_id' AND term_id = '$term_id' AND student_id = '$student_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$id = sprintf('%07d',$row['id']);
	$amount = $row['amount'];
	$date = $row['date'];
	$gateway = $row['gateway'];
	$name = $row['approved_by'];

	}
?>
<div id="add-new">
   <div id="add-new-head">Payment Details
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>

       <tr>
        <td  align="left" valign="middle">Payment Ref.:</td>
        <td  align="left" valign="middle"> <?php  echo $id   ?> </td>
      </tr>

       <tr>
        <td  align="left" valign="middle">Student:</td>
        <td  align="left" valign="middle"> <?php  echo studentName($student_id)   ?> </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Fee:</td>
        <td  align="left" valign="middle"> <?php echo feeName($fee_id) ?> </td>
      </tr>

      <tr>
        <td  align="left" valign="middle"> Session:</td>
        <td  align="left" valign="middle"> <?php echo sessionName($session_id) ?> </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Term:</td>
        <td  align="left" valign="middle"> <?php echo termName($term_id) ?> </td>
      </tr>

 	  <tr>
        <td align="left" valign="middle">Payment Date:</td>
        <td  align="left" valign="middle">
        	<?php echo $date;?>
      </td>
      </tr>

 	  <tr>
        <td align="left" valign="middle">Total Paid:</td>
        <td  align="left" valign="middle">
        <?php echo $userSymbul.number_format($amount); ?>
      </td>
		<?php if(feeTotal($fee_id) > totalPaid($amount)) { ?>
        <tr>
        <td align="left" valign="middle">Outstanding:</td>
        <td  align="left" valign="middle">
        <?php echo $userSymbul.number_format(feeTotal($fee_id)-$amount); ?>
      </td>
        <?php } ?>
 	  <tr>
        <td align="left" valign="middle">Mode of Payment:</td>
        <td  align="left" valign="middle">
        <?php echo gatewayName($gateway); ?>
      </td>
      </tr>

 	  <tr>
        <td align="left" valign="middle">Processed By:</td>
        <td  align="left" valign="middle">
        <?php echo adminData('name',$userID); ?>
      </td>
      </tr>

      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;    </td>
      </tr>
    </table>

	</div>
</div>
<?php
}


if(isset($_GET['fee_id'])){
	$term = filterinp($_GET['term_id']);
	$session = filterinp($_GET['session_id']);
	$fee = filterinp($_GET['fee_id']);
	$class = $student = 0;

	//Get Fee Data
	$sql=$query="SELECT * FROM fees WHERE id = '$fee'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);

	$amount = $row['amount'];
	$title = $row['title'];
	$hostel = $row['hostel_id'];
	$bus = $row['bus_id'];

	if($row['session_id']>0) $session = $row['session_id'];
	if($row['term_id']>0) $term = $row['term_id'];
	if($row['student_id']>0) { 
		$student = $row['student_id']; $bus = $hostel = $class = 0;
	}
	
	//fetch paid list for selected fee
	$sql=$query = "select student_id FROM fee_paid WHERE school_id = '$school_id' AND fee_id = '$fee' AND session_id = '$session' AND term_id = '$term'";
 	$resultP = mysqli_query($server, $query) or die(mysqli_error($server));
	$numP = mysqli_num_rows($resultP);

	//build array of paid student id
	$MailtoDelimiter = ",";
	$rsEmailList = mysqli_query($server, $query);
	$sEmailLink = '';
	while (list ($email) = mysqli_fetch_row($rsEmailList))	{
	    $sEmail = $email;
	    if ($sEmail)  {
	        if ($sEmailLink) $sEmailLink .= $MailtoDelimiter;
	        if (!stristr($sEmailLink, $sEmail))   $sEmailLink .= $sEmail;
	    }
	}
	$paidList = $sEmailLink;
	
	//build querry for student affected
	$joined = $added = "";
	if($student > 0) {
		$added = " AND s.id = '$student' ";	
	}
	if($class > 0) {
		$added = " AND c.class_id = '$class' AND c.session_id = '$session' ";	
		$joined = " JOIN student_class c ON s.id = c.student_id ";
	}
	if($hostel > 0 || $bus == -1) {
		$added = " AND h.id = '$hostel' ";	
		if($hostel < 0) $added = " AND h.id > 0 ";	
		$joined = " JOIN student_hostel h ON s.id = h.student_id ";
	}
	if($bus > 0 || $bus == -1) {
		$added = " AND b.id = '$bus' ";	
		if($bus < 0) $added = " AND b.id > 0 ";	
		$joined = " JOIN student_bus b ON s.id = b.student_id ";
	}
 	$sql2=$query2 = "select * FROM students s $joined WHERE s.school_id = '$school_id' $added ORDER BY s.first_name DESC";
	
	$resultS = mysqli_query($server, $query2) or die(mysqli_error($server));
	$numS = mysqli_num_rows($resultS);

	if($numP < "1") {
		$message = "No payment record fund for your selections! Please try another search terms.";
		$class="blue";
	}
} else {
		$message = "Select a Session, Term, Class and Fee to see report";
		$class="blue";
}

?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <select name="fee_id" id="e3" >
			<?php
			     $sqlC=$queryC="SELECT * FROM fees WHERE school_id = '$school_id' ORDER BY title ASC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

								$i=0;
								while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $titleF = $row['title'];
            ?>
               <option  value="<?php echo $c_id; ?>"><?php echo $titleF; ?></option>
            <?php
								$i++;
								}  ?>
            <option selected value="" disabled>Select Fee</option>
			</select>

        &nbsp;
           <select name="session_id" id="e1" >
			<?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY start_date DESC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
                while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($session == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php
									$i++;
								}   ?>
            <option selected value="" disabled>Select Session</option>
			</select>
		&nbsp;
        <select name="term_id" id="e2" >
       		 <option value="0" <?php if($term == 0) {echo 'selected';} ?>>Any Terms</option>
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($term == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php
									$i++;
								}   ?>
            <option selected value="" disabled>Select Term</option>
			</select>

        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3 || userRole($userID) == 3) { ?>
        <a href="admin/fee?pay&fee=<?=$fee?>"><button type="button" class="submit">Pay Fee</button></a>
        <?php } ?>
        </form>
    </div>

    <?php if(isset($_GET['fee_id'])) { ?>
	<div class="panel" style="/* [disabled]border-color: transparent; */">
    	<div class="panel-head"> &nbsp;<?php echo feeName($fee).' report';?></div>
        <div class="panel-body">
        	<a href="#" onClick="javascript:printDiv('Attendance')"> <button class="submit no-print">Print Report</button></a>
            <div id="Attendance">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            	<tr class="inner">
                	<td style="width:50%"><strong>Fee</strong>: <blue><?=feeName($fee)?></blue></td>
                    <td><strong>Amount</strong>: <blue><?=$userSymbul.number_format($amount,2)?></blue></td>
                </tr>
                <tr class="inner">
                	<td><strong>Session</strong>: <blue><?=sessionName($session)?></blue></td>
                    <td><strong>Term</strong>: <blue><?=termName($term)?></blue></td>
                </tr>
                <tr class="inner">
                	<td><strong>Affected Students</strong>: <blue><?=number_format($numS)?></blue></td>
                    <td><strong>Report Date:</strong> <blue><?=date('F j, Y')?></blue></td>
                </tr>
            </table>
            <p></p>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="30%" scope="col">Student</th>
                <th width="15%" scope="col">Class</th>
                <th width="25%" scope="col">Payment Status</th>
                <th width="15%" scope="col">Actions</th>
              </tr>
               <?php
					$paidList = explode(',',$paidList);

				$i=0;
				while($row = mysqli_fetch_assoc($resultS)){
					$id = $row['id'];
					$title = $row['first_name'].' '. $row['last_name'].' '. $row['other_name'];
					$class = className(getClass($row['id'],$currentSession));
					$image = "<red>Unpaid</red>";
					$ball = '';
					 if(in_array($id, $paidList)) {
						$image = "<green>Paid</green>";
						 if(feeTotal($fee) > totalPaid($fee, $session, $term, $class, $id)) {
							 $image  = '<orange>'.$userSymbul.number_format(feeTotal($fee) - totalPaid($fee, $session, $term, $class, $id)).' Outstanding </orange>';
						 }
					 }

			?>
              <tr class="inner">
                <td width="30%"> <?php echo $title; ?></td>
                <td width="15%"> <?php echo $class; ?></td>
                <td width="25%" valign="middle" style=" text-size:12px;"><?php echo $image; ?></td>
                <td width="15%" valign="middle">
                <?php if(in_array($id, $paidList)) { ?>
                <a class="no-print" href="admin/payment?view=<?php echo $id;?>&fee_id=<?php echo $fee;?>&session_id=<?php echo $session;?>&term_id=<?php echo $term;?>"><button>Detail</button></a>
                	<?php if(feeTotal($fee) > totalPaid($fee, $session, $term, $class, $id)) { ?>
                <a class="no-print" href="admin/fee?pay=<?php echo $id;?>&fee=<?php echo $fee;?>&session=<?php echo $session;?>&term=<?php echo $term;?>"><button class="btn-success">Add Payment </button></a>
					<?php }
				} ?>
                </td>
              </tr>
             <?php
					 $i++;
				 } ?>

    <?php } ?>
              </table>
			</div>
<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
</div>
