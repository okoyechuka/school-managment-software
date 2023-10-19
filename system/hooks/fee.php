<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		fee.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			28/02/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) > 3 && userRole($userID) != 7) {
header('location: admin.php');
}

	$curSS=$session = getSetting('current_session');
	$curTT=$term = getSetting('current_term');
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
		$sql=$query= "DELETE FROM fees WHERE id = '$delete' ";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));

		$message = "The fee was successfully deleted.";
		$class="green";
	} else {
		$message = "Are you sure you want to delete the selected fee? <br><a href='admin/fee?delete=".$delete."&yes=1'>Yes I'm sure</a> <a href='admin/fee'>Cancel</a>";
		$class="yellow";
	}
}

//start initiation
if(isset($_GET['new']) || isset($_REQUEST['edit']))
{
	if(userRole($userID) > 2) {
	header('location: fee');
	}

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

	if(isset($_REQUEST['edit'])) {
	$edit = filterinp($_REQUEST['edit']);

		$sql=$query="SELECT * FROM fees WHERE id = '$edit'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);

		$currentSession = $row['session_id'];
		$currentTerm = $row['term_id'];
		$class = $row['class_id'];
		$amount = $row['amount'];
		$title = $row['title'];
		$student = $row['student_id'];
		$hostel = $row['hostel_id'];
		$bus = $row['bus_id'];
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['title']; ?>
<?php
	} else {
	$amount = '';
	$title = '';
	$class = '';
	$student = 0;
	$hostel = 0;
	$bus = 0;
?>
<div id="add-new">
   <div id="add-new-head">New Fee
<?php } ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/fee" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
 	  <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" id="titles" required="required"  value="<?php echo $title;?>">
      </td>
      </tr>

       <tr>
        <td  align="left" valign="middle">Select Session:</td>
        <td  align="left" valign="middle">
        <select name="session_id" id="e1" style="width: 90%;" >
        	<option value="0" <?php if($currentSession == 0) {echo 'selected';} ?>>All Sessions</option>
			<?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY start_date DESC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title0 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($currentSession == $g_id) {echo 'selected';} ?>><?php echo $title0; ?></option>
            <?php  $i++; }   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Select Term:</td>
        <td  align="left" valign="middle">
        <select name="term_id" id="e2" style="width: 90%;" >
       		 <option value="0" <?php if($currentTerm == 0) {echo 'selected';} ?>>All Terms</option>
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title1 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($currentTerm == $g_id) {echo 'selected';} ?>><?php echo $title1; ?></option>
            <?php $i++; }   ?>
			</select>
        </td>
      </tr>
		
      <tr>
        <td  align="left" valign="middle">Assign to Student:  <small>(This will override Class,Hostel & Bus assignment)</small></td>
        <td  align="left" valign="middle">
        <select name="student_id" class="hidders" id="e3" style="width: 90%" >
       		<option value="0" <?php if($student == 0) {echo 'selected';} ?>>All Students</option>
			<?php
                $sql=$query="SELECT * FROM students WHERE school_id = '$school_id' AND status = '1' ORDER BY first_name ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title0 = $row['first_name'].' '.$row['last_name']. ' '.$row['other_name'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$student == $g_id) { echo 'selected'; } ?>><?php echo $title0; ?></option>
            <?php  $i++;}   ?>
			</select>
        </td>
      </tr>
        
      <tr class="hidden_fields">
        <td  align="left" valign="middle">Assign to Class: <small>(Optional)</small></td>
        <td  align="left" valign="middle">
        <select name="class_id" id="e4" style="width: 90%" >
       		<option value="0" <?php if($class == 0) {echo 'selected';} ?>>All Classes</option>
			<?php
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

                $i=0;
								while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title2 = $row['title'];
            ?>
               <option  value="<?php echo $c_id; ?>"><?php echo $title2; ?></option>
            <?php  $i++;}  ?>
			</select>
        </td>
      </tr>
	
     <tr class="hidden_fields">
        <td  align="left" valign="middle">Assign to Boarding Students or Hostel:  <small>(Optional)</small></td>
        <td  align="left" valign="middle">
        <select name="hostel_id" id="e5" style="width: 90%" >
       		<option value="0" <?php if($hostel == 0) {echo 'selected';} ?>>Do Not Assign</option>
            <option value="-1" <?php if($hostel == -1) {echo 'selected';} ?>>All Boarding Students</option>
			<?php
			     $sqlC=$queryC="SELECT * FROM hostels WHERE school_id = '$school_id' ORDER BY title ASC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);
                while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title2 = $row['title'];
            ?>
               <option  value="<?php echo $c_id; ?>" <?php if($hostel == $c_id){echo 'selected';} ?>><?php echo $title2; ?></option>
            <?php }  ?>
			</select>
        </td>
      </tr>
      
      <tr class="hidden_fields">
        <td  align="left" valign="middle">Assign to School Bus Users:  <small>(Optional)</small></td>
        <td  align="left" valign="middle">
        <select name="bus_id" id="e6" style="width: 90%" >
       		<option value="0" <?php if($bus == 0) {echo 'selected';} ?>>Do Not Assign</option>
            <option value="-1" <?php if($bus == -1) {echo 'selected';} ?>>All School Bus Users</option>
			<?php
			     $sqlC=$queryC="SELECT * FROM vehicles WHERE school_id = '$school_id' ORDER BY title ASC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);
                while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title2 = $row['title'];
            ?>
               <option  value="<?php echo $c_id; ?>" <?php if($bus == $c_id){echo 'selected';} ?>><?php echo $title2; ?></option>
            <?php }  ?>
			</select>
        </td>
      </tr>
      
 	  <tr>
        <td align="left" valign="middle">Amount:</td>
        <td  align="left" valign="middle">
        	<input type="number" name="amount" id="amounts" required="required"  value="<?php echo $amount;?>">
      </td>
      </tr>


      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;

        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="fee" value="<?php echo $edit; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Fee</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Create Fee</button>
        <?php } ?>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Creating Fee...</div>

      </td>
	</form>
      </tr>
    </table>

	</div>
</div>
<?php
}


//start initiation
if(isset($_GET['pay'])){
	$date = date('Y-m-d');
	$amount = '';

	if($_GET['pay'] > 0) {
		$student_id = $_GET['pay'];
		$fee_id = $_GET['fee'];
		$term_id = $_GET['term'];
		$session_id = $_GET['session'];

		$sql=$query="SELECT * FROM fee_paid WHERE fee_id = '$fee_id' AND session_id = '$session_id' AND term_id = '$term_id' AND student_id = '$student_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$amount = $row['amount'];
		$amount = feeTotal($fee_id) - $amount;
		$date = $row['date'];
		$id = $row['id'];
	}
?>
<div id="add-new">
   <div id="add-new-head">Pay Fee
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/fee" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>

       <tr>
        <td  align="left" valign="middle">Select Student:</td>
        <td  align="left" valign="middle">
        <select name="student_id" id="e1" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM students WHERE school_id = '$school_id' ORDER BY first_name ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title0 = $row['first_name']. ' '.$row['last_name']. ' '.$row['other_name'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$student_id == $g_id) { echo 'selected'; } ?>><?php echo $title0; ?></option>
            <?php  }   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Select Fee:</td>
        <td  align="left" valign="middle">
        <select name="fee_id" id="e2feetpay" required style="width: 90%;">
        	<option value="0" disabled selected >Select fee to pay</option>
			<?php
                $sql=$query="SELECT * FROM fees WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);
				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title1 = $row['title'];
            ?>
               <option data-amount="<?=$row['amount']?>" value="<?php echo $g_id; ?>" <?php if(@$fee_id == $g_id) { echo 'selected'; } ?>><?php echo $title1; ?></option>
            <?php }   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Select Session:</td>
        <td  align="left" valign="middle">
        <select name="session_id" style="width: 90%;" >
        	<option value="<?php echo '0'; ?>" <?php if(@$session_id == 0) { echo 'selected'; } ?>><?php echo 'Not Applicable'; ?></option>
			<?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)) {
                $g_id = $row['id'];
                $title2 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$session_id == $g_id) { echo 'selected'; } ?>><?php echo $title2; ?></option>
            <?php  }   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Select Term:</td>
        <td  align="left" valign="middle">
        <select name="term_id" style="width: 90%;" >
        	<option value="<?php echo '0'; ?>" <?php if(@$term_id == 0) { echo 'selected'; } ?>><?php echo 'Not Applicable'; ?></option>
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title3 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$term_id == $g_id) { echo 'selected'; } ?>><?php echo $title3; ?></option>
            <?php  $i++;}   ?>
			</select>
        </td>
      </tr>

 	  <tr>
        <td align="left" valign="middle">Payment Date:</td>
        <td  align="left" valign="middle">
        	<input type="date" name="date" id="amounts" required="required"  value="<?php echo $date;?>">
      </td>
      </tr>

 	  <tr>
        <td align="left" valign="middle">Total Amount:</td>
        <td  align="left" valign="middle">
        	<input type="number" name="amount" id="total" required="required"  value="<?php echo $amount; ?>">
      </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Mode of Payments:</td>
        <td  align="left" valign="middle">
        <select name="gateway" required id="e5" style="width: 90%;" >
        	<option value="0" disabled selected >Select a payment method</option>
			<?php
                 $sql=$query="SELECT * FROM paymentgateways WHERE enabled > 0 AND customer_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title3 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" ><?php echo $title3; ?></option>
            <?php  }   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;
		<?php if($_GET['pay'] > 0) { ?>
        	<input type="hidden" name="update_paid" value="<?php echo $id; ?>" />
            <input type="hidden" name="amount_paid" value="<?php echo $amount; ?>" />
        <?php } else { ?>
        	<input type="hidden" name="paid" value="yes" />
        <?php } ?>
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Payment</button>

      </td>
      <td>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Processing...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}



if(isset($_POST['add'])) {
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	if($student_id>0) {
		$class_id = $hostel_id = $bus_id = 0;
	}
		//create new prents
	$sql=$query = "INSERT INTO fees (`id`, `school_id`, `session_id`, `class_id`, `term_id`, `student_id`, `title`, `amount`, `hostel_id`, `bus_id`)
	VALUES (NULL, '$school_id', '$session_id', '$class_id', '$term_id', '$student_id', '$title', '$amount', '$hostel_id', '$bus_id');";
	mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new fee was succesfully created.';
	$class = 'green';

}

if(isset($_POST['paid'])){
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
		//create new prents
		if(feePaid($student_id,$fee_id,$session_id,$term_id)) {
			$message = 'A payment record already exist with the selected student and fee. <a href="admin/fee?pay='.$student_id.'&fee='.$fee_id.'&session='.$session_id.'&term='.$term_id.'">Click Here</a> to update the existing record instead.';
			$class = 'yellow';

		} else {
			$description = feeName($fee_id).' Payment by '.studentName($student_id);
			if($amount < feeTotal($fee_id)) {
				$description = feeName($fee_id).' Part-payment by '.studentName($student_id);
			}


			$sql=$query = "INSERT INTO fee_paid (`id`, `school_id`, `fee_id`, `student_id`, `date`, `approved_by`, `session_id`, `term_id`,`amount`,`gateway`)
			VALUES (NULL, '$school_id', '$fee_id', '$student_id', '$date', '$userID', '$session_id', '$term_id','$amount','$gateway');";
			mysqli_query($server, $query) or die(mysqli_error($server));

			//create invoice
			$parent_id = $parent = getParent($student_id);
			$sql=$query = "INSERT INTO invoices (`id`, `school_id`, `fee_id`, `student_id`, `date`, `session_id`, `term_id`, `amount`,  `status`, `parent_id`)
			VALUES (NULL, '$school_id', '$fee_id', '$student_id', '$date', '$session_id', '$term_id','$amount','Paid', '$parent_id');";
			mysqli_query($server, $query) or die(mysqli_error($server));
			//get inserted invoice ID
			$invoice_id = getInsertedID('invoices');

			//create transaction record
			$sql=$query ="INSERT INTO transactions (`id`, `school_id`, `description`, `status`, `date`, `approvedBy`, `direction`, `amount`, `invoice_id`)
			VALUES (NULL, '$school_id', '$description', 'Completed', '$date', '$userID', 'IN','$amount', '$invoice_id');";
			mysqli_query($server, $query) or die(mysqli_error($server));


		$message = 'The fee payment record was succesfully saved.';
		$class = 'green';
		}
}


if(isset($_POST['update_paid'])){
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$amount2 = $amount_paid+$amount;

	$description = feeName($fee_id).' Payment by '.studentName($student_id);
	if($amount < feeTotal($fee_id)) {
		$description = feeName($fee_id).' Part-payment by '.studentName($student_id);
	}

	//create new prents
	$sql=$query="UPDATE `fee_paid` SET `amount` =  '$amount2' WHERE `id` = '$update_paid';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `fee_paid` SET `approved_by` =  '$userID' WHERE `id` = '$update_paid';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `fee_paid` SET `date` =  '$date' WHERE `id` = '$update_paid';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `fee_paid` SET `gateway` =  '$gateway' WHERE `id` = '$update_paid';";
	mysqli_query($server, $query) or die(mysqli_error($server));

	//create invoice
	$parent = getParent($student_id);
	$sql=$query = "INSERT INTO invoices (`id`, `school_id`, `fee_id`, `student_id`, `date`, `session_id`, `term_id`, `amount`, `parent_id`)
	VALUES (NULL, '$school_id', '$fee_id', '$student_id', '$date', '$session_id', '$term_id','$amount', '$parent_id');";
	mysqli_query($server, $query) or die(mysqli_error($server));
	//get inserted invoice ID
	$invoice_id = getInsertedID('invoices');

	//create transaction record
	$sql=$query = "INSERT INTO transactions (`id`, `school_id`, `description`, `status`, `date`, `approvedBy`, `direction`, `amount`, `invoice_id`)
	VALUES (NULL, '$school_id', '$description', 'Completed', '$date', '$userID', 'IN','$amount', '$invoice_id');";
	mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'Payment succesfully added.';
	$class = 'green';
}


if(isset($_POST['save'])) {
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	
	if($student_id>0) {
		$class_id = $hostel_id = $bus_id = 0;
	}
	$sql=$query="UPDATE `fees` SET `session_id` =  '$session_id' WHERE `id` = '$fee';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `fees` SET `term_id` =  '$term_id' WHERE `id` = '$fee';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `fees` SET `class_id` =  '$class_id' WHERE `id` = '$fee';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `fees` SET `title` =  '$title' WHERE `id` = '$fee';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `fees` SET `amount` =  '$amount' WHERE `id` = '$fee';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `fees` SET `student_id` =  '$student_id' WHERE `id` = '$fee';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	mysqli_query($server,"UPDATE `fees` SET `hostel_id` =  '$hostel_id' WHERE `id` = '$fee';") or die(mysqli_error($server));
	mysqli_query($server,"UPDATE `fees` SET `bus_id` =  '$bus_id' WHERE `id` = '$fee';") or die(mysqli_error($server));

	$message = 'The selected fee was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword']))
{
$class = filterinp($_GET['class_id']);
$term = filterinp($_GET['term_id']);
$session = filterinp($_GET['session_id']);

$school_id = $school_id;

$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', $term);
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "f.title LIKE '%$term%' OR f.amount LIKE '%$term%' OR c.title LIKE '%$term%' OR t.title LIKE '%$term%' OR s.title LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "f.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query= "select f.* FROM fees f JOIN session s ON s.id = f.session_id JOIN classes c ON c.id = f.class_id JOIN terms t ON t.id = f.term_id WHERE a.school_id = '$school_id' AND $filter AND f.class_id = '$class' AND f.session_id = '$session' AND f.term_id = '$term'";

 	$resultF = mysqli_query($server, $query) or die(mysqli_error($server));
	$numF = mysqli_num_rows($resultF);

	if($numF < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM fees WHERE school_id = '$school_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";

	$resultF = mysqli_query($server, $query);
	$numF = mysqli_num_rows($resultF);
	if($numF < "1")
		{
		$message = "There are no fees created for your school";
		$class="blue";
		}
}

?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
           <select name="session_id" id="e1" style="" >
        	<option value="0" <?php if($currentSession == 0) {echo 'selected';} ?>>All Sessions</option>
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
            <?php  $i++;
								}   ?>
            <option selected value="" disabled>Select Session</option>
			</select>
		&nbsp;
        <select name="term_id" id="e2" style="" >
       		 <option value="0" <?php if($term == 0) {echo 'selected';} ?>>All Terms</option>
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($tern == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php  $i++;
					}   ?>
            <option selected value="" disabled>Select Term</option>
			</select>
        &nbsp;
        <select name="class_id" id="e3" style="" >
       		<option value="0" <?php if($class == 0) {echo 'selected';} ?>>All Classes</option>
			<?php
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

                $i=0;
								while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option  value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  $i++;
					}  ?>
            <option selected value="" disabled>Select Class</option>
			</select>
            &nbsp;
        <input type="search" name="keyword" placeholder="Search Fees"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<4) { ?>
			<?php if(userRole($userID)<3) { ?>
            <a href="admin/fee?new"><button type="button" class="submit">Add Fee</button></a>
            <?php } ?>
        <a href="admin/fee?pay"><button type="button" class="submit">Pay Fee</button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="20%" scope="col">Fee</th>
                <th width="15%" scope="col">Session</th>
                <th width="13%" scope="col">Term</th>
                <th width="13%" scope="col">Class</th>
                <th width="11%" scope="col">Amount</th>
                <th width="16%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
					while($row = mysqli_fetch_assoc($resultF)){
					$id = $row['id'];
					$session = $row['session_id'];
					$term = $row['term_id'];
					$class = $row['class_id'];
					$amount = $row['amount'];
					$title = $row['title'];

					$classNa = className($class);
					if($row['student_id']>0) {
						$classNa = 'Not applicable';
					}
				?>
              <tr class="inner">
                <td width="20%"> <?php echo $title; ?></td>
                <td width="15%"> <?php echo sessionName($session); ?></td>
                <td width="13%"> <?php echo termName($term); ?></td>
                <td width="13%"> <?php echo className($class); ?></td>
                <td width="11%"> <?php echo $userSymbul.$amount; ?></td>
                <td width="16%" valign="middle">
                <a href="admin/payment?fee_id=<?php echo $id;?>&session_id=<?php echo $curSS;?>&term_id=<?php echo $curTT;?>&class_id=<?php echo $class;?>"><button class="btn-success">Report </button></a>
                <?php if(userRole($userID)<3) { ?>
                    <a href="admin/fee?edit=<?php echo $id;?>"><button>View/Edit </button></a>
                    <?php if(!feeInUse($id)) { ?>
                    <a href="admin/fee?delete=<?php echo $id;?>"><button class="btn-danger">Remove</button></a>
                    <?php } ?>
                 <?php } ?>
                </td>
              </tr>
              <?php
					$i++;
				} ?>
              </table>

<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
</div>
