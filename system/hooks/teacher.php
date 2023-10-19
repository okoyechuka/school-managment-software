<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		teacher.php
Description:	This is the teacher page
Developer: 		Ynet Interactive
Date: 			10/11/2014
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbol = $userSymbul = $Currency->Symbul(getUser());

$currentSession = getSetting('current_session');
$currentTerm = getSetting('current_term');
if(userRole($userID) > 2) {
	header('location: adashboard');
}

if(isset($_REQUEST['delete'])){
	if(userRole($userID) > 2) {
		header('location: adashboard');
	}

	$teacher = filterinp($_REQUEST['delete']);
	$user = getUserID($teacher,'4');

	$sql=$query = "DELETE FROM users WHERE id = '$user'";
	$result = mysqli_query($server, $sql) or die(mysqli_error($server));

	$sql=$query = "DELETE FROM teachers WHERE id = '$teacher'";
	$result = mysqli_query($server, $sql) or die(mysqli_error($server));

	$message = "The selected teacher was successfully deleted.";
	$class="green";
}

if(isset($_REQUEST['view']))
{
	$teacher = mysqli_real_escape_string($server, $_REQUEST['view']);
		//get students profile
		$sql=$query="SELECT * FROM teachers WHERE id = '$teacher'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}

?>
<div id="add-new">
   <div id="add-new-head"><?php echo teacherName($teacher); ?>'s Credentials
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #CCC;">

	<div class="panel">
        <div class="panel-body panel-body2">
        	<div class="action-box">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print Credentials</button></a>
            <a href="admin/teacher?edit=<?php echo $teacher; ?>&done"><button class="submit">Edit</button></a>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr style="background:white;">
        <td align="left" valign="middle"><img src="media/uploads/<?php echo $picture; ?>" style="width: 50%; max-width: 200px;height: auto; border: 2px solid #999;"/></td>
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
        <td align="left" colspan="2" class="tr-heading"  valign="middle"><strong><g>Contact Information</g></strong></td>
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
        <td class="tr-heading"  colspan="2" align="left" valign="middle"><strong><g>Other Information</g></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assigned Class:</td>
        <td  align="left" valign="middle"><?php echo className(getTeacherClass($row['id'], $currentSession)); ?> </td>
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
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td class="tr-heading" colspan="2" align="left" valign="middle"><strong><g>Portal Login Details</g></strong></td>
      <tr>
        <td align="left" valign="middle">Username:</td>
        <td  align="left" valign="middle"><?php echo portalUsername($teacher, '4'); ?></td>
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
	$teacher = filterinp($_REQUEST['card']);
		//get students profile
		$sql=$query="SELECT * FROM teachers WHERE id = '$teacher'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}
	?>
        <div id="add-new">
            <div id="add-new-head"><?php echo teacherName($teacher); ?>'s ID Card
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
             <div class="inside" style="background-color: #CCC;">
             	<div id="idCard">
                	<div id="sch-logo"><img src="media/uploads/<?php echo $picture; ?>"  /></div>
                    <div id="sch-name"><?php echo getSetting('name'); ?><br /><black><?php echo getSetting('address').', '.getSetting('city').', '.getSetting('state'); ?></black></div>
                    <div id="id-title">Staff ID Card</div>
                    <div id="pasport"><img src="media/uploads/<?php echo $picture; ?>"  /></div>
                    <div id="data">
                    	<div class="title">Name: </div><div class="value"><?php echo teacherName($teacher); ?> </div>
                        <div class="title">Address: </div><div class="value"><?php echo $row['address']; ?></div>
                        <div class="title">Gender: </div><div class="value"><?php echo $row['sex']; ?></div>
                        <div class="title"> </div><div class="value"><?php echo $row['state'].', '.$row['country']; ?></div>
                        <div class="title">Emp Year: </div><div class="value"><?php echo $row['year']; ?></div>
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
	$teacher = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM teachers WHERE id = '$teacher'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo teacherName($teacher); ?>'s Profile
<?php
	} else {
		$teacher = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Teacher
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/teacher" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <tr>
        <td colspan="2" class="tr-heading"  align="left" valign="middle"><strong><f>Personal Information</f></strong></td>
      <tr>
        <td align="left" valign="middle">Photo:</td>
        <td  align="left" valign="middle">
        	<input type="file" accept="image/gif,image/png,image/jpg" style="width: 70%" name="photo" id="photo" >
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">First Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="first_name" required="required" placeholder="" value="<?php echo @$row['first_name']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Last Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="last_name" required="required" placeholder="" value="<?php echo @$row['last_name']; ?>">
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
        <td colspan="2" class="tr-heading" align="left" valign="middle"><strong><f>Contact Information</f></strong></td>
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
        <td align="left" colspan="2"  class="tr-heading" valign="middle"><strong><g>Monthly Salary Information</g></strong></td>
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
        <td align="left" colspan="2" class="tr-heading"  valign="middle"><strong><f>Other Information</f></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Date of Employment:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="employment_date" id="employment_date" placeholder="" value="<?php echo @$row['employment_date']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Designation :</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="designation" id="designation"  placeholder="Eg. Class Teacher, Head Teacher" value="<?php echo @$row['designation']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Qualifications :</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="qualification" id="qualification"  placeholder="Eg. B.Ed, B.Sc, SSCE" value="<?php echo @$row['qualification']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="top">Notes/Comments:</td>
        <td  align="left" valign="middle">
        	<textarea name="note" id="note"  placeholder="Type other information regarding this staff here" ><?php echo @$row['note']; ?></textarea>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assign Class:</td>
      <td>
        <select name="class" id="e4" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
				while($row = mysqli_fetch_assoc($result)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option <?php if(getTeacherClass($teacher) == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  $i++;
								}   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assign Subject:</td>
        <td>
        <select name="subject" id="e2" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM subject WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)){
                $c_id = $row['id'];
                $class = className($row['class_id']);
				$title = $row['title'].' ('.className($row['class_id']).')';
            ?>
               <option <?php if(getTeacherSubject($teacher) == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  $i++;}   ?>
			</select>
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
		<input type="text"  name="customf[<?=$row3['id']?>]" value="<?=customFieldValue($row3['id'],@$teacher); ?>">
		</td>
      </tr>
      <?php	} ?>
      
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="teacher" value="<?php echo $teacher; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Imformation</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Create Teacher</button>
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

if(isset($_POST['add'])){
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
		$file1 = "";
	} 
	if($file1 !== "") {
		move_uploaded_file($_FILES['photo']['tmp_name'],$upload_path . $filename1);
		$photo = $filename1;
	} else {
		$photo = '';
	}

if(empty($employment_date)) {
		$employment_date = date('Y-m-d');
	}

if(!teacherExist($first_name, $last_name, $school_id)) {
			$sql=$query = "INSERT INTO teachers (`id`, `school_id`, `first_name`, `last_name`, `sex`, `marital_status`, `address`, `city`, `state`, `country`, `photo`, `phone`, `email`, `employment_date`, `payroll`, `allowance`,`deduction`,`paye`,`qualification`,
				`designation`, `note`) VALUES (NULL, '$school_id', '$first_name', '$last_name',  '$sex', '$marital_status', '$address', '$city', '$state', '$country', '$photo',  '$phone', '$email', '$employment_date', '$payroll', '$allowance','$deduction','$paye','$qualification', '$designation', '$note');";
			mysqli_query($server, $query) or die(mysqli_error($server));
		//get inserted id
		$teacher = getInsertedID('teachers');
		
		//insert custom fiels 
		foreach($_POST['customf'] as $num => $cuid) {
			mysqli_query($server, "INSERT INTO custom_values (`field_id`, `user_id`,`value`) VALUES ('$num', '$teacher','$cuid');") or die(mysqli_error($server));
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
		$sql=$query = "INSERT INTO users (`id`, `username`, `password`, `name`, `email`, `role_id`, `profile_id`, `school_id`,`phone`) VALUES (NULL, '$username', '$password2', '$name', '$email', '4', '$teacher', '$school_id', '$phone');";
		mysqli_query($server, $query) or die(mysqli_error($server));

		//update pl_pa
		$query=$sql="UPDATE teachers SET `pln_pa` = '$password' WHERE id = '$teacher'";
		mysqli_query($server, $query) or die(mysqli_error($server));

	//insert class
		$sql=$query="SELECT * FROM teacher_class WHERE class_id = '$class'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$found = mysqli_num_rows($result);

		if($found > 0) {
			$sql=$query="UPDATE `teacher_class` SET `teacher_id` =  '$teacher' WHERE `class_id` = '$class';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		} else {
			$sql=$query="INSERT INTO teacher_class (`id`, `teacher_id`, `class_id`) VALUES (NULL, '$teacher', '$class');";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}

	//insert subject
	$sql=$query="SELECT * FROM teacher_subject WHERE subject_id = '$subject'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$found = mysqli_num_rows($result);

		if($found > 0) {
			$sql=$query="UPDATE `teacher_subject` SET `teacher_id` =  '$teacher' WHERE `subject_id` = '$subject';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		} else {
			$sql=$query="INSERT INTO teacher_subject (`id`, `teacher_id`, `subject_id`) VALUES (NULL, '$teacher', '$subject');";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
		$newAccountEmail = 'Dear [CUSTOMER NAME].<br><br> Your '.getSetting('name').' portal login password has been created. Your login details are: <br><b>Username</b>: [USERNAME]<br> <b>Password</b>: [PASSWORD].<br><br>Please contact your school admin for more information. <hr>Note that this is an auto generated message from SOA school management software. Please discard if received in error!';
		if(empty($email)) {	$newAccountEmail = '';	}
			
		if(!empty($newAccountEmail)) {
			$mail = str_replace('[USERNAME]', $username, $newAccountEmail);	
			$mail = str_replace('[PASSWORD]', $password, $newAccountEmail);
			$mail = str_replace('[CUSTOMER NAME]', $name, $newAccountEmail);	
			$mail = strtr($newAccountEmail, array('[PASSWORD]'=>$password,'[CUSTOMER NAME]'=>$name,'[USERNAME]'=>$username));																
			sendEmail(getSetting('smtpUsername'),getSetting('name'),'Your '.getSetting('name').' Staff Login Details',$email,$mail,$school_id);		
		}	
	$message = 'The new teacher was succesfully created with the following login details;<br>	Username: '.$username.' Password: '.$password;
	$class = 'green';
	}else {
		$message = 'Sorry but this teacher has already been created. Please use a defferent name';
	$class = 'yellow';
	}

}

if(isset($_POST['save'])){
$teacher = $_POST['teacher'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "teacher") && ($key != "save") && ($key != "admit") && ($key != "subject")  && ($key != "photo") && ($key != "class") && ($key != "parent" && ($key != 'customf'))) {
			$value = mysqli_real_escape_string($server, $value);
			$sql=$query="UPDATE `teachers` SET `$key` =  '$value' WHERE `id` = '$teacher';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}
	foreach($_POST['customf'] as $num => $cuid) {
		mysqli_query($server, "DELETE FROM custom_values WHERE field_id = '$num' AND user_id = '$teacher'");
		mysqli_query($server, "INSERT INTO custom_values (`field_id`, `user_id`,`value`) VALUES ('$num', '$teacher','$cuid');") or die(mysqli_error($server));
	}
	$upload_path = 'media/uploads/';
	$file1 = $_FILES['photo']['name'];
	$filename1 = date("d-m-Y").$_FILES['photo']['name'];
	$ext = end(explode(".", $_FILES['photo']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
		$file1 = "";
	} 
	//update photo if set
	if($file1 !=="") {
		move_uploaded_file($_FILES['photo']['tmp_name'],$upload_path . $filename1);
		$sql=$query="UPDATE `teachers` SET `photo` =  '$filename1' WHERE `id` = '$teacher';";
		mysqli_query($server, $query) or die(mysqli_error($server));
	}
	
	//update user
	$user_i = getUserID($teacher,'4');
	$sql=$query="UPDATE `users` SET `phone` =  '$phone', `email` = '$email' WHERE `id` = '$user_i';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	
	//update parents
	if(isset($_POST['class'])) {
		$class = $_POST['class'];
	//update class
	$sql=$query="UPDATE `teacher_class` SET `class_id` =  '$class' WHERE `teacher_id` = '$teacher';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	}
	$message = 'The selected teacher\'s profile was succesfully updated.';
	$class = 'green';
}
if(isset($_GET['keyword'])){
	$class_id = $_GET['class_id'];
	$subject_id = $_GET['subject'];
	
	$school_id = $school_id;
	
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
	    if (!empty($term))    {
	         $clauses[] = "s.first_name LIKE '%$term%' OR s.last_name LIKE '%$term%' OR s.marital_status LIKE '%$term%' OR s.sex LIKE '%$term%' OR s.state LIKE '%$term%'";
	    }    else    {
	         $clauses[] = "s.first_name LIKE '%%' OR s.address LIKE '%%' OR s.sex LIKE '%%' OR s.state LIKE '%%' OR s.last_name LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select s.* FROM teachers s WHERE s.school_id = '$school_id' AND $filter LIMIT $pageLimit,$setLimit ";
	if(!empty($class_id)) {
		$sql=$query = "select s.* FROM teachers s JOIN teacher_class sc ON s.id = sc.teacher_id WHERE s.school_id = '$school_id' AND sc.class_id = '$class_id' AND $filter  LIMIT $pageLimit,$setLimit";
	}

	if(!empty($subject_id)) {
		$sql=$query = "select s.* FROM teachers s JOIN teacher_subject sc ON s.id = sc.teacher_id WHERE s.school_id = '$school_id' AND sc.subject_id = '$subject_id' AND $filter LIMIT $pageLimit,$setLimit";
	}

 	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM teachers WHERE school_id = '$school_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")	{
		$message = "No teachers records found!";
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
        &nbsp;
        <select name="subject" id="e3" style="width: 140px; font-size:14px; text-align:left; margin: 0 20px;" >
			<?php
			echo '<option value="">Select Subjects</option>';
			     $sqlC=$queryC="SELECT * FROM subject WHERE school_id = '$school_id' ORDER BY id DESC";
				$subject = getTeacherSubject($teacher);
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);
				while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'].' ('.className($row['class_id']).')';
            ?>
               <option <?php if(@$subject == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  }   ?>
			</select>
        &nbsp;
        <select name="class_id" id="e4" style="width: 140px; font-size:14px; text-align:left; margin: 0 20px;" >
			<?php
				echo '<option value="">Select Classes</option>';
				$class = getTeacherClass($teacher);
			    $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
			    $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);
				while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option <?php if(@$class == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  }   ?>
			</select>
            &nbsp;
        <input type="search" name="keyword" placeholder="Search Word" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/teacher?new"><button type="button" class="submit">Add Teacher</button></a>
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
                <th width="13%" scope="col">Class</th>
                <th width="15.5%" scope="col">Subject</th>
                <th width="37%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row=mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$class = className(getTeacherClass($row['id']));
					$user = getUserID($row['id'],'4');
					$name = $row['last_name'].' '.$row['first_name'];
					$subject = subjectName(getTeacherSubject($row['id']));
					$class = className(getTeacherClass($row['id']));
					$sex = $row['sex'];
					$user = getUserID($row['id'],'4');

				?>
            
              <tr class="inner">
                <td width=""> <?php echo sprintf('%07d',$id); ?></td>
                <td width=""> <?php echo $name; ?></td>
                <td width=""> <?php echo $sex; ?></td>
                <td width=""> <?php echo $class; ?></td>
                <td width=""> <?php echo $subject; ?></td>
                <td width="" valign="middle">
                <a href="admin/teacher?view=<?php echo $id;?>"><button class="btn-success">Profile</button></a>
				<?php if(userRole($userID)<3) { ?>
                <a href="admin/teacher?edit=<?php echo $id;?>"><button class="btn-warning">Edit</button></a>
                <a href="admin/adminuser?edit=<?php echo $user;?>"><button>Edit User</button></a>
                <a href="admin/teacher?delete=<?php echo $id;?>"><button class="btn-danger">Remove</button></a>
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
