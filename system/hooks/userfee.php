<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		userfee.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			10/03/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());


	$session = getSetting('current_session');
	$term = getSetting('current_term');
	$class="0";

if(!isset($_REQUEST['student_id']) || $_REQUEST['student_id'] < 1) {
if(userRole($userID) == 5) {
	$parent = userProfile($userID);
} else {
	$student = userProfile($userID);
	$parent = $student;
	header('location: userfee?student_id='.$student);
}

?>
<div id="add-new">
   <div id="add-new-head">Choose a Student to View Fees
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="get" action="userfee" enctype="multipart/form-data">
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
                $sql=$query="SELECT * FROM students s JOIN student_parent sp ON sp.student_id = s.id WHERE s.school_id = '$school_id' AND (sp.parent_id = '$parent' OR sp.student_id = '$student') ORDER BY s.first_name ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title0 = $row['first_name']. ' '.$row['last_name']. ' '.$row['other_name'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$student_id == $g_id) { echo 'selected'; } ?>><?php echo $title0; ?></option>
            <?php  $i++;
								}   ?>
			</select>
        </td>
      </tr>
      <tr>
      <td  align="left" valign="middle"></td>
        <td  align="left" valign="middle">
      <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" type="submit">Get Fees</button>
      </td>
      </tr>
      </table>
      </form>
      </div>
      </div>
<?php
} else {
if(userRole($userID) == 5) {
	$parent = userProfile($userID);

} else {
	$student = userProfile($userID);
	$parent = $student;
}

	$student_id = filterinp($_REQUEST['student_id']);
	$student = $student_id;


//start initiation
if(isset($_GET['pay']))
{
	$date = date('Y-m-d');
	$amount = '';

	$student_id = mysqli_real_escape_string($server,$_GET['pay']);
	$fee_id = mysqli_real_escape_string($server,$_GET['fee_id']);
	$term_id = filterinp($_GET['term_id']);
	$session_id = filterinp($_GET['session_id']);
	$class_id = getClass($student_id,$session_id);

	$sql=$query="SELECT * FROM fee_paid WHERE fee_id = '$fee_id' AND session_id = '$session_id' AND term_id = '$term_id' AND student_id = '$student_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num_fee = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$amount = feeTotal($fee_id);
	if(feeTotal($fee_id) != totalPaid($fee_id, $session_id, $term_id, $class_id, $student_id)) {
	$amount = feeTotal($fee_id) - totalPaid($fee_id, $session_id, $term_id, $class_id, $student_id);
	$date = $row['date'];
	$id = $row['id'];
	}
?>
<div id="add-new">
   <div id="add-new-head">Pay Fee
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="POST" action="userfee" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
		<?php if(@$_REQUEST['student_id']>0){?>
        	<input type="hidden" name="student_id" value="<?=mysqli_real_escape_string($server,$_REQUEST['student_id'])?>">
        <?php } else { ?>
       <tr>
        <td  align="left" valign="middle">Select Student:</td>
        <td  align="left" valign="middle">
        <select name="student_id" id="e1" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM students s JOIN student_parent sp ON sp.student_id = s.id WHERE s.school_id = '$school_id' AND (sp.parent_id = '$parent' OR sp.student_id = '$student') ORDER BY s.first_name ASC";
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
		<?php }
		if(@$_REQUEST['student_id']>0){	 ?>
        	<input type="hidden" name="fee_id" value="<?=mysqli_real_escape_string($server,$_REQUEST['fee_id'])?>">
        <?php } else { ?>
      <tr>
        <td  align="left" valign="middle">Select Fee:</td>
        <td  align="left" valign="middle">
        <select name="fee_id" id="e2" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM fees ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title1 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$fee_id == $g_id) { echo 'selected'; } ?>><?php echo $title1; ?></option>
            <?php  	}   ?>
			</select>
        </td>
      </tr>
		<?php } ?>
      <tr>
        <td  align="left" valign="middle">Select Session:</td>
        <td  align="left" valign="middle">
        <select name="session_id" id="e3" style="width: 90%;" >
        	<option value="<?php echo '0'; ?>" <?php if(@$session_id == 0) { echo 'selected'; } ?>><?php echo 'Not Applicable'; ?></option>
			<?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title2 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$session_id == $g_id) { echo 'selected'; } ?>><?php echo $title2; ?></option>
            <?php  $i++; }   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Select Term:</td>
        <td  align="left" valign="middle">
        <select name="term_id" id="e4" style="width: 90%;" >
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
            <?php $i++; }   ?>
			</select>
        </td>
      </tr>

 	  <tr>
        <td align="left" valign="middle">Total Amount:</td>
        <td  align="left" valign="middle">
        	<input type="number" name="amount" id="total" required="required"  value="<?php echo $amount; ?>">
      </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Prefered Payment Method:</td>
        <td  align="left" valign="middle">
        <select name="gateway" id="e5" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM paymentgateways WHERE (customer_id = '$school_id') AND enabled = '1' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title3 = $row['title'];
				$type = $row['type'];
            ?>
                   <option value="<?php echo $g_id; ?>" ><?php echo $title3; ?></option>
            <?php   }   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;
        	<input type="hidden" name="update_paid" value="<?php echo $id; ?>" />
            <input type="hidden" name="amount_paid" value="<?php echo $amount; ?>" />
        	<input type="hidden" name="paid" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Make Payment</button>

     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Creating Invoice...</div>
	</form>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}



if(isset($_POST['paid'])) {
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
		//create new 
		if($amount_paid<1) {
			$message = 'You have already paid the selected fee in full';
			$class = 'yellow';

		} else {
			$description = 'Payment for '.feeName($fee_id).' by '.studentName($student_id);
			if($amount < feeTotal($fee_id)) {
				$description = 'Part-payment for '.feeName($fee_id).' by '.studentName($student_id);
			}

			//create invoice
			$parent = getParent($student_id);
			$date = date('Y-m-d');
			$sql=$query = "INSERT INTO invoices (`id`, `school_id`, `fee_id`, `student_id`, `date`, `session_id`, `term_id`, `amount`,  `status`, `parent_id`)
			VALUES (NULL, '$school_id', '$fee_id', '$student_id', '$date', '$session_id', '$term_id','$amount','Un-Paid', '$parent');";
			mysqli_query($server, $query) or die(mysqli_error($server));
			//get inserted invoice ID
			$invoice_id = mysqli_insert_id($server);

			//create transaction record
			$date = date('Y-m-d');
			$tran_ref = 'SF'.time();
			$sql=$query = "INSERT INTO transactions (`id`, `school_id`, `description`, `status`, `date`, `approvedBy`, `direction`, `amount`, `invoice_id`, `gateway`, `transaction_reference`)
			VALUES (NULL, '$school_id', '$description', 'Pending', '$date', '-', 'IN','$amount', '$invoice_id', '$gateway', '$tran_ref');";
			mysqli_query($server, $query) or die(mysqli_error($server));

		//strore in session
		$_SESSION['Transaction_id'] = $invoice_id;
		$_SESSION['payment_gateway'] = $gateway;
		$_SESSION['tran_reference'] = $tran_ref;

		//display Invoice within form with link to pay with prefared payment option or to send payment notice if custom payment option was selected

		//get students profile
		$sql=$query="SELECT * FROM invoices WHERE id = '$invoice_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$amount = $row['amount'];
		$school_id = $row['school_id'];
		$fee_id = $row['fee_id'];
		$student_id = $row['student_id'];
		$date = $row['date'];
		$session_id = $row['session_id'];
		$term_id = $row['term_id'];
		$status = $row['status'];

		//set session
		$_SESSION['invoice_id'] = $invoice_id;

		if($status=='Un-Paid') { $tag1='<red>'; $tag2='</red>';}
		if($status=='Paid') { $tag1='<green>'; $tag2='<green>';}
	?>
        <div id="add-new">
            <div id="add-new-head">Invoice Number <?php echo sprintf('%07d',$invoice_id); ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
             <div class="inside" style="background-color: #CCC;">
             	<div id="reciept">

	<div class="panel" style="width: 99%;">
        <br /><div style="text-align: center">
        <img src="media/uploads/<?php echo getSetting('logo'); ?>" style="width: 150px;" /><br />
		<?php echo '<strong class="capitalize">'.getSetting('name').'</strong></left><br>';
		echo '<small>'.getSetting('address').', ';
		echo ''.getSetting('city').', '.getSetting('state').'</small><br>';
		echo '<small>Email: '.getSetting('email').'</small> &nbsp;';
		echo '<small>Tel: '.getSetting('phone1').' '.getSetting('phone2').'</small><br>';   ?>
        <br />INVOICE
        </div>
    	<div id="invoice-from" >
        <?php
		echo '<left><strong><blue>Invoice Date:</blue> </strong>'.date('d F, Y', strtotime($date)).'</left>';
		echo '<br><left><strong><blue>Status:</blue> '.$tag1.$status.$tag2.'</strong></left>';
		echo '<br><left><blue>Invoice No.</blue> '. sprintf("%07d",$invoice_id).'</left><br>';
		?>
        </div>
    	<div id="invoice-to">
        <?php
		$sql=$query="SELECT * FROM students WHERE id = '$student_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$address = $row['address'];
		$city = $row['city'];
		$state = $row['state'];

		echo '<left><strong><blue>INVOICE TO: </blue></strong></left><br>';
		echo '<left><strong class="capitalize">'.studentName($student_id).'</strong></left><br>';
		if(!empty($address)) echo '<left>'.$address.'</left><br>';
		if(!empty($city)) echo '<left>'.$city.', '.$state.'</left><br>';
		echo '<left>'.className(getClass($student_id,$session_id)).'</left><br>';
		?>
        </div>
        <div id="invoice-body">
         <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="60%" scope="col">Description</th>
                <th width="25%" scope="col">Amount</th>
              </tr>
              <tr class="cont">
                <td> <?php echo sessionName($session_id).' '.feeName($fee_id); ?></td>
                <td> <?php echo $userSymbul.$amount; ?></td>
              </tr>
              <tr class="cont">
                <td><strong>Total:</strong></td>
                <td> <strong><?php echo $userSymbul.$amount; ?></strong></td>
              </tr>
            </table>
           </div>
        </div>
        <div id="invoice-instruction" style="margin-bottom: 20px;">
			<a href="" onClick="javascript:printDiv('reciept')"><button class="submit" style="position: absolute; bottom: 30px; right: 20px;">Print Invoice</button></a>
            <?php
	//get gateway setting
	$sql=$query= "SELECT * FROM paymentgateways WHERE id='$gateway'";
	$result = mysqli_query($server,$query);
	$row = mysqli_fetch_assoc($result);
	$gateway = $row['type'];
	$gateway_id = $row['id'];
	$text = $row['text'];

	//save gateway incase of falure
	$_SESSION['gateway'] = $gateway;

	if($gateway=='custom') {
	echo '<note>'.$text.'<br>Your payment status remain Pending until it is confirmed.'.'</note>';
	echo '<a href="index.php"><button class="submit" onClick="document.getElementById(\'login-loading\').style.visibility=\'visible\'; return true;" name="continue" value="1" type="button">Return to Dashboard</button></a>';
	echo '<div id="login-loading"><i class="fa fa-spin fa-spinner"></i> Processing...</div>';
	} else {
	loadPaymentGateway($gateway_id,$_SESSION['Transaction_id'],$amount,$student_id);
	}
			?>
        </div>
    </div>

       </div>
     </div>
     </div>
<?php
		}
}


//----------------- Display failed payment infor and link to retry ------------------------------
if(isset($_REQUEST['failed'])){
	$invoice_id = $_SESSION['Transaction_id'];

		//display Invoice within form with link to pay with prefared payment option or to send payment notice if custom payment option was selected

		//get students profile
		$sql=$query="SELECT * FROM invoices WHERE id = '$invoice_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$amount = $row['amount'];
		$school_id = $row['school_id'];
		$fee_id = $row['fee_id'];
		$student_id = $row['student_id'];
		$date = $row['date'];
		$session_id = $row['session_id'];
		$term_id = $row['term_id'];
		$status = 'Un-Paid';

		//set session
		$_SESSION['invoice_id'] = $invoice_id;

		if($status=='Un-Paid') { $tag1='<red>'; $tag2='</red>';}
		if($status=='Paid') { $tag1='<green>'; $tag2='<green>';}
	?>
        <div id="add-new">
            <div id="add-new-head">Online Payment Failed
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
             <div class="inside" style="background-color: #CCC;">

           <?php  showMessage('Oops!<br>Your payment for invoice No. '.$invoice_id.' failed - '.@$_SESSION['comment'].'. Here is your payment details.','red'); ?>

             	<div id="reciept">

	<div class="panel" style="width: 99%;">
        <br /><div style="text-align: center">
        <img src="media/uploads/<?php echo getSetting('logo'); ?>" style="width: 150px;" /><br />
		<?php echo '<strong class="capitalize">'.getSetting('name').'</strong></left><br>';
		echo '<small>'.getSetting('address').', ';
		echo ''.getSetting('city').', '.getSetting('state').'</small><br>';
		echo '<small>Email: '.getSetting('email').'</small> &nbsp;';
		echo '<small>Tel: '.getSetting('phone1').' '.getSetting('phone2').'</small><br>';   ?>
        <br />INVOICE
        </div>
    	<div id="invoice-from" >
        <?php
		echo '<left><strong><blue>Invoice Date:</blue> </strong>'.date('d F, Y', strtotime($date)).'</left>';
		echo '<br><left><strong><blue>Status:</blue> '.$tag1.$status.$tag2.'</strong></left>';
		echo '<br><left><blue>Invoice No.</blue> '. sprintf("%07d",$invoice_id).'</left><br>';
		?>
        </div>
    	<div id="invoice-to">
        <?php
		$sql=$query="SELECT * FROM students WHERE id = '$student_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$address = $row['address'];
		$city = $row['city'];
		$state = $row['state'];

		echo '<left><strong><blue>INVOICE TO: </blue></strong></left><br>';
		echo '<left><strong class="capitalize">'.studentName($student_id).'</strong></left><br>';
		if(!empty($address)) echo '<left>'.$address.'</left><br>';
		if(!empty($city)) echo '<left>'.$city.', '.$state.'</left><br>';
		echo '<left>'.className(getClass($student_id,$session_id)).'</left><br>';
		?>
        </div>
        <div id="invoice-body">
         <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="60%" scope="col">Description</th>
                <th width="25%" scope="col">Amount</th>
              </tr>
              <tr class="cont">
                <td> <?php echo sessionName($session_id).' '.feeName($fee_id); ?></td>
                <td> <?php echo $userSymbul.$amount; ?></td>
              </tr>
              <tr class="cont">
                <td><strong>Total:</strong></td>
                <td> <strong><?php echo $userSymbul.$amount; ?></strong></td>
              </tr>
            </table>
           </div>
        </div>
        <div id="invoice-instruction" style="margin-bottom: 20px;">
			<a href="" onClick="javascript:printDiv('reciept')"><button class="submit" style="position: absolute; bottom: 30px; right: 20px;">Print Invoice</button></a>

            <?php
	//get gateway setting
	$gateway = $_SESSION['gateway'];
	//get gateway setting
	$sql=$query= "SELECT * FROM paymentgateways WHERE id='$gateway'";
	$result = mysqli_query($server,$query);
	$row = mysqli_fetch_assoc($result);
	$gateway = $row['type'];
	$gateway_id = $row['id'];
	$text = $row['text'];

	//save gateway incase of falure
	$_SESSION['gateway'] = $gateway;

	if($gateway=='custom') {
	echo '<note>'.$text.'<br>Your payment status remain Pending until it is confirmed.'.'</note>';
	echo '<a href="index.php"><button class="submit" onClick="document.getElementById(\'login-loading\').style.visibility=\'visible\'; return true;" name="continue" value="1" type="button">Return to Dashboard</button></a>';
	echo '<div id="login-loading"><i class="fa fa-spin fa-spinner"></i> Processing...</div>';
	} else {
	loadPaymentGateway($gateway_id,$_SESSION['Transaction_id'],$amount,$student_id);
	}
			?>
        </div>
    </div>

       </div>
     </div>
     </div>
<?php
}



//------------------Display Payment Confirmation and Reciept ------------------------------
if(isset($_REQUEST['success']))
{
	$invoice_id = $_SESSION['Transaction_id'];

		//display Invoice within form with link to pay with prefared payment option or to send payment notice if custom payment option was selected

		//get students profile
		$sql=$query="SELECT * FROM invoices WHERE id = '$invoice_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$amount = $row['amount'];
		$school_id = $row['school_id'];
		$fee_id = $row['fee_id'];
		$student_id = $row['student_id'];
		$date = $row['date'];
		$session_id = $row['session_id'];
		$term_id = $row['term_id'];
		$status = 'Paid';

		//set session
		$_SESSION['invoice_id'] = $invoice_id;

		if($status=='Un-Paid') { $tag1='<red>'; $tag2='</red>';}
		if($status=='Paid') { $tag1='<green>'; $tag2='<green>';}
	?>
        <div id="add-new">
            <div id="add-new-head">Invoice Payment Confirmation
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
             <div class="inside" style="background-color: #CCC;">

           <?php  showMessage('Congratulations!<br>Your payment for invoice No. '.$invoice_id.' was successfull. Here is your payment reciept.','green'); ?>

             	<div id="reciept">

	<div class="panel" style="width: 99%;">
        <br /><div style="text-align: center">
        <img src="media/uploads/<?php echo getSetting('logo'); ?>" style="width: 150px;" /><br />
		<?php echo '<strong class="capitalize">'.getSetting('name').'</strong></left><br>';
		echo '<small>'.getSetting('address').', ';
		echo ''.getSetting('city').', '.getSetting('state').'</small><br>';
		echo '<small>Email: '.getSetting('email').'</small> &nbsp;';
		echo '<small>Tel: '.getSetting('phone1').' '.getSetting('phone2').'</small><br>';   ?>
        <br />INVOICE
        </div>
    	<div id="invoice-from" >
        <?php
		echo '<left><strong><blue>Invoice Date:</blue> </strong>'.date('d F, Y', strtotime($date)).'</left>';
		echo '<br><left><strong><blue>Status:</blue> '.$tag1.$status.$tag2.'</strong></left>';
		echo '<br><left><blue>Invoice No.</blue> '. sprintf("%07d",$invoice_id).'</left><br>';
		?>
        </div>
    	<div id="invoice-to">
        <?php
		$sql=$query="SELECT * FROM students WHERE id = '$student_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$address = $row['address'];
		$city = $row['city'];
		$state = $row['state'];

		echo '<left><strong><blue>INVOICE TO: </blue></strong></left><br>';
		echo '<left><strong class="capitalize">'.studentName($student_id).'</strong></left><br>';
		if(!empty($address)) echo '<left>'.$address.'</left><br>';
		if(!empty($city)) echo '<left>'.$city.', '.$state.'</left><br>';
		echo '<left>'.className(getClass($student_id,$session_id)).'</left><br>';
		?>
        </div>
        <div id="invoice-body">
         <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="60%" scope="col">Description</th>
                <th width="25%" scope="col">Amount</th>
              </tr>
              <tr class="cont">
                <td> <?php echo sessionName($session_id).' '.feeName($fee_id); ?></td>
                <td> <?php echo $userSymbul.$amount; ?></td>
              </tr>
              <tr class="cont">
                <td><strong>Total:</strong></td>
                <td> <strong><?php echo $userSymbul.$amount; ?></strong></td>
              </tr>
            </table>
           </div>
        </div>
        <div id="invoice-instruction" style="margin-bottom: 20px;">
			<a href="" onClick="javascript:printDiv('reciept')"><button class="submit" style="position: absolute; bottom: 30px; right: 20px;">Print Invoice</button></a>
        </div>
    </div>

       </div>
     </div>
     </div>
<?php
}


if(isset($_GET['keyword']))
{
$class = '';
$term = filterinp($_GET['term_id']);
$session = filterinp($_GET['session_id']);

$school_id = $school_id;

$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', $_GET['keyword']);
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "f.title LIKE '%$term%' OR f.amount LIKE '%$term%' OR c.title LIKE '%$term%' OR t.title LIKE '%$term%' OR st.first_name LIKE '%$term%' OR st.last_name LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "f.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select f.* FROM fees f JOIN session s ON s.id = f.session_id JOIN classes c ON c.id = f.class_id JOIN terms t ON t.id = f.term_id JOIN student_class scl ON scl_class_id = c.class_id JOIN students st ON st.id = scl.student_id JOIN student_parent spe ON spe.student_id = st.id WHERE a.school_id = '$school_id' AND (spe.student_id = '$student' OR spe.parent_id = '$parent') AND $filter AND f.class_id = '$class' AND f.session_id = '$session' AND f.term_id = '$term'";

 	$resultF = mysqli_query($server, $sql) or die(mysqli_error($server));
	$numF = mysqli_num_rows($resultF);

	if($numF < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT f.* FROM fees f JOIN student_class sc ON (f.class_id = sc.class_id OR f.class_id = 0) JOIN student_parent sp ON sp.student_id = sc.student_id WHERE f.school_id = '$school_id' AND (sc.student_id = '$student' OR sp.parent_id = '$parent' OR f.class_id = 0 OR f.student_id = '$student' OR f.student_id = '0') GROUP BY f.id ORDER BY f.title ASC";

	$resultF = mysqli_query($server, $sql) or die(mysqli_error($server));
	$numF = mysqli_num_rows($resultF);
	if($numF < "1")
		{
		$message = "There are no fees available for you";
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
        	<option value="0" <?php if($session == 0) {echo 'selected';} ?>>Select Sessions</option>
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
			</select>
		&nbsp;
        <select name="term_id" id="e2" style="" >
       		 <option value="0" <?php if($term == 0) {echo 'selected';} ?>>Select Terms</option>
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
			</select>
            &nbsp;
        <input type="search" name="keyword" placeholder="Search Fees" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
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
                <th width="10%" scope="col">Status</th>
                <th width="8%" scope="col"></th>
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

				$paidList = array_filter(explode(',',getPaidList($school_id,$id,$class,$session,$term)));

					$image = "<red>Unpaid</red>";
					$ball = '';
					 if(in_array($student_id, $paidList)) {
						 $image = "<green>Paid</green>";

						 if(feeTotal($id) > totalPaid($id, $session, $term, $class, $student_id)) {
							 $image = '<orange>'.$userSymbul.number_format(feeTotal($id) - totalPaid($id, $session, $term, $class, $student_id)).' Outstanding </orange>';
						 }
					 }


				?>
              <tr class="inner">
                <td width="20%"> <?php echo $title; ?></td>
                <td width="15%"> <?php echo sessionName($session); ?></td>
                <td width="13%"> <?php echo termName($term); ?></td>
                <td width="13%"> <?php echo className($class); ?></td>
                <td width="11%"> <?php echo $userSymbul.$amount; ?></td>
                <td width="10%" valign="middle">
                <?php echo $image; ?>
                <td width="8%" valign="middle">
                <?php if(feeTotal($id) > totalPaid($id, $session, $term, $class, $student_id)) { ?>
                <a href="userfee?pay=<?php echo $student_id;?>&student_id=<?php echo $student_id;?>&fee_id=<?php echo $id;?>&session_id=<?php echo $session;?>&term_id=<?php echo $term;?>&class_id=<?php echo $class;?>"><button class="btn-success">Pay Fee </button></a>
                <?php } ?>

                </td>
              </tr>
              <?php $i++;
						  } ?>
              </table>

<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
</div>
<?php } ?>
