<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		staff.php
Description:	This is main HR page
Developer: 		Ynet Interactive
Date: 			04/5/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) > 2) {
header('location: index.php');
}
if(isset($_REQUEST['view']))
{
	$staff = mysqli_real_escape_string($server, $_REQUEST['view']);
		//get students profile
		$sql=$query="SELECT * FROM staffs WHERE id = '$staff'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}

?>
<div id="add-new">
   <div id="add-new-head"><?php echo staffName($staff); ?>'s Credentials
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #CCC;">

	<div class="panel">
        <div class="panel-body panel-body2">
        	<div class="action-box">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print Credentials</button></a>
            <a href="admin/staff?edit=<?php echo $staff; ?>&done"><button class="submit">Edit</button></a>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle"><img src="media/uploads/<?php echo $picture; ?>" style="width: 70%; height: auto; max-height: 250px; border: 2px solid #999;"/></td>
        <td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Full Name:</td>
        <td  align="left" valign="middle"><?php echo $row['last_name'].' '.$row['first_name']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Gender:</td>
        <td  align="left" valign="middle"><?php echo $row['sex']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Marital Status:</td>
        <td  align="left" valign="middle"><?php echo $row['marital_status']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td colspan="2"  class="tr-heading"  align="left" valign="middle"><strong><g>Contact Information</g></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Residence Address:</td>
        <td  align="left" valign="middle"><?php echo $row['address'].', '.$row['city']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">City:</td>
        <td  align="left" valign="middle"><?php echo $row['city']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">State:</td>
        <td  align="left" valign="middle"><?php echo $row['state']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Nationality:</td>
        <td  align="left" valign="middle"><?php echo countryName($row['country']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Phone:</td>
        <td  align="left" valign="middle"><?php echo $row['phone']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email:</td>
        <td  align="left" valign="middle"><?php echo $row['email']; ?></td>
      </tr>

      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td class="tr-heading" colspan="2" align="left" valign="middle"><strong><g>Monthly Salary Information</g></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Basic Salary:</td>
        <td  align="left" valign="middle"><?php echo $userSymbol.$row['payroll']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Other Allowances:</td>
        <td  align="left" valign="middle"><?php echo $userSymbol.$row['allowance']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Employee Tax (PAYE):</td>
        <td  align="left" valign="middle"><?php echo $userSymbol.$row['paye']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Other Deductions:</td>
        <td  align="left" valign="middle"><?php echo $userSymbol.$row['deduction']; ?></td>
      </tr>

      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td class="tr-heading" colspan="2" align="left" valign="middle"><strong><g>Bank Account Information</g></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Account Title:</td>
        <td  align="left" valign="middle"><?php echo $row['acc_name']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Account Number:</td>
        <td  align="left" valign="middle"><?php echo $row['acc_num']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Banker:</td>
        <td  align="left" valign="middle"><?php echo $row['banker']; ?></td>
      </tr>

      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td colspan="2"  class="tr-heading"  align="left" valign="middle"><strong><g>Other Information</g></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Employment Date:</td>
        <td  align="left" valign="middle"><?php echo date('D F d, Y', strtotime($row['employment_date'])); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Designation:</td>
        <td  align="left" valign="middle"><?php echo $row['designation']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Qualifications:</td>
        <td  align="left" valign="middle"><?php echo $row['qualification']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top">Notes:</td>
        <td  align="left" valign="middle"><?php echo $row['note']; ?></td>
      </tr>
	  <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2"  class="tr-heading"  valign="middle"><strong>Custom Fields</strong></td>
      </tr>
     <?php
	$sql=$query = "SELECT * FROM custom_fields WHERE school_id = '$school_id' AND form = 'staff' ORDER BY id DESC";
	$result = mysqli_query($server, $sql);
	while($row3 = mysqli_fetch_assoc($result)){
	 ?>
      <tr>
        <td align="left" valign="middle"><?=$row3['label']?>:</td>
        <td  align="left" valign="middle"><?=customFieldValue($row3['id'],$row['id']) ?></td>
      </tr>
      <?php	} ?>
      <tr>
        <td  align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td colspan="2"  class="tr-heading"  align="left" valign="middle"><strong><g>Portal Login Details</g></strong></td>
      <tr>
        <td align="left" valign="middle">Username:</td>
        <td  align="left" valign="middle"><?php echo portalUsername($staff, $row['role_id']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Password: </td>
        <td  align="left" valign="middle"><?php echo $row['pln_pa']; ?> </td>
      </tr>
      </table>
	</div>
  </div>
  </div>

</div>
</div>


 <?php
}
 if(isset($_REQUEST['card']) && !empty($_REQUEST['card'])) {
	$staff = filterinp($_REQUEST['card']);
		//get students profile
		$sql=$query="SELECT * FROM staffs WHERE id = '$staff'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}
	?>
        <div id="add-new">
            <div id="add-new-head"><?php echo staffName($staff); ?>'s ID Card
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
             <div class="inside" style="background-color: #CCC;">
             	<div id="idCard">
                	<div id="sch-logo"><img src="media/uploads/<?php echo $picture; ?>"  /></div>
                    <div id="sch-name"><?php echo getSetting('name'); ?><br /><black><?php echo getSetting('address').', '.getSetting('city').', '.getSetting('state'); ?></black></div>
                    <div id="id-title">Staff ID Card</div>
                    <div id="pasport"><img src="media/uploads/<?php echo $picture; ?>"  /></div>
                    <div id="data">
                    	<div class="title">Name: </div><div class="value"><?php echo staffName($staff); ?> </div>
                        <div class="title">Address: </div><div class="value"><?php echo $row['address']; ?></div>
                        <div class="title">Gender: </div><div class="value"><?php echo $row['sex']; ?></div>
                        <div class="title"> </div><div class="value"><?php echo $row['state'].', '.$row['country']; ?></div>
                    </div>
                </div>
                <div id="id-print">
                This generated ID Card has been optimized for use with any 3.5" X 2" Plastic Card Printer.<br /><br />
                <a href="" onClick="javascript:printDiv('idCard')">
                <button class="submit">Print ID Card</button></a></div>
             </div>
        </div>
 <?php }


if(isset($_REQUEST['edit']) || isset($_REQUEST['new']))
{
if(userRole($userID) > 2) {
header('location: index.php');
}

	if(isset($_REQUEST['edit'])) {
	$staff = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM staffs WHERE id = '$staff'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo staffName($staff); ?>'s Profile
<?php
	} else {
		$staff = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Staff
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/staff" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <tr>
        <td width="35%" colspan="2"  class="tr-heading"  align="left" valign="middle"><strong><g>Personal Information</g></strong></td>
      <tr>
        <td align="left" valign="middle">Photo:</td>
        <td  align="left" valign="middle">
        	<input type="file" accept="image/gif,image/png,image/jpg" style="width: 70%" name="photo" id="photo" >
        </td>
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
        <td align="left" valign="middle">Gender:</td>
        <td  align="left" valign="middle">
        <select name="sex" id="sex" style="width:90%" >
               <option <?php if(@$row['sex'] == 'Male') { echo 'selected';} ?> value="Male"><?php echo 'Male'; ?></option>
               <option <?php if(@$row['sex'] == 'Female') { echo 'selected';} ?> value="Female"><?php echo 'Female'; ?></option>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Marital Status:</td>
        <td  align="left" valign="middle">
        <select name="marital_status" id="sex" style="width:90%" >
               <option <?php if(@$row['marital_status'] == 'Single') { echo 'selected';} ?> value="Single"><?php echo 'Single'; ?></option>
               <option <?php if(@$row['marital_status'] == 'Married') { echo 'selected';} ?> value="Married"><?php echo 'Married'; ?></option>
                <option <?php if(@$row['marital_status'] == 'Divorced') { echo 'selected';} ?> value="Divorced"><?php echo 'Divorced'; ?></option>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2" class="tr-heading"  valign="middle"><strong><f>Contact Information</f></strong></td>
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
        <td align="left" valign="middle">State:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="state" id="state" required="required" placeholder="" value="<?php echo @$row['state']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Country:</td>
        <td  align="left" valign="middle">
        	<select name="country" id="e1" required="required" style="width: 50%;"><?php echo getCountryList($row['country']); ?></select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Mobile Phone Number:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="phone" id="phone" required="required" placeholder="Include country code (Eg. 234801546***)" value="<?php echo @$row['phone']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email:</td>
        <td  align="left" valign="middle">
        	<input type="email"  name="email" id="email" required="required" placeholder="" value="<?php echo @$row['email']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2"  class="tr-heading" valign="middle"><strong><g>Salary Information</g></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Basic Salary: <small>In <?=$userSymbul?></small></td>
        <td  align="left" valign="middle">
        	<input type="text" name="payroll" onkeypress="return isNumberKey(event)" id="payroll"  placeholder="Basic Monthly Salary" value="<?php echo @$row['payroll']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Other Allowances: <small>In <?=$userSymbul?></small></td>
        <td  align="left" valign="middle">
        	<input type="text" name="allowance" onkeypress="return isNumberKey(event)" id="payroll2" placeholder="" value="<?php echo @$row['allowance']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Employee Tax (PAYE): <small>In <?=$userSymbul?></small></td>
        <td  align="left" valign="middle">
        	<input type="text" name="paye" onkeypress="return isNumberKey(event)" id="payroll3" placeholder="" value="<?php echo @$row['paye']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Other Deductions: <small>In <?=$userSymbul?></small></td>
        <td  align="left" valign="middle">
        	<input type="text" name="deduction" onkeypress="return isNumberKey(event)" id="payroll4" placeholder="" value="<?php echo @$row['deduction']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2"  class="tr-heading" valign="middle"><strong><g>Salary Account Information</g></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Account Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="acc_name" id="acc_name" placeholder="" value="<?php echo @$row['acc_name']; ?>">
        </td>
      </tr>
       <tr>
        <td align="left" valign="middle">Account Number:</td>
        <td  align="left" valign="middle">
        	<input type="number"  name="acc_num" id="acc_num" placeholder="" value="<?php echo @$row['acc_num']; ?>">
        </td>
      </tr>
       <tr>
        <td align="left" valign="middle">Banker:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="banker" id="banker" placeholder="" value="<?php echo @$row['banker']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2"  class="tr-heading"  valign="middle"><strong><g>Other Information</g></strong></td>
      </tr>

      <tr>
        <td align="left" valign="middle">Date of Employment:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="employment_date" id="employment_date" placeholder="" value="<?php echo @$row['employment_date']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Qualifications<small>In <?=$userSymbul?></small>: </td>
        <td  align="left" valign="middle">
        	<input type="text"  name="qualification" id="qualification"  placeholder="Eg. B.Ed, B.Sc, SSCE" value="<?php echo @$row['qualification']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Designation:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="designation" id="designation"  placeholder="Eg. Head Teacher, Accountant, Librarian" value="<?php echo @$row['designation']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Assigned User Role:</td>
        <td  align="left" valign="middle">
        	<select name="role_id" id="sex"  >
               <option <?php if(@$row['role_id'] == '2') { echo 'selected';} ?> value="2"><?php echo 'Manager'; ?></option>
                <option <?php if(@$row['role_id'] == '3') { echo 'selected';} ?> value="3"><?php echo 'Accountant'; ?></option>
                <option <?php if(@$row['role_id'] == '7') { echo 'selected';} ?> value="7"><?php echo 'Front Desk'; ?></option>
               <option <?php if(@$row['role_id'] == '8') { echo 'selected';} ?> value="8"><?php echo 'Librarian'; ?></option>
                <option <?php if(@$row['role_id'] == '9') { echo 'selected';} ?> value="9"><?php echo 'Other Staffs'; ?></option>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="top">Notes/Comments:</td>
        <td  align="left" valign="middle">
        	<textarea  name="note" id="note" style="width: 100%; height: 50px;" placeholder="Type other information regarding this staff here" ><?php echo @$row['note']; ?></textarea>
        </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2"  class="tr-heading"  valign="middle"><strong>Custom Fields</strong></td>
      </tr>
     <?php
	$sql=$query = "SELECT * FROM custom_fields WHERE school_id = '$school_id' AND form = 'staff' ORDER BY id DESC";
	$result = mysqli_query($server, $sql);
	while($row3 = mysqli_fetch_assoc($result)){
	 ?>
      <tr>
        <td align="left" valign="middle"><?=$row3['label']?>:</td>
        <td  align="left" valign="middle">
		<input type="text"  name="customf[<?=$row3['id']?>]" value="<?=customFieldValue($row3['id'],@$staff); ?>">
		</td>
      </tr>
      <?php	} ?>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="staff" value="<?php echo $staff; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Imformation</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Create Staff</button>
        <?php } ?>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Changes...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}

if(isset($_POST['add']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$upload_path = 'media/uploads/';
	$file1 = $_FILES['photo']['name'];
	$filename1 = date("d-m-Y").$_FILES['photo']['name'];
	$ext = end(explode(".", $_FILES['photo']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
	} else {
		if($file1 !== "") {
			move_uploaded_file($_FILES['photo']['tmp_name'],$upload_path . $filename1);
			$photo = $filename1;
		} else {
			$photo = '';
		}

		if(empty($employment_date)) {
			$employment_date = date('Y-m-d');
		}

		if(!staffExist($first_name, $last_name, $school_id)) {
				$sql=$query ="INSERT INTO staffs (`id`, `school_id`, `first_name`, `last_name`, `sex`, `marital_status`, `address`, `city`, `state`, `country`, `photo`, `phone`, `email`, `employment_date`, `payroll`,`allowance`,`deduction`,`paye`, `qualification`, `designation`, `note`, `role_id`) VALUES (NULL, '$school_id', '$first_name', '$last_name',  '$sex', '$marital_status', '$address', '$city', '$state', '$country', '$photo',  '$phone', '$email', '$employment_date', '$payroll','$allowance','$deduction','$paye', '$qualification', '$designation', '$note', '$role_id');";
				mysqli_query($server, $query) or die(mysqli_error($server));
			//get inserted id
			$staff = getInsertedID('staffs');
			
			//insert custom fiels 
			foreach($_POST['customf'] as $num => $cuid) {
				mysqli_query($server, "INSERT INTO custom_values (`field_id`, `user_id`,`value`) VALUES ('$num', '$staff','$cuid');") or die(mysqli_error($server));
			}
			
			//create login account
			$name = $first_name.' '.$last_name;
			$username = strtolower($first_name.'.'.$last_name);
			$password = rand(19999999, 99999999);
			$salt = genRandomPassword(32);
			$crypt = getCryptedPassword($password, $salt);
			$password2 = $crypt.':'.$salt;
			if(usernameExist($username)) {
				$username = strtolower($first_name.'.'.$last_name).rand(19, 99);;
			}
			$sql=$query="INSERT INTO users (`id`, `username`, `password`, `name`, `email`, `role_id`, `profile_id`, `school_id`, `phone`) VALUES (NULL, '$username', '$password2', '$name', '$email', '$role_id', '$staff', '$school_id', '$phone');";

		  mysqli_query($server, $query) or die(mysqli_error($server));

		//update pl_pa
		$sql=$query="UPDATE staffs SET `pln_pa` = '$password' WHERE id = '$staff'";
		mysqli_query($server, $query) or die(mysqli_error($server));
		
		$newAccountEmail = 'Dear [CUSTOMER NAME].<br><br> Your '.getSetting('name').' portal login password has been created. Your login details are: <br><b>Username</b>: [USERNAME]<br> <b>Password</b>: [PASSWORD].<br><br>Please contact your school admin for more information. <hr>Note that this is an auto generated message from SOA school management software. Please discard if received in error!';
		if(empty($email)) {	$newAccountEmail = '';	}
		if(!empty($newAccountEmail)) {
			$mail = str_replace('[USERNAME]', $username, $newAccountEmail);	
			$mail = str_replace('[PASSWORD]', $password, $newAccountEmail);
			$mail = str_replace('[CUSTOMER NAME]', $name, $newAccountEmail);	
			$mail = strtr($newAccountEmail, array('[PASSWORD]'=>$password,'[CUSTOMER NAME]'=>$name,'[USERNAME]'=>$username));																
			sendEmail(getSetting('smtpUsername'),getSetting('name'),'Your '.getSetting('name').' Staff Login Details',$email,$mail,$school_id);		
		}	
		
		$message = 'The new staff was succesfully created with the following login details; <br>
		username: '.$username.' Password: '.$password.'.';
		$class = 'green';
		}else {
			$message = 'Sorry but this staff has already been created. Please use a defferent name';
		$class = 'yellow';
		}
	}
}

if(isset($_POST['save'])){
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
$staff = $_POST['staff'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "staff") && ($key != "save") && ($key != "admit") && ($key != "photo") && ($key != "subject")  && ($key != "class") && ($key != "role_id") && ($key != 'customf')) {
			$value = mysqli_real_escape_string($server, $value);
			$sql=$query="UPDATE `staffs` SET `$key` =  '$value' WHERE `id` = '$staff';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}
	
	foreach($_POST['customf'] as $num => $cuid) {
		mysqli_query($server, "DELETE FROM custom_values WHERE field_id = '$num' AND user_id = '$staff'");
		mysqli_query($server, "INSERT INTO custom_values (`field_id`, `user_id`,`value`) VALUES ('$num', '$staff','$cuid');") or die(mysqli_error($server));
	}
		
	$upload_path = 'media/uploads/';
	$file1 = $_FILES['photo']['name'];
	$filename1 = date("d-m-Y").$_FILES['photo']['name'];
	$ext = end(explode(".", $_FILES['photo']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
	} else {
		//update photo if set
		if($file1 !=="") {
			move_uploaded_file($_FILES['photo']['tmp_name'],$upload_path . $filename1);
			$sql=$query="UPDATE `staffs` SET `photo` =  '$filename1' WHERE `id` = '$staff';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}

		//update user data
		$staff_user = getUserID($staff, $role_id);
		$sql=$query="UPDATE `users` SET
			`phone` = '$phone',
			`email` = '$email'
		 WHERE `profile_id` = '$staff_user'";
		mysqli_query($server, $query) or die(mysqli_error($server));

		$message = 'The selected staff\'s profile was succesfully updated.';
		$class = 'green';
	}
}

if(isset($_REQUEST['delete'])){
	if(userRole($userID) > 2) {
	header('location: index.php');
	}

	$staff = filterinp($_REQUEST['delete']);

	//get user role
	$sql=$query="SELECT * FROM staffs WHERE id = '$staff'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['role_id'];

	$user = getUserID($staff,$picture);

	$sql=$query = "DELETE FROM users WHERE id = '$user'";
	$result = mysqli_query($server, $sql) or die(mysqli_error($server));

	$sql=$query = "DELETE FROM staffs WHERE id = '$staff'";
	$result = mysqli_query($server, $sql) or die(mysqli_error($server));

	$message = "The selected staff was successfully deleted.";
	$class="green";
}


if(isset($_GET['keyword'])){
$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', mysqli_real_escape_string($server, $_GET['keyword']));
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))   {
	         $clauses[] = "first_name LIKE '%$term%' OR last_name LIKE '%$term%' OR marital_status LIKE '%$term%' OR sex LIKE '%$term%' OR designation LIKE '%$term%'";
	    } else {
	         $clauses[] = "first_name LIKE '%%' OR address LIKE '%%' OR sex LIKE '%%' OR designation LIKE '%%' OR last_name LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM staffs WHERE school_id = '$school_id' AND $filter LIMIT $pageLimit,$setLimit";

	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {

	 $sql=$query = "SELECT * FROM staffs WHERE school_id = '$school_id' ORDER BY first_name DESC LIMIT $pageLimit,$setLimit";

	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "No records found!";
		$class="blue";
		}
	}
?>
<!-- Start Dashboard ---->
<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <input type="search" name="keyword" placeholder="Search Word" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/staff?new"><button type="button" class="submit">Add Staff</button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="3.5%" scope="col">ID</th>
                <th width="25%" scope="col">Full Name</th>
                <th width="7%" scope="col">Gender</th>
                <th width="13%" scope="col">Designtion</th>
                <th width="15.5%" scope="col">Employment Date</th>
                <th width="37%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$role = $row['role_id'];
					$user = getUserID($row['id'],$role);
					$name = $row['last_name'].' '.$row['first_name'];
					$date = date('d/m/Y', strtotime($row['employment_date']));
					$designation = $row['designation'];
					$sex = $row['sex'];

				?>
              <tr class="inner">
                <td width=""> <?php echo $id; ?></td>
                <td width=""> <?php echo $name; ?></td>
                <td width=""> <?php echo $sex; ?></td>
                <td width=""> <?php echo $designation; ?></td>
                <td width=""> <?php echo $date; ?></td>
                <td width="" valign="middle">
                <a href="admin/staff?view=<?php echo $id;?>"><button class="btn-success">Profile</button></a>
				<?php if(userRole($userID)<3) { ?>
                <a href="admin/staff?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/adminuser?edit=<?php echo $user;?>"><button class="btn-warning">Edit User</button></a>
                <a href="admin/staff?delete=<?php echo $id;?>"><button class="btn-danger">Remove</button></a>
                <a href="admin/sms?id=<?php echo $user;?>"><button>SMS</button></a>
                <a href="admin/email?id=<?php echo $user;?>"><button>Email</button></a>
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
