<?php defined('SMSPanel') or die('Snooping around is not allowed. Please use the front door');
/*
File name: 		adminsetting.php
Description:	This is main customer setting page
Developer: 		Chuka Okoye (Mr. White)
Date: 			11/11/2014
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());
?>


<?php
if(isset($_POST['update'])) {
	$customer = getAdmin();
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$password = $_POST['password'];

	$sql=$query="UPDATE `users` SET
	`firstName` = '$firstName',
	`lastName` = '$lastName'
	 WHERE `id` = '$customer'";

		mysqli_query($server, $query) or die(mysqli_error($server));
	$message = "Your account details was successfully updated.";
	$class="green";

	if(!empty($password)) {
		$salt = genRandomPassword(32);
		$crypt = getCryptedPassword($password, $salt);
		$password2 = $crypt.':'.$salt;

		$sql=$query="UPDATE `users` SET
		`password` = '$password2'
		 WHERE `id` = '$customer'";

		mysqli_query($server, $query) or die(mysqli_error($server));

	//send new password by sms and email
	$newPasswordEmail = getSetting('newPasswordEmail');
	$newPasswordEmailSubject = getSetting('newPasswordEmailSubject');
	$emailSender = getSetting('emailSender');
	$emailFrom = getSetting('companyEmail');

		if(!empty($newPasswordEmail)) {
			$mail = str_replace('[USERNAME]', $username, $newPasswordEmail);
			$mail = str_replace('[PASSWORD]', $password, $newPasswordEmail);
			$mail = str_replace('[CUSTOMER NAME]', $name, $newPasswordEmail);

			sendEmail($emailFrom,$emailSender,$newPasswordEmailSubject,$email,$mail);
		}
	$message = "Your account details was successfully updated";
	$class="green";
	}


}
	$id = getAdmin();
	$sql=$query= "SELECT * FROM users WHERE id = '$id'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);

	$row = mysqli_fetch_assoc($result);
	$firstName = $row['firstName'];
	$lastName = $row['lastName'];
	$username = $row['username'];
	$email = $row['email'];
	//display form
?>

<div id="add-new">
	<div id="add-new-head">Edit My Account Details
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">X</div></a></div>
     <div class="inside">
		<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
    <form method="post" action="" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="5" cellpadding="0">
      <tr>
        <td align="left" valign="middle">First Name:</td>
        <td width="70%" align="left" valign="middle">
        	<input type="test" name="firstName" id="firstName" value="<?php echo $firstName; ?>" maxlength="200" required="required" placeholder="">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Last Name:</td>
        <td width="70%" align="left" valign="middle">
        	<input type="test" name="lastName" id="lastName" value="<?php echo $lastName; ?>" maxlength="200" required="required" placeholder="">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Username:</td>
        <td width="70%" align="left" valign="middle">
        	<input type="text" disabled="disabled" name="username" id="username" value="<?php echo $username; ?>" readonly required="required" placeholder="">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email:</td>
        <td width="70%" align="left" valign="middle">
        	<input type="text" name="email" id="email" value="<?php echo $email; ?>" maxlength="200" required="required" placeholder="">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Change Password:</td>
        <td width="70%" align="left" valign="middle">
        	<input type="test" name="password" id="password" maxlength="200"  placeholder="This will change your current password">
        </td>
      </tr>
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="update"  />
        <input type="hidden" name="customer" value="<?php echo getAdmin(); ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="save" value="1" type="submit">Update Details</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Processing...</div>
        </td>
      </tr>
   </table>
   </div>
 </div>

<!-- Start Dashboard ---->
<div class="wrapper">
	<div class="status-box"  style="background-color: #093">
    	<span class="big"><?php echo countCustomers('active'); ?></span>
        <span class="small">Active Customers</span>
    </div>

	<div class="status-box" style="background-color: #C30">
    	<span class="big"><?php echo countCustomers('suspended'); ?></span>
        <span class="small">Suspended Customers</span>
    </div>

	<div class="status-box">
    	<span class="big"><?php echo countCustomers('all'); ?></span>
        <span class="small">Total Customers</span>
    </div>
</div>

<div class="wrapper">
<!-- Show Payment alerts -->
	<div class="panel">
    	<div class="panel-head"><img src="media/images/list-white.png" />Recent Payment Notifications</div>
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="15%" scope="col">Payment Date</th>
                <th width="10%" scope="col">Order ID</th>
                <th width="15%" scope="col">Order Total</th>
                <th width="20%" scope="col">Depositor</th>
                <th width="15%" scope="col">Method</th>
                <th width="10%" scope="col">Payment Ref</th>
                <th width="15%" scope="col">Action</th>
              </tr>
               <?php
						    $sql=$query = "SELECT * FROM paymentalerts WHERE status = '0' ORDER BY id DESC LIMIT 15";
								$result = mysqli_query($server, $query);
								$num = mysqli_num_rows($result);

								$i = 0;
								while($row = mysqli_fetch_assoc($result)){
										$date = date('d/m/Y H:m', strtotime($row['date']));
										$id = $row['id'];
										$method = shorten(transactionGateway($row['transaction']),20);
										$order = $row['transaction'];
										$cost = transactionAmount($row['transaction']);
										$depositor = $row['depositor'];
										$reference = $row['reference'];
										$status = $row['status'];
										?>
			              <tr class="cont">
			                <td> <?php echo $date; ?></td>
			                <td> <?php echo sprintf('%07d',$order); ?></td>
			                <td> <?php echo $userSymbul.round($cost,2); ?></td>
			                <td> <?php echo $depositor; ?></td>
			                <td> <?php echo $method; ?></td>
			                <td> <?php echo $reference; ?></td>
			                <td valign="middle">
			                <a href="Alert.php?approvetrnxID=<?php echo $id;?>"><button>Mark as Verified</button></a>
			                </td>
			              </tr>
	              		<?php
										$i++;
								}
								?>
            </table>
        </div>
    </div>

	<div class="panel">
    	<div class="panel-head"><img src="media/images/list-white.png" />Recent Message Trafics</div>
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="10%" scope="col">Sender</th>
                <th width="10%" scope="col">Customer</th>
                <th width="30%" scope="col">Message</th>
                <th width="10%" scope="col">Units</th>
                <th width="10%" scope="col">Status</th>
                <th width="15%" scope="col">Action</th>
              </tr>
              <?php
							$sql=$query= "SELECT * FROM sentmessages  ORDER BY id DESC LIMIT 20";
							$result = mysqli_query($server, $query);
							$num = mysqli_num_rows($result);

							$i = 0;
							while ($row = mysqli_fetch_assoc($result)) {

								$id = $row['id'];
								$senderID = $row['senderID'];
								$customer = $row['customer'];
								$message = shorten($row['message'],'50');
								$units = $row['units'];
								$status = $row['status'];
								?>
				        <tr class="cont">
				          <td> <?php echo $id; ?></td>
				          <td> <?php echo $senderID; ?></td>
				          <td> <?php echo getUsername($customer); ?></td>
				          <td> <?php echo $message; ?></td>
				          <td> <?php echo number_format($units); ?></td>
				          <td> <?php echo $status; ?></td>
				          <td valign="middle"><a href="JobDetail.php?msgID=<?php echo $id;?>"><button>Detail</button></a> <a href="Compose.php?msgID=<?php echo $id;?>"><button>Resend</button></a></td>
				        </tr>
				        <?php
								$i++;
							} ?>
            </table>

        </div>
    </div>

	<div class="panel">
    	<div class="panel-head"><img src="media/images/list-white.png" />Recent Orders</div>
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="15%" scope="col">Date</th>
                <th width="10%" scope="col">Trnx ID</th>
                <th width="15%" scope="col">Payment Method</th>
                <th width="10%" scope="col">Quantity</th>
                <th width="15%" scope="col">Amount</th>
                <th width="10%" scope="col">Status</th>
                <th width="20%" scope="col">Action</th>
              </tr>
               <?php
			    $sql=$query = "SELECT * FROM transactions ORDER BY id DESC LIMIT 15";
				$result = mysqli_query($server, $query);
				$num = mysqli_num_rows($result);

				$i = 0;
				while($row = mysqli_fetch_assoc($result)){
					$date = date('d/m/Y H:m', strtotime($row['date']));
					$id = $row['id'];
					$method = shorten(gatewayName($row['gateway']),20);
					$units = $row['units'];
					$cost = $row['cost'];
					$gateway = $row['gateway'];
					$status = $row['status'];
					if($status==1) { $tag1='<blue>'; $tag2='</blue>';}
					if($status==2) { $tag1='<blue>'; $tag2='</blue>';}
					if($status==3) { $tag1='<green>'; $tag2='<green>';}
					if($status==4) { $tag1='<red>'; $tag2='</red>';}
					?>
          <tr class="cont">
            <td> <?php echo $date; ?></td>
            <td> <?php echo sprintf('%07d',$id); ?></td>
            <td> <?php echo $method; ?></td>
            <td> <?php echo number_format($units); ?></td>
            <td> <?php echo $userSymbul.round($userRate*$cost,2); ?></td>
            <td> <?php echo $tag1.transactionStatus($status).$tag2; ?></td>
            <td valign="middle">
            <?php
						if(isCustomGateway($gateway) && ($status<3)) {
							?>
            	<a href="Transaction.php?approve&trnxID=<?php echo $id;?>"><button class="success">Approve Transaction</button></a>
            	<?php
						}
						else {
							echo '-';
						} ?>
            </td>
          </tr>
          <?php
					$i++;
				} ?>
            </table>
        </div>
    </div>
</div>