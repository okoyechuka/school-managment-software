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
if(userRole($userID) == 5 ||userRole($userID) == 6) {
header('location: dashboard');
}
$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

$currentSession = getSetting('current_session');
$currentTerm = getSetting('current_term');
if(isset($_GET['export'])) {
	exportToCSV('Students_','students','admission_number,first_name,last_name,other_name,date_of_birth,sex,email,phone,city,state'," WHERE school_id = '".$school_id."'");
	exit;
}		

if(isset($_REQUEST['view'])){
	$student = mysqli_real_escape_string($server, $_REQUEST['view']);
		//get students profile
		$sql=$query="SELECT * FROM students WHERE id = '$student'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}
		//get parent profile
		$parent = getParent($student);
		$sql2=$query2="SELECT * FROM parents WHERE id = '$parent'";
		$result2 = mysqli_query($server, $query2) or die(mysqli_error($server));
		$row2 = mysqli_fetch_assoc($result2);
		$father_name = $row2['father_name'];
?>
<div id="add-new">
   <div id="add-new-head"><?php echo studentName($student); ?>'s Credentials
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
        	<div class="action-box">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print Credentials</button></a>
            <?php if(userRole($userID) < 3) {?>
            <a href="admin/student?card=<?php echo $student; ?>&done"><button class="submit">Generate ID Card</button></a>
            <a href="admin/student?edit=<?php echo $student; ?>&done"><button class="submit">Edit</button></a>
            <?php }?>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle"><img src="media/uploads/<?php echo $picture; ?>" style="width: 70%; height: auto; border: 2px solid #999; max-width: 200px;"/></td>
        <td  align="left" valign="middle"></td>
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
        <td align="left" colspan="2"  class="tr-heading"  valign="middle"><strong>Contact Information</strong></td>
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
        <td align="left" valign="middle">Phone:</td>
        <td  align="left" valign="middle"><?php echo $row['phone']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email:</td>
        <td  align="left" valign="middle"><?php echo $row['email']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td colspan="2"  class="tr-heading"  align="left" valign="middle"><strong>Other Information</strong></td>
        </tr>
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
        <td align="left" valign="middle">Current Class:</td>
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
        <td colspan="2"  class="tr-heading"  align="left" valign="middle"><strong>Parents' Information</strong></td>
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
     <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2"  class="tr-heading"  valign="middle"><strong>Assigned Guardians</strong></td>
      </tr>
     <?php
	$sql=$query = "SELECT * FROM student_guardian WHERE student_id = '$student' ORDER BY id DESC LIMIT 10";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);

	$i=0;
	while($row3 = mysqli_fetch_assoc($result)){
		$id = $row3['guardian_id'];
	 ?>
      <tr>
        <td colspan="2" align="left" valign="middle"><?php echo guardianData($id); ?></td>
      </tr>
      <?php
	$i++;
	} ?>
      <tr>
        <td colspan="2" align="center" valign="middle"> <a href="admin/student?guardian=<?php echo $student;?>" ><button class="submit">Add Guardian</button></a></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td  colspan="2"  class="tr-heading"  align="left" valign="middle"><strong>Portal Login Details</strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Username:</td>
        <td  align="left" valign="middle"><?php echo portalUsername($student, 'Student'); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Password: </td>
        <td  align="left" valign="middle"><?php echo $row['pln_pa']; ?> </td>
      </tr>
      </table>
  </div>

</div>
</div>


 <?php
}
 if(isset($_REQUEST['card']) && !empty($_REQUEST['card'])) {
	$student = filterinp($_REQUEST['card']);
		//get students profile
		$sql=$query="SELECT * FROM students WHERE id = '$student'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}
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
                        <div class="title">Adm No.: </div><div class="value"><?php echo $row['admission_number']; ?></div>
                        <div class="title">Adm Year: </div><div class="value"><?php echo $row['year']; ?></div>
                    </div>
                </div>
                <div id="id-print">
                This generated ID Card has been optimized for use with any 3.5" X 2" Plastic Card Printer.<br /><br />
                <a href="" onClick="javascript:printDiv('idCard')">
                <button class="submit">Print ID Card</button></a></div>
             </div>
        </div>
 <?php }


if(isset($_REQUEST['edit'])){
if(userRole($userID) > 2 ) {
header('location: admin.php');
}

	$student = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM students WHERE id = '$student'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo studentName($student); ?>'s Profile
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/student" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <!-- Sender ID -->
      <tr>
        <td  colspan="2"  class="tr-heading"  align="left" valign="middle"><strong>Personal Information</strong></td>
        </tr>
      <tr>
        <td align="left" valign="middle">Change Photo:</td>
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
        <td align="left" valign="middle">Other Names:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="other_name" id="senderID" required="required" placeholder="" value="<?php echo @$row['other_name']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Gender:</td>
        <td  align="left" valign="middle">
        <select name="sex" id="sex" style="min-width:200px" >
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
        <td align="left" valign="middle">Bload Group: <small>(Optional)</small></td>
        <td  align="left" valign="middle">
        	<input type="text"  name="bload_group" id="bload_group" placeholder="Eg. O+, A, etc" value="<?php echo @$row['bload_group']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td  colspan="2"  class="tr-heading"  align="left" valign="middle"><strong>Contact Information</strong></td>
        </tr>
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
        	<select name="country" id="e1" required="required" style="width: 50%;"><?php echo getCountryList($row['country']); ?></select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Nationality:</td>
        <td  align="left" valign="middle">
        	<select name="nationality" id="e2" required="required" style="width: 50%;"><?php echo getCountryList($row['nationality']); ?></select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Phone:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="phone" id="phone" placeholder="Include country code" value="<?php echo @$row['phone']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email:</td>
        <td  align="left" valign="middle">
        	<input type="email"  name="email" id="email" placeholder="" value="<?php echo @$row['email']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left"  colspan="2"  class="tr-heading"  valign="middle"><strong>Other Information</strong></td>
        </tr>
      </tr>
      <tr>
        <td align="left" valign="middle">Admission Year:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="year" id="year" required="required" placeholder="" value="<?php echo @$row['year']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Change Parent:</td>
        <td  align="left" valign="middle">
        <select name="parent" id="e3" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM parents WHERE school_id = '$school_id' ORDER BY father_name ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $p_id = $row['id'];
                $title = $row['father_name'].' & '.$row['mother_name'];
            ?>
               <option <?php if(getParent($student) == $p_id) { echo 'selected';} ?> value="<?php echo $p_id; ?>"><?php echo $title; ?></option>
            <?php
									$i++;
								}   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Change Class:</td>
        <td  align="left" valign="middle">
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
               <option <?php if(getClass($student,$currentSession) == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  $i++;
								}   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Change Hostel:</td>
        <td  align="left" valign="middle">
        <select name="hostel_id" id="e5" style="width: 90%;" >
        <option value="0">None</option>
			<?php
                $sql=$query="SELECT * FROM hostels WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $h_id = $row['id'];
                $title = $row['title'];
            ?>
               <option <?php if(getHostel($student) == $h_id) { echo 'selected';} ?> value="<?php echo $h_id; ?>"><?php echo $title; ?></option>
            <?php  $i++;}   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Change School Bus:</td>
        <td  align="left" valign="middle">
        <select name="bus_id" id="e6" style="width: 90%;" >
        <option value="0">None</option>
		<?php
             $sql=$query="SELECT * FROM vehicles WHERE school_id = '$school_id' ORDER BY title ASC";
             $result = mysqli_query($server, $query);
             while($row = mysqli_fetch_assoc($result)){
               $h_id = $row['id'];
               $title = $row['title'];            ?>
               <option <?php if(getBus($student) == $h_id) { echo 'selected';} ?> value="<?php echo $h_id; ?>"><?php echo $title; ?></option>
            <?php	}   ?>
		</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Enable Portal Access:</td>
        <td  align="left" valign="middle">
        <select name="portal_access" id="portal"  style="min-width:200px">
               <option <?php if(@$row['portal_access'] == '1') { echo 'selected';} ?> value="1"><?php echo 'Yes'; ?></option>
               <option <?php if(@$row['portal_access'] == '0') { echo 'selected';} ?> value="0"><?php echo 'No'; ?></option>
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
	$sql=$query = "SELECT * FROM custom_fields WHERE school_id = '$school_id' AND form = 'student' ORDER BY id DESC";
	$result = mysqli_query($server, $sql);
	while($row3 = mysqli_fetch_assoc($result)){
	 ?>
      <tr>
        <td align="left" valign="middle"><?=$row3['label']?>:</td>
        <td  align="left" valign="middle">
		<input type="text"  name="customf[<?=$row3['id']?>]" value="<?=customFieldValue($row3['id'],@$student); ?>">
		</td>
      </tr>
      <?php	} ?>
      
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="student" value="<?php echo $student; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Imformation</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Changes...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}

if(isset($_REQUEST['guardian']))
{

if(userRole($userID) > 2) {
header('location: admin.php');
}

	$student = mysqli_real_escape_string($server, $_REQUEST['guardian']);

	?>
        <div id="add-new">
            <div id="add-new-head">Assign Guardian to <?php echo studentName($student); ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
             <div class="inside" style="background-color: #fff;">
     <form method="post" action="admin/student" enctype="multipart/form-data">
    <table width="50%" border="0" cellspacing="0" cellpadding="0">
       <tr>
        <td  align="left" valign="middle">Select Guardian:</td>
        <td  align="left" valign="middle">
        <select name="guardian_id" id="e5" style="" >
			<?php
                $sql=$query="SELECT * FROM guardians WHERE school_id = '$school_id' ORDER BY first_name ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['first_name'].' '.$row['last_name'];
            ?>
               <option value="<?php echo $g_id; ?>"><?php echo $title; ?></option>
            <?php  $i++;}   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;
        <input type="hidden" name="add_guardian" value="<?php echo $student; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Assign Guardian</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Changes...</div>
        </td>
      </tr>
    </table>
           </div>
       </div>
<?php
}

if(isset($_REQUEST['add_guardian'])){
	$student_id = mysqli_real_escape_string($server, $_POST['add_guardian']);
	$guardian_id = mysqli_real_escape_string($server, $_POST['guardian_id']);
	if(!guardianStudentExist($student_id,$guardian_id)) {
			$sql=$query="INSERT INTO student_guardian (`student_id`, `guardian_id`) VALUES ('$student_id', '$guardian_id');";
			mysqli_query($server, $query) or die(mysqli_error($server));
	}
	$message = 'The selected guardian was succesfully assigned to '.studentName($student_id);
	$class = 'green';
}

if(isset($_REQUEST['status']))
{
	$student = mysqli_real_escape_string($server, $_REQUEST['status']);
		//get students profile
		$sql=$query="SELECT * FROM students WHERE id = '$student'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$status = $row['status'];

	?>
        <div id="add-new">
            <div id="add-new-head"><?php echo studentName($student); ?>'s Admission Status
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
             <div class="inside" style="background-color: #fff;">
     <form method="post" action="admin/student" enctype="multipart/form-data">
    <table width="50%" border="0" cellspacing="0" cellpadding="0">
       <tr>
        <td  align="left" valign="middle">Active Student:</td>
        <td width="30%" align="left" valign="middle">
        	<input type="radio" name="status" value="1" <?php if($row['status'] == 1) { ?> checked="checked" <?php } ?>>
        </td>
      </tr>
       <tr>
        <td align="left" valign="middle">Suspended Student:</td>
        <td align="left" valign="middle">
        	<input type="radio" name="status" value="2" <?php if($row['status'] == 2) { ?> checked="checked" <?php } ?>>
        </td>
      </tr>
       <tr>
        <td align="left" valign="middle">Deceased Student:</td>
        <td align="left" valign="middle">
        	<input type="radio" name="status" value="3" <?php if($row['status'] == 3) { ?> checked="checked" <?php } ?>>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Deferred Student:</td>
        <td align="left" valign="middle">
        	<input type="radio" name="status" value="4" <?php if($row['status'] == 4) { ?> checked="checked" <?php } ?>>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Transferred Student:</td>
        <td align="left" valign="middle">
        	<input type="radio" name="status" value="7" <?php if($row['status'] == 7) { ?> checked="checked" <?php } ?>>
        </td>
      </tr>
       <tr>
        <td align="left" valign="middle">Graduated Student:</td>
        <td align="left" valign="middle">
        	<input type="radio" name="status" value="5" <?php if($row['status'] == 5) { ?> checked="checked" <?php } ?>>
        </td>
      </tr>
       <tr>
        <td align="left" valign="middle">Expelled Student:</td>
        <td  align="left" valign="middle">
        	<input type="radio" name="status" value="6" <?php if($row['status'] == 6) { ?> checked="checked" <?php } ?>>
        </td>
      </tr>
      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="student" value="<?php echo $student; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Status</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Changes...</div>
        </td>
      </tr>
    </table>
           </div>
       </div>
<?php
}

if(isset($_GET['import'])){

?>

<div id="add-new">
   <div id="add-new-head">Import Students Data from Excel
       <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
   	<div id="messageBox" style="max-width: 850px;" class="message blue">
    <strong>Important Notice</strong><br>
    Use this tool to import existing students from an Excel file. Please download the Sample Excel file to see the correct arrangement for your Excel sheet. Please note that all fields are required to get the best result.<br><br>
     <a class="btn btn-info" href="sample2.xls" class="btn btn-primary">Download Sample</a></div>
	</div>
    <form method="post" action="admin/student" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Select Class to Import</td>
        <td  align="left" valign="middle">
        	<select name="class_id" style="min-width: 200px;">
			<?php
			    $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

								$i=0;
								while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  $i++; }   ?>
               <option selected value="" disabled>Select Class</option>
        	</select>
        </td>
       </tr>

       <tr>
        <td align="left" valign="middle">Upload Excel File:</td>
        <td  align="left" valign="middle">
        	<input type="file" name="sdata" accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required="required" value="Choose File">
        </td>
       </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="import_csv" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Import Records</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Importing Data...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}

if(isset($_POST['import_csv'])) {
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	if($class_id < 1) {
		$message = 'You have not selected any class';
		$class = 'red';
	} else {
		$upload_path = 'media/uploads/';
		$file1 = $_FILES['sdata']['name'];
		$ext = end(explode(".", $_FILES['sdata']['name']));
		$allowed = array("xls","xlsx");
		if(!in_array(strtolower($ext), $allowed)) {
			$message = 'Please upload a valid Excel file';
			$class = 'red';
		} else {
			if(move_uploaded_file($_FILES['sdata']['tmp_name'],$upload_path.$file1)) {
				$success = importCustomers($file1,$class_id,$school_id);
				if($success!=='ok') {
					$message = $success;
					$class = 'red';
				} else {
					$message = 'The uploaded student records have been successfully imported. You may need to update each students photo if needed.';
					$class = 'green';
				}
			} else {
				$message = 'Sorry but your file could not be uploaded. Please try again or use a different file';
				$class = 'red';
			}
		}
	}
}

if(isset($_GET['delete'])) {
	$student = mysqli_real_escape_string($server, $_GET['delete']);
	mysqli_query($server, "DELETE FROM custom_values WHERE user_id = '$student'");
	mysqli_query($server, "DELETE FROM `student_class` WHERE `student_id` = '$student'");
	mysqli_query($server, "DELETE FROM `students` WHERE `id` = '$student'");
	mysqli_query($server, "DELETE FROM `student_parent` WHERE `student_id` = '$student'");
	mysqli_query($server, "DELETE FROM `student_attendance` WHERE `student_id` = '$student'");
	mysqli_query($server, "DELETE FROM `student_hostel` WHERE `student_id` = '$student'");
	mysqli_query($server, "DELETE FROM `student_bus` WHERE `student_id` = '$student'");
	mysqli_query($server, "DELETE FROM `users` WHERE `profile_id` = '$student'");
}

if(isset($_POST['student'])) {
$student = $_POST['student'];
	foreach ($_POST as $key => $value ){
		$$key = mysqli_real_escape_string($server, $value);
	}
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "student") && ($key != "save") && ($key != "admit") && ($key != "photo") && ($key != "class") && ($key != "parent" && ($key != 'customf') && ($key != 'bus_id'))) {
			$value = mysqli_real_escape_string($server, $value);
			$sql=$query="UPDATE `students` SET `$key` =  '$value' WHERE `id` = '$student';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}
	foreach($_POST['customf'] as $num => $cuid) {
		mysqli_query($server, "DELETE FROM custom_values WHERE field_id = '$num' AND user_id = '$student'");
		mysqli_query($server, "INSERT INTO custom_values (`field_id`, `user_id`,`value`) VALUES ('$num', '$student','$cuid');") or die(mysqli_error($server));
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
		$sql=$query="UPDATE `students` SET `photo` =  '$filename1' WHERE `id` = '$student';";
		mysqli_query($server, $query) or die(mysqli_error($server));
	}
	//update hostel
	if($hostel_id > 0 ) {
		mysqli_query($server, "DELETE FROM `student_hostel` WHERE `student_id` = '$student'");
		$sql=$query="INSERT INTO student_hostel (`id`, `student_id`, `hostel_id`) VALUES (NULL, '$student', '$hostel_id');";
		mysqli_query($server, $query) or die(mysqli_error($server));
	} else {
		mysqli_query($server, "DELETE FROM `student_hostel` WHERE `student_id` = '$student'");
	}
	//update Bus
	if($bus_id > 0 ) {
		mysqli_query($server, "DELETE FROM `student_bus` WHERE `student_id` = '$student'");		
		$sql=$query="INSERT INTO student_bus (`id`, `student_id`, `bus_id`) VALUES (NULL, '$student', '$bus_id');";
		mysqli_query($server, $query) or die(mysqli_error($server));
	} else {
		mysqli_query($server, "DELETE FROM `student_bus` WHERE `student_id` = '$student'");		
	}
		
	//update phone number
	$sql=$query="UPDATE `users` SET `email` =  '$email' WHERE `profile_id` = '$student';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$name = $last_name.' '.$first_name.' '.$other_name;
	$sql=$query="UPDATE `users` SET `name` =  '$name' WHERE `profile_id` = '$student';";
	mysqli_query($server, $query) or die(mysqli_error($server));

	//update parents
	if(isset($_POST['parent'])) {
		$parent = mysqli_real_escape_string($server,$_POST['parent']);
		$sql=$query="UPDATE `student_parent` SET `parent_id` =  '$parent' WHERE `student_id` = '$student';";
		mysqli_query($server, $query) or die(mysqli_error($server));
	}
	//update class
	if(isset($_POST['class'])) {
		$class = mysqli_real_escape_string($server,$_POST['class']);
		$sql=$query="DELETE FROM `student_class` WHERE `session_id` = '$currentSession' AND `student_id` = '$student';";
		mysqli_query($server, $query) or die(mysqli_error($server));
	
		$sql=$query ="INSERT INTO student_class (`student_id`, `class_id`, `session_id`)
		VALUES ('$student', '$class', '$currentSession');";
	
		mysqli_query($server, $query) or die(mysqli_error($server));
	}
	
	$message = 'The selected student\'s profile was succesfully updated.';
	$class = 'green';
}
if(isset($_GET['keyword'])){
$session_id = mysqli_real_escape_string($server,$_GET['session']);
if(empty($session_id)) {
	$session_id = $currentSession;
}
$class_id = mysqli_real_escape_string($server,$_GET['class']);
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
	    if (!empty($term))	    {
	         $clauses[] = "a.first_name LIKE '%$term%' OR a.last_name LIKE '%$term%' OR a.other_name LIKE '%$term%' OR a.sex LIKE '%$term%' OR a.state LIKE '%$term%'";
	    }	    else	    {
	         $clauses[] = "a.first_name LIKE '%%' OR a.address LIKE '%%' OR a.sex LIKE '%%' OR a.state LIKE '%%' OR a.last_name LIKE '%%'";
	    }
	}
	$extras='';
	if($session_id>0&&$class_id>0) {
		$extras=" AND ( c.class_id = '$class_id' AND  c.session_id = '$session_id') ";
	}
	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select a.* FROM students a JOIN student_class c ON a.id = c.student_id WHERE a.school_id = '$school_id' AND $filter $extras LIMIT $pageLimit,$setLimit";
	if(!empty($class_id)) {
	//$query = $sql .=  "AND (sc.class_id = '$class_id')";
	}

 	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "SELECT * FROM students WHERE school_id = '$school_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "No students records found!";
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
        <select name="session" id="e3" style="margin-right: 5px;" >
			<?php
			     $sqlC=$queryC="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY id DESC";
				$session_id = getSetting('current_session');
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

				while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option <?php if(@$session_id == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php   }   ?>
            	<option selected value="" disabled>Select Session</option>
			</select>
            &nbsp;
        <select name="class" id="e4" style="margin-right: 5px;" >
			<?php
			if(userRole($userID) != 4) {
				echo '<option value="">All Classes</option>';
			}
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
			if(userRole($userID) == 4) {
				$class_id = getTeacherClass(userProfile($userID));
				$sqlC=$queryC="SELECT * FROM classes WHERE id = '$class_id' ORDER BY title ASC";
			}
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

				while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option <?php if(@$class_id == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  }   ?>
               <option selected value="" disabled>Select Class</option>
			</select>
            &nbsp;
        <input type="search" name="keyword" placeholder="Search Word"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/admit"><button type="button" class="submit">Enroll</button></a>
        <a href="admin/student?import"><button type="button" class="submit success">Import Students</button></a>
        <a href="admin/student?export"><button type="button" class="submit">Export Students</button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="4.5%" scope="col">ID</th>
                <th width="20%" scope="col">Full Name</th>
                <th width="10%" scope="col">Email/Phone</th>
                <th width="10%" scope="col">Class</th>
                <th width="7%" scope="col">Gender</th>
                <th width="12.5%" scope="col">Status</th>
                <th width="30%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$class = className(getClass($row['id'],$currentSession));
					$class_id2 = getClass($row['id'],$currentSession);
					$user = getUserID($row['id'],'6');
					$name = $row['last_name'].' '.$row['first_name'].' '.$row['other_name'];
					$status = $row['status'];
					$emp = $row['email'].'/'.$row['phone'];
					$sex = $row['sex'];
					if($status==2) { $tag1='<orange>'; $tag2='</ogange>';}
					if($status==4) { $tag1='<gray>'; $tag2='</gray>';}
					if($status==7) { $tag1='<gray>'; $tag2='</gray>';}
					if($status==1) { $tag1='<green>'; $tag2='<green>';}
					if($status==4) { $tag1='<red>'; $tag2='</red>';}
					if($status==5) { $tag1='<red>'; $tag2='</red>';}
					if($status==6) { $tag1='<blue>'; $tag2='</blue>';}

				if(userRole($userID) == 4 && getTeacherClass(userProfile($userID)) != $class_id2) {
					//skip student
				} else {
				?>
              <tr class="inner">
                <td > <?php echo $id; ?></td>
                <td > <?php echo $name; ?></td>
                <td > <?php echo $emp; ?></td>
                <td > <?php echo $class; ?></td>
                <td > <?php echo $sex; ?></td>
                <td > <?php echo '<a href="admin/student?status='.$id.'">'.$tag1.statusName($status).$tag2.'</a>'; ?></td>
                <td valign="middle">
                <a href="admin/student?view=<?php echo $id;?>"><button class="btn-info">View</button></a>
                <a href="admin/reportcard?view=<?php echo $id;?>"><button class="btn-success">Report Card</button></a>
                <a href="admin/parent?view=<?php echo getParent($id);?>"><button>Parents</button></a>
				<?php if(userRole($userID)<3) { ?>
                <a href="admin/student?edit=<?php echo $id;?>"><button class="btn-warning">Edit</button></a>
                <a href="admin/student?status=<?php echo $id;?>"><button class="btn-info">Status</button></a>
                <a href="admin/sms?id=<?php echo $user;?>"><button>SMS</button></a>
                <a href="admin/email?id=<?php echo $user;?>"><button>Email</button></a>
                <a href="admin/student?delete=<?php echo $id;?>"><button class="btn-danger">Delete</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php }
					$i++;
				} ?>
              </table>

<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
</div>
