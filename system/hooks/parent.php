<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		parent.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			25/07/2017
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());



if(isset($_REQUEST['delete'])){
if(userRole($userID) > 2) {
	header('location: admin.php');
}

	$parent = filterinp($_REQUEST['delete']);
	//@ceejay, $user can't be placed here
	$user = getUserID($parent,'5');

	$sql=$query = "DELETE FROM users WHERE id = '$user'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));

	$sql=$query = "DELETE FROM parents WHERE id = '$parent'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));

	$message = "The selected parent was successfully deleted.";
	$class="green";
}


if(isset($_REQUEST['view'])){
		$parent = mysqli_real_escape_string($server, $_REQUEST['view']);
		//get students profile
		$sql=$query="SELECT * FROM parents WHERE id = '$parent'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$F_picture = $row['father_photo'];
		if(empty($F_picture)) {
			$F_picture = 'no-body.png';
		}
		$M_picture = $row['mother_photo'];
		if(empty($M_picture)) {
			$M_picture = 'no-body.png';
		}

?>
<div id="add-new">
   <div id="add-new-head"><?php echo parentName($parent); ?>'s Details
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
       <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print Credentials</button></a>
       <?php if(userRole($userID) < 3) {?>
            <a href="admin/parent?edit=<?php echo $parent; ?>&done"><button class="submit">Edit</button></a>
       <?php } ?>
      <div class="breaker"><hr></div>
     <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left"  colspan="2"  class="tr-heading"  valign="middle"><strong>Father's Details</strong></td>
	</tr>
      <tr>
        <td align="left"  colspan="2" style="background-color:white;" valign="middle"><img src="media/uploads/<?php echo $F_picture; ?>" style="width: 70%; height: auto; border: 2px solid #999; max-width: 200px;"/></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Father's Full Name:</td>
        <td  align="left" valign="middle"><?php echo $row['father_name']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Father's Occupation: </td>
        <td  align="left" valign="middle"><?php echo $row['father_occupation']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left"  colspan="2"  class="tr-heading"  valign="middle"><strong>Mother's Details</strong></td>
	</tr>
      <tr>
        <td align="left"  colspan="2" style="background-color:white;" valign="middle"><img src="media/uploads/<?php echo $M_picture; ?>" style="width: 70%; height: auto; border: 2px solid #999; max-width: 200px;"/></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Mother's Full Name: </td>
        <td  align="left" valign="middle"><?php echo $row['mother_name']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Mother's Occupation: </td>
        <td  align="left" valign="middle"><?php echo $row['mother_occupation']; ?> </td>
      </tr>
     <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" style="background-color:white;" colspan="2"  class="tr-heading"  valign="middle"><strong>Other Information</strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Residence Address:</td>
        <td  align="left" valign="middle"><?php echo $row['address'].', '.$row['city'].' '.$row['state'].' '.countryName($row['country']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email Address:</td>
        <td  align="left" valign="middle"><?php echo $row['email']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Contact Phone Numnber:</td>
        <td  align="left" valign="middle"><?php echo $row['phone']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Alternative Phone Numnber:</td>
        <td  align="left" valign="middle"><?php echo $row['phone2']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Authorization Code:</td>
        <td  align="left" valign="middle"><?php echo $row['authorization_code']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left"  colspan="2"  class="tr-heading"  valign="middle"><strong>Portal Login Details</strong></td>
      <tr>
        <td align="left" valign="middle">Username:</td>
        <td  align="left" valign="middle"><?php echo portalUsername($parent, 'Parent'); ?></td>
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

if(isset($_REQUEST['edit']) || isset($_REQUEST['new']))
{

if(userRole($userID) > 2) {
header('location: index.php');
}

	if(isset($_REQUEST['edit'])) {
	$parent = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM parents WHERE id = '$parent'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo parentName($parent); ?>'s Details
<?php
	} else {
		$parent = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Parent
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/parent" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left"  colspan="2"  class="tr-heading"  valign="middle"><strong>Father's Information</strong></td>
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
        	<input type="text"  name="father_name" id="father_name" required="required" placeholder="" value="<?php echo @$row['father_name']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Father's Occupation: <small>(Optional)</small></td>
        <td  align="left" valign="middle">
        	<input type="text"  name="father_occupation" id="father_occupation" placeholder="" value="<?php echo @$row['father_occupation']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <!-- Mother Info -->
      <tr>
        <td align="left"  colspan="2"  class="tr-heading" valign="middle"><strong>Mother's Information</strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Mather's Photo:</td>
        <td  align="left" valign="middle">
        	<input type="file" accept="image/gif,image/png,image/jpg" style="width: 70%" name="mother_photo" id="mother_photo" >
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Mather's Full Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="mother_name" id="mother_name" required="required" placeholder="" value="<?php echo @$row['mother_name']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Mother's Occupation: <small>(Optional)</small></td>
        <td  align="left" valign="middle">
        	<input type="text"  name="mother_occupation" id="mother_occupation" placeholder="" value="<?php echo @$row['mother_occupation']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left"  colspan="2"  class="tr-heading" valign="middle"><strong>Contact Information</strong></td>
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
        <td align="left" valign="middle">State of Residence:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="state" id="state" required="required" placeholder="" value="<?php echo @$row['state']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Country of Residence:</td>
        <td  align="left" valign="middle">
        	<select name="country" id="e1" required="required" style="width: 90%;"><?php echo getCountryList($row['country']); ?></select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email Address:</td>
        <td  align="left" valign="middle">
        	<input type="email"  name="email" id="email" placeholder="" value="<?php echo @$row['email']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Contact Phone Numnber:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="phone" id="phone" required="required" placeholder="Include country code eg. 2348031234567" value="<?php echo @$row['phone']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Alternative Phone Numnber:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="phone2" id="phone2" placeholder="Include country code eg. 2348031234567" value="<?php echo @$row['phone2']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left"  colspan="2"  class="tr-heading"  valign="middle"><strong>Other Information</strong></td>
        </td>
      </tr>
      <tr>

        <td align="left" valign="middle">Authorization Code:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="authorization_code" id="authorization_code" required="required" placeholder="This cde will be requested to confirm parent's representatives" value="<?php echo @$row['authorization_code']; ?>">
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="parent" value="<?php echo $parent; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Imformation</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Create Parent</button>
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
		$sql=$query = "INSERT INTO parents (`id`, `school_id`, `father_name`, `mother_name`, `father_occupation`, `mother_occupation`,
			`father_photo`, `mother_photo`, `address`, `city`, `state`, `country`, `email`, `phone`, `phone2`, `authorization_code`)
			VALUES (NULL, '$school_id', '$father_name', '$mother_name', '$father_occupation', '$mother_occupation', '$filename2',
				'$filename1', '$address', '$city', '$state', '$country', '$email', '$phone', '$phone2', '$authorization_code');";
		mysqli_query($server, $query) or die(mysqli_error($server));
		//get inserted id
		$parent = getInsertedID('parents');
		//create login account
		$name = 'Mr & Mrs '.$father_name;
		$username = rand(19999999, 99999999);
		$password = rand(19999999, 99999999);
		$salt = genRandomPassword(32);
		$crypt = getCryptedPassword($password, $salt);
		$password2 = $crypt.':'.$salt;

		if(usernameExist($username)) {
			$username = rand(19999999, 99999999)+rand(100, 999);
		}

		$sql=$query ="INSERT INTO users (`id`, `username`, `password`, `name`, `email`, `role_id`, `profile_id`, `school_id`, `phone`)
						VALUES (NULL, '$username', '$password2', '$name', '$email', '5', '$parent', '$school_id', '$phone');";
		mysqli_query($server, $query) or die(mysqli_error($server));

		//update pln_pa
		$sql=$query="UPDATE parents SET `pln_pa` = '$password' WHERE id = '$parent'";
		mysqli_query($server, $query) or die(mysqli_error($server));

		$message = 'The new parent was succesfully created.';
		$class = 'green';
	} else {
		$message = 'Sorry but these parents data has already been created. Please use defferent names';
		$class = 'yellow';
	}

}

if(isset($_POST['save'])){
$parent = $_POST['parent'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "parent") && ($key != "save") && ($key != "father_photo") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
			$value = mysqli_real_escape_string($server, $value);
			$sql=$query="UPDATE `parents` SET `$key` =  '$value' WHERE `id` = '$parent';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}

	//upload father
	$upload_path = 'media/uploads/';
	$file1 = $_FILES['father_photo']['name'];
	$filename1 = date("d-m-Y").$_FILES['father_photo']['name'];
	$ext = end(explode(".", $_FILES['father_photo']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
		$file1 = "";
	}
	
	//update father photo if set
	if($file1 !=="") {
		move_uploaded_file($_FILES['father_photo']['tmp_name'],$upload_path . $filename1);
		$sql=$query="UPDATE `parents` SET `father_photo` =  '$filename1' WHERE `id` = '$parent';";
		mysqli_query($server, $query) or die(mysqli_error($server));
	}

	//upload mother
	$upload_path = 'media/uploads/';
	$file2 = $_FILES['mother_photo']['name'];
	$filename2 = date("d-m-Y").$_FILES['mother_photo']['name'];
	$ext = end(explode(".", $_FILES['mother_photo']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
		$file2 = "";
	}
	//update mother photo if set
	if($file2 !=="") {
		move_uploaded_file($_FILES['mother_photo']['tmp_name'],$upload_path . $filename2);
		$sql=$query="UPDATE `parents` SET `mother_photo` =  '$filename2' WHERE `id` = '$parent';";
		mysqli_query($server, $query) or die(mysqli_error($server));
	}

	$message = 'The selected parents\' profile was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword'])){
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
	         $clauses[] = "father_name LIKE '%$term%' OR mother_name LIKE '%$term%' OR address LIKE '%$term%' OR city LIKE '%$term%' OR state LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "father_name LIKE '%%' OR address LIKE '%%' OR city LIKE '%%' OR state LIKE '%%' OR mother_name LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM parents  WHERE school_id = '$school_id' AND $filter LIMIT $pageLimit,$setLimit";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM parents WHERE school_id = '$school_id' ORDER BY father_name DESC LIMIT $pageLimit,$setLimit";
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
        <input type="search" name="keyword" placeholder="Search Parents" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/parent?new"><button type="button" class="submit">Add <hide>New</hide></button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="4.5%" scope="col">ID</th>
                <th width="15%" scope="col">Father</th>
                <th width="15%" scope="col">Mother</th>
                <th width="13%" scope="col">Phone</th>
                <th width="15%" scope="col">Location</th>
                <th width="33%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row=mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$father = $row['father_name'];
					$mother = $row['mother_name'];
					$phone = $row['phone'];
					$state = $row['city'].'-'.$row['state'];
					$user = getUserID($row['id'],'5');

				?>
              <tr class="inner">
                <td width="4.5%"> <?php echo sprintf('%01d',$i+1); ?></td>
                <td width="15%"> <?php echo $father; ?></td>
                <td width="15%"> <?php echo $mother; ?></td>
                <td width="13%"> <?php echo $phone; ?></td>
                <td width="15%"> <?php echo $state; ?></td>
                <td width="33%" valign="middle">
                <a href="admin/parent?view=<?php echo $id;?>"><button>Profile</button></a>
				<?php if(userRole($userID)<3) { ?>
                <a href="admin/parent?edit=<?php echo $id;?>"><button class="btn-warning">Edit</button></a>
                <a href="admin/adminuser?edit&ID=<?php echo $user;?>"><button class="btn-success">Edit Login</button></a>
                <a href="admin/parent?delete=<?php echo $id;?>"><button class="btn-danger">Remove</button></a>
                <a href="admin/sms?id=<?php echo $user;?>"><button>SMS</button></a>
                <a href="admin/email?id=<?php echo $user;?>"><button>Email</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php
						$i++;
					} ?>
              </table>

<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
</div>
