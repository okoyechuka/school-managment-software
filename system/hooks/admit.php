<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

if(userRole($userID) > 2) {
header('location: adashboard');
}

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');


$state = '';
if(countSchoolClass() < 1) {
$message = 'You have not created any classes  yet!. <br>You must create at-least one class before you can start admitting students. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/class?new" title="Create New Class">Click Here</a> to create new classes.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
?>
<div class="wrapper">
	<div class="inner-left" style="width: 98%;">
  <div id="mess" style="position: relative; top: 0;"> <p style="border:1px solid #036; padding:20px;"><?=$message?></p><?php if(!empty($message)) { showMessage($message,$class,0); } ?> </div>
<?php
} elseif (getSetting('current_session') < 1) {
$message = 'You have not defined the current accademic session yet!. <br>You must fix this before you can start admitting students. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/session" title="Define Active Session">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
?>
<div class="wrapper">
	<div class="inner-left" style="width: 90%;">
  <div id="mess" style="position: relative; top: 0;"> <p style="border:1px solid #036; padding:20px;"><?=$message?></p> <?php if(!empty($message)) { showMessage($message,$class,0); } ?> </div>
<?php
} elseif(getSetting('current_term') < 1) {
$message = 'You have not defined the current accademic term yet!. <br>You must fix this before you can start admitting students. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/term" title="Define Active Term">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
?>
<div class="wrapper">
	<div class="inner-left" style="width: 90%;">
  <div id="mess" style="position: relative; top: 0;"> <p style="border:1px solid #036; padding:20px;"><?=$message?></p> <?php if(!empty($message)) { showMessage($message,$class,0); } ?> </div>
<?php
} elseif(getSetting('graduate_class_id') < 1) {
$message = 'You have not defined the school\'s graduation class yet!. <br>You must fix this before you can start admitting students. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/generalsetting" title="Define Graduate Class">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
?>
<div class="wrapper">
	<div class="inner-left" style="width: 90%;">
  <div id="mess" style="position: relative; top: 0;"> <p style="border:1px solid #036; padding:20px;"><?=$message?></p> <?php if(!empty($message)) { showMessage($message,$class,0); } ?> </div>
<?php
} else {
	$current_session = getSetting('current_session');
	$current_term = getSetting('current_term');
if(isset($_POST['admit'])) {
	//get submitted data
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	//Upload files

	$upload_path = 'media/uploads/';
	$file2 = $_FILES['photo']['name'];
	$filename = date("d-m-Y").$_FILES['photo']['name'];
	$ext = end(explode(".", $_FILES['photo']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
	} else {
		if(isset($_POST['applicant'])) {
			 $file3 = $_POST['applicant'];
			 $ext = end(explode(".",$file3));
			 $filename =rand(10000000,9999999).'.'.$ext;
		}

		if(move_uploaded_file($_FILES['photo']['tmp_name'],$upload_path . $filename) || ($file2 =="") || (copy($file3,$filename))){
			if(@$file2 =="" && @$file3 == "") { $filename = '';}
			//create new student
			if(empty($admission_number)) {
			$admission_number = getAdmissionNumber($school_id,$year);
			}

			if(isset($_POST['applicant_id'])) {
				$sql=$query="UPDATE `applicants` SET `status` =  'Admitted' WHERE `id` = '$applicant_id';";
				mysqli_query($server, $query) or die(mysqli_error($server));
			}

			if(!studentExist($first_name, $last_name, $other_name, $school_id)) {
				$sql=$query= "INSERT INTO students (`id`, `school_id`, `first_name`, `last_name`, `other_name`, `sex`, `date_of_birth`, `address`, `city`, `local_council`, `state`, `country`, `portal_access`, `nationality`, `state_origin`, `admission_number`, `bload_group`, `photo`, `status`, `hostel_id`, `year`,`phone`, `email`)
				VALUES (NULL, '$school_id', '$first_name', '$last_name', '$other_name', '$sex', '$date_of_birth', '$address', '$city', '$local_council', '$state', '$country', '1', '$nationality', '$state_origin', '$admission_number', '$bload_group', '$filename', '1', '$hostel_id', '$year','$phone', '$email');";
				mysqli_query($server, $query) or die(mysqli_error($server));
				//get inserted id
				$student = mysqli_insert_id($server);
				global $hooks;
				$vars = array("id"=>$student );
				$_SESSION['EventVals'] = $vars;
				$hooks->do_action('OnStudentAdmin'); 
				//insert custom fiels 
				foreach($_POST['customf'] as $num => $cuid) {
					mysqli_query($server, "INSERT INTO custom_values (`field_id`, `user_id`,`value`) VALUES ('$num', '$student','$cuid');") or die(mysqli_error($server));
				}
				
				$_SESSION['student'] = $student;
				//create login account
				$name = $first_name.' '.$last_name;
				$username = rand(19999999, 99999999);
				$password = rand(19999999, 99999999);
				$_SESSION['student_password'] = $password;
				$salt = genRandomPassword(32);
				$crypt = getCryptedPassword($password, $salt);
				$password2 = $crypt.':'.$salt;
				if(usernameExist($username)) {
					$username = rand(19999999, 99999999)+rand(100, 999);
				}
				$_SESSION['student_username'] = $username;
				$sql=$query="INSERT INTO users (`id`, `username`, `password`, `name`, `email`, `role_id`, `profile_id`, `school_id`, `phone`)
							VALUES (NULL, '$username', '$password2', '$name', '$email', '6', '$student', '$school_id', '$phone');";
				mysqli_query($server, $query) or die(mysqli_error($server));

				//update pl_pa
				$sql=$query="UPDATE students SET `pln_pa` = '$password' WHERE id = '$student'";
				mysqli_query($server, $query) or die(mysqli_error($server));
				//assign class
				$sql=$query= "INSERT INTO student_class (`id`, `student_id`, `class_id`, `session_id`) VALUES (NULL, '$student', '$class', '$current_session');";
				mysqli_query($server, $query) or die(mysqli_error($server));
				//assign hostel
				if($hostel_id > 0 ) {
					$sql=$query="INSERT INTO student_hostel (`id`, `student_id`, `hostel_id`) VALUES (NULL, '$student', '$hostel_id');";
					mysqli_query($server, $query) or die(mysqli_error($server));
				}
				if($bus_id > 0 ) {
					$sql=$query="INSERT INTO student_bus (`id`, `student_id`, `bus_id`) VALUES (NULL, '$student', '$bus_id');";
					mysqli_query($server, $query) or die(mysqli_error($server));
				}
				//check if parent is defined
				if($parent > 0 ) {
					$_SESSION['parent'] = $parent;
					$sql=$query="INSERT INTO student_parent (`id`, `student_id`, `parent_id`) VALUES (NULL, '$student', '$parent');";
					mysqli_query($server, $query) or die(mysqli_error($server));
					//redirect to report
					header('location: admit?done');
				}else {
					//display parent creation form
					$message = 'The new student\'s profile has been saved.<br>Please provide the parent\'s details to complete the admission process.';
					$class = 'blue';
					?>
					<div class="wrapper">
						<div class="inner-left" style="">
					  <div id="mess" style="position: relative; top: 0;"> <?php if(!empty($message)) { showMessage($message,$class); } ?> </div>
					<form method="post" action="" enctype="multipart/form-data">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="data_form">
					  <tr>
						<td colspan="2" class="tr-heading" align="left" valign="middle"><strong>Father's Information</strong></td>
						</tr>
					  <tr>
						<td align="left" valign="middle">Father's Photo:</td>
						<td  align="left" valign="middle">
							<input type="file" accept="image/gif,image/png,image/jpg" style="width: 70%" name="father_photo" id="father_photo" >
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle">Father's Full Name:</td>
						<td  align="left" valign="middle">
							<input type="text"  name="father_name" id="father_name" required="required" placeholder="" value="<?php echo @$father_name; ?>">
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle">Father's Occupation: <small>(Optional)</small></td>
						<td  align="left" valign="middle">
							<input type="text"  name="father_occupation" id="father_occupation" placeholder="Eg. Banker, etc" value="<?php echo @$father_occupation; ?>">
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
					  </tr>
					  <!-- Mother Info -->
					  <tr>
						<td colspan="2" class="tr-heading" align="left" valign="middle"><strong>Mother's Information</strong></td>
					  <tr>
						<td align="left" valign="middle">Mather's Photo:</td>



						<td  align="left" valign="middle">
							<input type="file" accept="image/gif,image/png,image/jpg" style="width: 70%" name="mother_photo" id="mother_photo" >
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle">Mather's Full Name:</td>
						<td  align="left" valign="middle">
							<input type="text"  name="mother_name" id="mother_name" required="required" placeholder="" value="<?php echo @$mother_name; ?>">
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle">Mother's Occupation: <small>(Optional)</small></td>
						<td  align="left" valign="middle">
							<input type="text"  name="mother_occupation" id="mother_occupation" placeholder="Eg. Accountant, etc" value="<?php echo @$mother_occupation; ?>">
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
					  </tr>
					  <tr>
						<td colspan="2" class="tr-heading" align="left" valign="middle"><strong>Contact Information</strong></td>
					  </tr>
					  <tr>
						<td align="left" valign="middle">Residence Address:</td>
						<td  align="left" valign="middle">
							<input type="text" name="address" id="address" required="required" placeholder="" value="<?php echo @$address; ?>">
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle">City:</td>
						<td  align="left" valign="middle">
							<input type="text"  name="city" id="city" required="required" placeholder="" value="<?php echo @$city; ?>">
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle">State of Residence:</td>
						<td  align="left" valign="middle">
							<input type="text"  name="state" id="state" required="required" placeholder="" value="<?php echo @$state; ?>">
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle">Country of Residence:</td>
						<td  align="left" valign="middle">
							<select name="country" id="e1" required="required" style="width: 50%;"><?php echo getCountryList($country); ?></select>
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle">Email Address:</td>
						<td  align="left" valign="middle">
							<input type="email"  name="email" id="email" placeholder="" value="<?php echo @$email; ?>">
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle">Contact Phone Numnber:</td>
						<td  align="left" valign="middle">
							<input type="text"  name="phone" id="phone" required="required" placeholder="Include country code eg. 2348031234567" value="<?php echo @$phone; ?>">
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle">Alternative Phone Numnber:</td>
						<td  align="left" valign="middle">
							<input type="text"  name="phone2" id="phone2" placeholder="Include country code eg. 2348031234567" value="<?php echo @$phone2; ?>">
						</td>
					  </tr>
					  <tr>
						<td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
					  </tr>
					  <tr>
						<td colspan="2" class="tr-heading" align="left" valign="middle"><strong>Other Information</strong></td>
					  </tr>
					  <tr>

						<td align="left" valign="middle">Authorization Code:</td>
						<td  align="left" valign="middle">
							<input type="text"  name="authorization_code" id="authorization_code" placeholder="This code will be requested to confirm parent's representatives" value="<?php echo @$authorization_code; ?>">
						</td>
					  </tr>
					  
					   <tr>
						<td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
					  </tr>
					  <tr>
						<td align="left" colspan="2"  class="tr-heading"  valign="middle"><strong>Custom Fields</strong></td>
					  </tr>
					 <?php
					$sql=$query = "SELECT * FROM custom_fields WHERE school_id = '$school_id' AND form = 'student' ORDER BY id DESC";
					$result = mysqli_query($server, $sql);
					while($row3 = mysqli_fetch_assoc($result)){
					 ?>
					  <tr>
						<td align="left" valign="middle"><?=$row3['label']?>:</td>
						<td  align="left" valign="middle">
						<input type="text"  name="customf[<?=$row3['id']?>]" value="">
						</td>
					  </tr>
					  <?php	} ?>
						
					  <!-- Submit Buttons -->
					  <tr>
						<td align="left" valign="top">&nbsp;</td>
						<td width="69%" align="left" valign="top">
						<input type="hidden" name="save" value="yes" />
						<input type="hidden" name="student_id" value="<?php echo $student; ?>" />
						<button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="save_parent" value="1" type="submit">Create Parents & Continue</button>
					</form>
						<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Creating Parents...</div>
						</td>
					  </tr>
					</table>
				  </div>
				</div>
	<?php   	//die();
				}  //end if parent exist
			} else { //end of student exist
				$message = 'Oops!<br> '.$first_name.' '.$last_name.' '.$other_name. ' already has an admission record in your school. You can use the existing record or use different names if this is another student.';
				$class = 'yellow';
			}
		}else {
			$message = 'Oops!<br> An error was encountered while uploading the student\'s photo. Please try again or contact your vendor if this error continues.';
			$class = 'yellow';
		}
	}
}

if(isset($_POST['save_parent'])) {
	//get submitted data
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	//Upload files

	$upload_path = 'media/uploads/';
	$file1 = $_FILES['mother_photo']['name'];
	$filename1 = date("d-m-Y").$_FILES['mother_photo']['name'];
	$ext = end(explode(".", $_FILES['mother_photo']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
		$file1 = "";
	} 
	
	$file2 = $_FILES['father_photo']['name'];
	$filename2 = date("d-m-Y").$_FILES['father_photo']['name'];
	$ext = end(explode(".", $_FILES['father_photo']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
		$file2 = "";
	} 
	
	if($file1 !=="") {	move_uploaded_file($_FILES['mother_photo']['tmp_name'],$upload_path . $filename1); }else {$filename1 = '';}
	if($file2 !=="") {	move_uploaded_file($_FILES['father_photo']['tmp_name'],$upload_path . $filename2); }else {$filename2 = '';}

	//create new prents
	if(!parentExist($father_name, $mother_name, $school_id)) {
		$sql=$query="INSERT INTO parents (`id`, `school_id`, `father_name`, `mother_name`, `father_occupation`, `mother_occupation`, `father_photo`, `mother_photo`,
			`address`, `city`, `state`, `country`, `email`, `phone`, `phone2`, `authorization_code`) VALUES (NULL, '$school_id', '$father_name', '$mother_name',
				'$father_occupation', '$mother_occupation', '$filename2', '$filename1', '$address', '$city', '$state', '$country', '$email', '$phone', '$phone2',
				'$authorization_code');";
		mysqli_query($server, $query) or die(mysqli_error($server));
		//get inserted id
		$parent = getInsertedID('parents');
		//link to student
		$sql=$query="INSERT INTO student_parent (`id`, `student_id`, `parent_id`) VALUES (NULL, '$student_id', '$parent');";
		mysqli_query($server, $query) or die(mysqli_error($server));

		$_SESSION['parent'] = $parent;
		//create login account
		$name = 'Mr & Mrs '.$father_name;
		$username = rand(19999999, 99999999);
		$password = rand(19999999, 99999999);
		$_SESSION['parent_password'] = $password;
		$salt = genRandomPassword(32);
		$crypt = getCryptedPassword($password, $salt);
		$password2 = $crypt.':'.$salt;
		if(usernameExist($username)) {
			$username = rand(19999999, 99999999)+rand(100, 999);
		}
		$_SESSION['parent_username'] = $username;

		$sql=$query="INSERT INTO users (`id`, `username`, `password`, `name`, `email`, `role_id`, `profile_id`, `school_id`, `phone`)
						VALUES (NULL, '$username', '$password2', '$name', '$email', '5', '$parent', '$school_id', '$phone');";

		mysqli_query($server, $query) or die(mysqli_error($server));

		//update pln_pa
		$sql=$query="UPDATE parents SET `pln_pa` = '$password' WHERE id = '$parent'";
		mysqli_query($server, $query) or die(mysqli_error($server));
	} else {//end of parent exist
		$sql=$query= "SELECT * FROM parents WHERE father_name = '$father_name' AND mother_name = '$mother_name' AND school_id = '$school_id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$parent = $row['id'];
		$sql=$query="INSERT INTO student_parent (`id`, `student_id`, `parent_id`) VALUES (NULL, '$student_id', '$parent');";
		mysqli_query($server, $query) or die(mysqli_error($server));
		$_SESSION['parent'] = $parent;
	}
	header('location: admit?done');
}

if(isset($_REQUEST['done'])) {
		$student = $_SESSION['student'];
		$parent = $_SESSION['parent'];
		if(!isset($_REQUEST['card']))
		$message = 'The new student\'s profile was succesfully created. <br>Here are the credentials and Portal Login details:';
		$class = 'green';
		//get students profile
		$sql=$query="SELECT * FROM students WHERE id = '$student'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}
		//get parent profile
		$sql=$query2="SELECT * FROM parents WHERE id = '$parent'";
		$result2 = mysqli_query($server, $query2) or die(mysqli_error($server));
		$row2 = mysqli_fetch_assoc($result2);
		$father_name = $row2['father_name'];
		?>
		<div class="wrapper">
		<div class="inner-left" style="width:100%">
		  <div id="mess" style="position: relative; top: 0;"> <?php if(!empty($message)) { showMessage($message,$class); } ?> </div>
	<?php if(isset($_REQUEST['card']) && !empty($_REQUEST['card'])) {
	$student = filterinp($_REQUEST['card']);
	?>
        <div id="add-new">
            <div id="add-new-head"><?php echo studentName($student); ?>'s ID Card
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
             <div class="inside" style="background-color: #CCC;">
             	<div id="idCard">
                	<div id="sch-logo"><img src="media/uploads/<?php echo getSetting('logo'); ?>"  /></div>
                    <div id="sch-name"><?php echo getSetting('name'); ?><br /><black><?php echo getSetting('address').', '.getSetting('city').', '.getSetting('state'); ?></black></div>
                    <div id="id-title">Student's ID Card</div>
                    <div id="pasport"><img src="media/uploads/<?php echo $picture; ?>" style="border:1px solid #ccc" />
                    <p style="text-align: center">
				<?php
				$colorFront = new BCGColor(0, 0, 0);
				$colorBack = new BCGColor(255, 255, 255);
				$font = new BCGFont(BASEPATH.'Barcode/font/Arial.ttf', 18);
				$code = new BCGcode128();
				$code->setScale(3);
				$code->setThickness(20);
				$code->setForegroundColor($colorFront);
				$code->setBackgroundColor($colorBack);
				$code->setFont($font);
				$code->parse($row['id']);
				$drawing = new BCGDrawing('media/images/'.$row['id'].'.png', $colorBack);
				$drawing->setBarcode($code);
				$drawing->draw();
				$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
				?>
                <img src="media/images/<?php echo $row['id'].'.png'; ?>" style="width: 80%; margin-top: -17px;" />
                </p>
                </div>
                    <div id="data">
                    	<div class="title">Name: </div><div class="value"><?php echo studentName($student); ?> </div>
                        <div class="title">Gender: </div><div class="value"><?php echo $row['sex']; ?></div>
                        <div class="title">Class: </div><div class="value"><?php echo className(getClass($student,$currentSession)); ?></div>
                        <div class="title">Adm. No.: </div><div class="value"><?php echo $row['admission_number']; ?></div>
                        <div class="title">Adm Year: </div><div class="value"><?php echo $row['year']; ?></div>
                    </div>
                </div>
                <div id="id-print">
                This generated ID Card has been optimized for use with any 3.5" X 2" Plastic Card Printer.<br /><br />
                <a href="" onClick="javascript:printDiv('idCard')">
                <button class="submit">Print ID Card</button></a></div>
             </div>
        </div>
<?php } ?>
<!-- Display Credential -------------------------------------->
	<a href="admin/admit"><button class="submit">Admit New Student</button></a>
	<div class="panel">
    	<div class="panel-head"><img src="media/images/list-white.png" />Credentials</div>
        <div class="panel-body panel-body2">
        	<div class="action-box">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print Credentials</button></a>
            <a href="admin/admit?card=<?php echo $student; ?>&done"><button class="submit">Generate ID Card</button></a>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0" class="data_form">
      <tr style="background-color:white;">
        <td align="left" valign="middle"><img src="media/uploads/<?php echo $picture; ?>" style="width: 70%; height: auto; border: 2px solid #999; max-width: 200px;"/></td>
        <td align="left" valign="middle"></td>
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
        <td align="left" valign="middle">Bload Group: </td>
        <td  align="left" valign="middle"><?php echo $row['bload_group']; ?> </td>
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
        <td  align="left" valign="middle"><?php echo countryName($row['country']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Nationality:</td>
        <td  align="left" valign="middle"><?php echo countryName($row['nationality']); ?></td>
      </tr>
       <tr>
        <td align="left" valign="middle">Phone Number:</td>
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
        <td align="left" colspan="2" class="tr-heading"  valign="middle"><strong>Other Information</strong></td>
      </tr>

      <tr>
        <td align="left" valign="middle">Admission Year:</td>
        <td  align="left" valign="middle"><?php echo $row['year']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Admission Number:</td>
        <td  align="left" valign="middle"><?php echo $row['admission_number']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assigned Class:</td>
        <td  align="left" valign="middle"><?php echo className(getClass($row['id'],$currentSession)); ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assigned Hostel:</td>
        <td  align="left" valign="middle"><?php echo hostelName(getHostel($row['id'])); ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assigned School Bus:</td>
        <td  align="left" valign="middle"><?php echo busName(getBus($row['id'])); ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2" class="tr-heading" valign="middle"><strong>Parents' Information</strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Father's Full Name:</td>
        <td  align="left" valign="middle"><?php echo $row2['father_name']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Mother's Full Name: </td>
        <td  align="left" valign="middle"><?php echo $row2['mother_name']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Residence Address:</td>
        <td  align="left" valign="middle"><?php echo $row2['address'].', '.$row2['city'].' '.$row2['state'].' '.countryName($row2['country']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email Address:</td>
        <td  align="left" valign="middle"><?php echo $row2['email']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Contact Phone Numnber:</td>
        <td  align="left" valign="middle"><?php echo $row2['phone']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Alternative Phone Numnber:</td>
        <td  align="left" valign="middle"><?php echo $row2['phone2']; ?></td>
      </tr>
		
       <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2"  class="tr-heading"  valign="middle"><strong>Custom Fields</strong></td>
      </tr>
     <?php
	$sql=$query = "SELECT * FROM custom_fields WHERE school_id = '$school_id' AND form = 'student' ORDER BY id DESC";
	$result = mysqli_query($server, $sql);
	while($row3 = mysqli_fetch_assoc($result)){
	 ?>
      <tr>
        <td align="left" valign="middle"><?=$row3['label']?>:</td>
        <td  align="left" valign="middle"><?=customFieldValue($row3['id'],$row['id']) ?></td>
      </tr>
      <?php	} ?> 
        
      <?php if(isset($_SESSION['student_username'])) { ?>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td colspan="2" class="tr-heading" align="left" valign="middle"><strong>Student's Portal Login Details</strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Username:</td>
        <td  align="left" valign="middle"><?php echo $_SESSION['student_username']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Password:</td>
        <td  align="left" valign="middle"><?php echo $_SESSION['student_password']; ?> </td>
      </tr>
      <?php } ?>
       <?php if(isset($_SESSION['parent_username'])) { ?>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td colspan="2" class="tr-heading" align="left" valign="middle"><strong>Parent's Portal Login Details</strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Username:</td>
        <td  align="left" valign="middle"><?php echo $_SESSION['parent_username']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Password:</td>
        <td  align="left" valign="middle"><?php echo $_SESSION['parent_password']; ?> </td>
      </tr>
   </table>
      <?php } ?>
      </div>
    </div>
	</div>
<?php
} else {
$nationality = '';
$country = '';
$id = '';
$year = date('Y');

if(isset($_POST['transfer_student'])) {
	//get applicant's data
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
}
?>

<div class="wrapper">
	<div class="inner-left" style="width:100%;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
    <form method="post" action="" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="data_form">
      <tr>
        <td colspan="2" class="tr-heading" align="left" valign="middle"><strong>Personal Information</strong></td>
	  </tr>
      <tr>
        <td align="left" valign="middle">Student's Photo:</td>
        <td  align="left" valign="middle">
        	<input type="file" accept="image/gif,image/png,image/jpg" style="width: 70%" name="photo" id="photo" >
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">First Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="first_name"  required="required" placeholder="" value="<?php echo @$first_name; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Last Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="last_name"  required="required" placeholder="" value="<?php echo @$last_name; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Other Names:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="other_name" placeholder="" value="<?php echo @$other_name; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Gender:</td>
        <td  align="left" valign="middle">
        <select name="sex" id="sex" style="width:90%" >
               <option <?php if(@$sex == 'Male') { echo 'selected';} ?> value="Male"><?php echo 'Male'; ?></option>
               <option <?php if(@$sex == 'Female') { echo 'selected';} ?> value="Female"><?php echo 'Female'; ?></option>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Date of Birth:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="date_of_birth" id="date_of_birth" required="required" placeholder="" value="<?php echo @$date_of_birth; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Bload Group: <small>(Optional)</small></td>
        <td  align="left" valign="middle">
        	<input type="text"  name="bload_group" id="bload_group" placeholder="Eg. O+, A, etc" value="<?php echo @$bload_group; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td colspan="2" class="tr-heading" align="left" valign="middle"><strong>Contact Information</strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Residence Address:</td>
        <td  align="left" valign="middle">
        	<input type="text" name="address" id="address" required="required" placeholder="" value="<?php echo @$address; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">City:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="city" id="city" required="required" placeholder="" value="<?php echo @$city; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Local Council / LGA <small>(Optional)</small>:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="local_council" id="council" placeholder="Local Government Area " value="<?php echo @$local_council; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">State of Residence:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="state" id="state" required="required" placeholder="" value="<?php echo @$state; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">State of Origin:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="state_origin" id="state_origin" required="required" placeholder="" value="<?php echo @$state_origin; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Country of Residence:</td>
        <td  align="left" valign="middle">
        	<select name="country" id="e1" required="required" style="width: 90%;"><?php echo getCountryList($country); ?></select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Nationality:</td>
        <td  align="left" valign="middle">
        	<select name="nationality" id="e2" required="required" style="width: 90%;"><?php echo getCountryList($country); ?></select>
        </td>
      </tr>
       <td align="left" valign="middle">Phone:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="phone" id="phone" placeholder="Include country code" value="<?php echo @$phone; ?>">
        </td>
      </tr>
       <td align="left" valign="middle">Email:</td>
        <td  align="left" valign="middle">
        	<input type="email"  name="email" id="email"  placeholder="" value="<?php echo @$email; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td colspan="2" class="tr-heading" align="left" valign="middle"><strong>Other Information</strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Admission Year:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="year" id="year" required="required" placeholder="" value="<?php echo @$year; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Admission Number:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="admission_number" id="admission_number" readonly placeholder="This will be automatically assigned by the system." value="<?php echo @$admission_number; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assign Parent:</td>
        <td  align="left" valign="middle">
        <select name="parent" id="e3" style="width: 90%;" >
			<?php
        	if(empty($id)) { echo '<option value="0" selected >Create New Parents</option>'; }
                $sql=$query="SELECT * FROM parents WHERE school_id = '$school_id' ORDER BY father_name ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row=mysqli_fetch_assoc($result)){
		                $p_id = $row['id'];
		                $title = $row['father_name'].' & '.$row['mother_name'];
				            ?>
				               <option <?php if(getParent($id) == $p_id) { echo 'selected';} ?> value="<?php echo $p_id; ?>"><?php echo $title; ?></option>
		            	<?php
									$i++;
								}
					?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assign Class:</td>
        <td  align="left" valign="middle">
        <select name="class" id="e4" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
                while($row=mysqli_fetch_assoc($result)){
		                $c_id = $row['id'];
		                $title = $row['title'];
				            ?>
				               <option <?php if(getClass($id,$currentSession) == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
		            		<?php
										$i++;
							}   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assign Hostel:</td>
        <td  align="left" valign="middle">
        <select name="hostel_id" id="e5" style="width: 90%;" >
        <option value="0">None</option>
		<?php
             $sql=$query="SELECT * FROM hostels WHERE school_id = '$school_id' ORDER BY title ASC";
             $result = mysqli_query($server, $query);
             while($row = mysqli_fetch_assoc($result)){
               $h_id = $row['id'];
               $title = $row['title'];            ?>
               <option <?php if(getHostel($id) == $h_id) { echo 'selected';} ?> value="<?php echo $h_id; ?>"><?php echo $title; ?></option>
            <?php	}   ?>
		</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assign Bus:</td>
        <td  align="left" valign="middle">
        <select name="bus_id" id="e5" style="width: 90%;" >
        <option value="0">None</option>
		<?php
             $sql=$query="SELECT * FROM vehicles WHERE school_id = '$school_id' ORDER BY title ASC";
             $result = mysqli_query($server, $query);
             while($row = mysqli_fetch_assoc($result)){
               $h_id = $row['id'];
               $title = $row['title'];            ?>
               <option <?php if(getBus($id) == $h_id) { echo 'selected';} ?> value="<?php echo $h_id; ?>"><?php echo $title; ?></option>
            <?php	}   ?>
		</select>
        </td>
      </tr>
 
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_POST['transfer_student'])) { ?>
        <input type="hidden" name="applicant" value="<?php $_POST['photo']; ?>" />
        <input type="hidden" name="applicant_id" value="<?php $_POST['applicant_id']; ?>" />
        <?php } ?>
        <input type="hidden" name="save" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Enroll Student</button>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Changes...</div>
        </td>
      </tr>
    </table>
	</form>
      </div>
</div>
<?php } }  ?>