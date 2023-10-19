<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		Guardian.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			18/02/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());



if(isset($_REQUEST['delete'])){
if(userRole($userID) > 2) {
header('location: admin.php');
}
	$guardian = filterinp($_REQUEST['delete']);
	$sql=$query= "DELETE FROM guardians WHERE id = '$guardian'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));

	$message = "The selected guardian was successfully deleted.";
	$class="green";
}


if(isset($_REQUEST['view'])){
	$guardian = mysqli_real_escape_string($server, $_REQUEST['view']);
		//get students profile
		$sql=$query="SELECT * FROM guardians WHERE id = '$guardian'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}


?>
<div id="add-new">
   <div id="add-new-head"><?php echo guardianName($guardian); ?>'s Details
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
        	<div class="action-box">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print Credentials</button></a>
           <?php if(userRole($userID) < 3) {?>
            <a href="admin/guardian?edit=<?php echo $guardian; ?>&done"><button class="submit">Edit</button></a>
            <?php } ?>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" colspan="2" class="tr-heading"  valign="middle"><strong>Personal Details</strong></td>
	</tr>
      <tr>
        <td align="left" valign="middle"><img src="media/uploads/<?php echo $picture; ?>" style="width: 70%; height: auto; border: 2px solid #999; max-width: 200px;"/></td>
        <td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" valign="middle">First Name:</td>
        <td  align="left" valign="middle"><?php echo $row['first_name']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Last Name: </td>
        <td  align="left" valign="middle"><?php echo $row['last_name']; ?> </td>
      </tr>
     <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2" class="tr-heading"  valign="middle"><strong>Contact Information</strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Address:</td>
        <td  align="left" valign="middle"><?php echo $row['address'].', '.$row['city'].' '.$row['state'].' '.countryName($row['country']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">City:</td>
        <td  align="left" valign="middle"><?php echo $row['city']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">State:</td>
        <td  align="left" valign="middle"><?php echo $row['state']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Country:</td>
        <td  align="left" valign="middle"><?php echo $row['country']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Phone Numnber:</td>
        <td  align="left" valign="middle"><?php echo $row['phone']; ?> </td>
      </tr>

      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      </table>
  </div>

</div>
</div>


 <?php
}

if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])){
if(userRole($userID) > 2) {
header('location: admin.php');
}

	if(isset($_REQUEST['edit'])) {
	$guardian = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM guardians WHERE id = '$guardian'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo guardianName($guardian); ?>'s Details
<?php
	} else {
		$parent = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Guardian
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/guardian" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" class="tr-heading"  valign="middle"><strong>Personal Information</strong></td>
        </tr>
      <tr>
        <td align="left" valign="middle">Guardian's Photo:</td>
        <td  align="left" valign="middle">
        	<input type="file" accept="image/gif,image/png,image/jpg" style="width: 70%" name="photo" id="photo" >
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">First Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="first_name" id="first_name" required="required" placeholder="" value="<?php echo @$row['first_name']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Last Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="last_name" id="last_name" placeholder="" value="<?php echo @$row['last_name']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2" class="tr-heading" valign="middle"><strong>Contact Information</strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Address:</td>
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
        	<select name="country" id="e1" required="required" style="width: 90%;"><?php echo getCountryList($row['country']); ?></select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Phone Numnber:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="phone" id="phone" required="required" placeholder="Include country code eg. 2348031234567" value="<?php echo @$row['phone']; ?>">
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="guardian" value="<?php echo $guardian; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Imformation</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Create Guardian</button>
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
	} else {
		if($file1 !=="") {	move_uploaded_file($_FILES['photo']['tmp_name'],$upload_path . $filename1); }else {$filename1 = '';}

		//create new prents
		if(!guardianExist($first_name, $last_name, $school_id)) {
			$sql=$query="INSERT INTO guardians (`id`, `school_id`, `first_name`, `last_name`, `photo`, `address`, `city`, `state`, `country`,`phone` )
			VALUES (NULL, '$school_id', '$first_name', '$last_name', '$filename1', '$address', '$city', '$state', '$country', '$phone');";
			mysqli_query($server, $query) or die(mysqli_error($server));

		$message = 'The new guardian was succesfully created.';
		$class = 'green';
		} else {
		$message = 'Sorry but these guardian data has already been created. Please use defferent names';
		$class = 'yellow';
		}
	}
}

if(isset($_POST['save'])){
	$guardian = $_POST['guardian'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "guardian") && ($key != "save") && ($key != "photo") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
			$value = mysqli_real_escape_string($server, $value);
			$sql=$query="UPDATE `guardians` SET `$key` =  '$value' WHERE `id` = '$guardian';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}

	//upload father
	$upload_path = 'media/uploads/';
	$file1 = $_FILES['photo']['name'];
	$filename1 = date("d-m-Y").$_FILES['photo']['name'];
	$ext = end(explode(".", $_FILES['photo']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
	} else {
		//update father photo if set
		if($file1 !=="") {
			move_uploaded_file($_FILES['photo']['tmp_name'],$upload_path . $filename1);
			$sql=$query="UPDATE `guardians` SET `photo` =  '$filename1' WHERE `id` = '$guardian';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
		$message = 'The selected guardian\'s profile was succesfully updated.';
		$class = 'green';
	}
}

if(isset($_GET['keyword'])){
$class_id = mysqli_real_escape_string($server, $_GET['class']);
$subject_id = mysqli_real_escape_string($server, $_GET['subject']);

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
	    if (!empty($term))
	    {
	         $clauses[] = "p.first_name LIKE '%$term%' OR p.last_name LIKE '%$term%' OR p.address LIKE '%$term%' OR p.city LIKE '%$term%' OR p.state LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "p.first_name LIKE '%%' OR p.address LIKE '%%' OR p.city LIKE '%%' OR p.state LIKE '%%' OR p.last_name LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query= "select * FROM guardians p JOIN student_guardian sp ON p.id = sp.guardian_id JOIN students s ON s.id = sp.student_id WHERE p.school_id = '$school_id' AND $filter LIMIT $pageLimit,$setLimit";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query= "SELECT * FROM guardians WHERE school_id = '$school_id' ORDER BY first_name DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "No records found!";
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
        <input type="search" name="keyword" placeholder="Search Guardian" />
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/guardian?new"><button type="button" class="submit">Add <hide>New</hide></button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="7.5%" scope="col">ID</th>
                <th width="15%" scope="col">First Name</th>
                <th width="15%" scope="col">Last Name</th>
                <th width="13%" scope="col">Phone</th>
                <th width="25%" scope="col">Assigned Student</th>
                <th width="20%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$first_name = $row['first_name'];
					$last_name = $row['last_name'];
					$phone = $row['phone'];
					$student = studentName(guardianStudent($row['id']));
				if(guardianStudent($row['id']) < 1) { $student = 'Not assigned';}
				?>
              <tr class="inner">
                <td width="7.5%"> <?php echo $id; ?></td>
                <td width="15%"> <?php echo $first_name; ?></td>
                <td width="15%"> <?php echo $last_name; ?></td>
                <td width="13%"> <?php echo $phone; ?></td>
                <td width="25%"> <?php echo $student; ?></td>
                <td width="20%" valign="middle">
                <a href="admin/guardian?view=<?php echo $id;?>"><button class="success">Profile</button></a>
				<?php if(userRole($userID)<3) { ?>
                <a href="admin/guardian?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/guardian?delete=<?php echo $id;?>"><button class="danger">Remove</button></a>
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
