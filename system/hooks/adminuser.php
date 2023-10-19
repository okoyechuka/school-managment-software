<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		adminuser.php
Description:	This is main customer setting page
Developer: 		Ynet Interactive
Date: 			08/3/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) > 2) {
header('location: admin.php');
}

if(isset($_POST['save'])) {
	$name = mysqli_real_escape_string($server,$_POST['name']);
	$email = mysqli_real_escape_string($server,$_POST['email']);
	$username = mysqli_real_escape_string($server,$_POST['username']);
	$phone = mysqli_real_escape_string($server,$_POST['phone']);
	$password = mysqli_real_escape_string($server,$_POST['password']);
	$role_id = mysqli_real_escape_string($server,$_POST['role_id']);

	$salt = genRandomPassword(32);
	$crypt = getCryptedPassword($password, $salt);
	$password2 = $crypt.':'.$salt;

	//create user
	$sql=$query="INSERT INTO users (`id`, `username`, `password`, `name`, `email`, `role_id`, `school_id`, `phone`)
		VALUES (NULL, '$username', '$password2', '$name', '$email', '$role_id', '$school_id', '$phone');";

	$add = mysqli_query($server, $query) or die(mysqli_error($server));

	$message = "The new user account was successfully created.";
	$class="green";

}

if(isset($_POST['update'])) {
	$customer = mysqli_real_escape_string($server,$_POST['user']);
	$password = mysqli_real_escape_string($server,$_POST['password']);
	$name = mysqli_real_escape_string($server,$_POST['name']);
	$email = mysqli_real_escape_string($server,$_POST['email']);
	$username = mysqli_real_escape_string($server,$_POST['username']);
	$phone = mysqli_real_escape_string($server,$_POST['phone']);
	$role_id = mysqli_real_escape_string($server,$_POST['role_id']);
	$profile_id = mysqli_real_escape_string($server,$_POST['staff']);

	$sql=$query="UPDATE `users` SET
		`name` = '$name',
		`email` = '$email',
		`phone` = '$phone',
		`role_id` = '$role_id',
		`email` = '$email'
	 WHERE `id` = '$customer' AND school_id = '$school_id'";
	mysqli_query($server, $query) or die(mysqli_error($server));

	//update staff profile too
	if($role_id == 4) {
		$sql=$query="UPDATE `teachers` SET
			`phone` = '$phone',
			`email` = '$email'
		 WHERE `id` = '$profile_id' AND school_id = '$school_id'";
		mysqli_query($server, $query) or die(mysqli_error($server));
		if(!empty($password)) {
			$sql=$query="UPDATE `teachers` SET `pln_pa` = '$password'  WHERE `id` = '$profile_id' AND school_id = '$school_id'";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	} elseif($role_id == 5) { //update plain pass
		if(!empty($password)) {
			$sql=$query="UPDATE `parents` SET `pln_pa` = '$password'  WHERE `id` = '$profile_id' AND school_id = '$school_id'";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	} elseif($role_id == 6) { //update plaun pass
		if(!empty($password)) {
			$sql=$query="UPDATE `students` SET `pln_pa` = '$password'  WHERE `id` = '$profile_id' AND school_id = '$school_id'";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	} else {
		$sql=$query="UPDATE `staffs` SET
			`phone` = '$phone',
			`email` = '$email'
		 WHERE `id` = '$profile_id' AND school_id = '$school_id'";
		 mysqli_query($server, $query) or die(mysqli_error($server));
	}

	$message = "User account details was successfully updated.";
	$class="green";

	if(!empty($password)) {
		$salt = genRandomPassword(32);
		$crypt = getCryptedPassword($password, $salt);
		$password2 = $crypt.':'.$salt;
		$sql=$query="UPDATE `users` SET
			`password` = '$password2'
		WHERE `id` = '$customer' AND school_id = '$school_id'";

		mysqli_query($server, $query) or die(mysqli_error($server));

		$message = "User account details was successfully updated. Don't forget to notify the user about the password change";
		$class="green";
	}
}

if(isset($_REQUEST['delete'])) {
	$id = filterinp($_REQUEST['ID']);
	$sql=$query= "DELETE FROM users WHERE id = '$id' AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$message = "The selected account was successfully deleted.";
	$class="green";
}

if(isset($_REQUEST['edit'])) {
	if(isset($_REQUEST['ID'])) {
		$id = filterinp($_REQUEST['ID']);
		$sql=$query= "SELECT * FROM users WHERE id = '$id' AND school_id = '$school_id'";
		$result = mysqli_query($server, $query);
		$num = mysqli_num_rows($result);
		$row = mysqli_fetch_assoc($result);
		$name = $row['name'];
		$phone = $row['phone'];
		$username = $row['username'];
		$email = $row['email'];
		$staff_id = $row['profile_id'];
		$hed = 'Edit '.$name.' User Details';
	} else {
		$name = '';
		$phone = '';
		$email = '';
		$username = '';
		$staff_id = '';
		$hed = 'Create New Admin User';
	}
	//display form
?>
<div id="add-new">
	<div id="add-new-head"><?php echo $hed; ?>
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
     <div class="inside">
		<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
    <form method="post" action="" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">User Role:</td>
        <td  align="left" valign="middle">
        	<select name="role_id" style="width:90%" >
	<?php if($row['role_id'] == 4) { ?>
    		<option <?php if(@$row['role_id'] == '4') { echo 'selected';} ?> value="4"><?php echo 'Teacher'; ?></option>
    <?php } elseif($row['role_id'] == 5) { ?>
    		<option <?php if(@$row['role_id'] == '5') { echo 'selected';} ?> value="5"><?php echo 'Parents'; ?></option>
    <?php } elseif($row['role_id'] == 6) { ?>
    		<option <?php if(@$row['role_id']== 6) { echo 'selected';} ?> value="6"><?php echo 'Student'; ?></option>
    <?php } else { ?>
            <?php if(userRole($userID) == 1) { ?>
               <option <?php if(@$row['role_id'] == '1') { echo 'selected';} ?> value="1"><?php echo 'Supper User/Administrator'; ?></option>
               <?php } ?>
               <option <?php if(@$row['role_id'] == '2') { echo 'selected';} ?> value="2"><?php echo 'Manager'; ?></option>
               <option <?php if(@$row['role_id'] == '3') { echo 'selected';} ?> value="3"><?php echo 'Accountant'; ?></option>
               <option <?php if(@$row['role_id'] == '7') { echo 'selected';} ?> value="7"><?php echo 'Front Desk'; ?></option>
               <option <?php if(@$row['role_id'] == '8') { echo 'selected';} ?> value="8"><?php echo 'Librarian'; ?></option>
               <option <?php if(@$row['role_id'] == '10') { echo 'selected';} ?> value="10"><?php echo 'Store Manager';?></option>
               <option <?php if(@$row['role_id'] == '9') { echo 'selected';} ?> value="9"><?php echo 'Other Staffs'; ?></option>
	<?php } ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Full Name:</td>
        <td  align="left" valign="middle">
        	<input type="text" name="name" id="firstName" <?php if(isset($_REQUEST['ID'])) {echo 'readonly';} ?>  value="<?php echo $name; ?>" maxlength="200" required="required" placeholder="">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Mobile Phone:</td>
        <td  align="left" valign="middle">
        	<input type="text" name="phone" id="lastName" value="<?php echo $phone; ?>" maxlength="200" placeholder="">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Username:</td>
        <td  align="left" valign="middle">
        	<input type="text" <?php if(isset($_REQUEST['ID'])) {echo 'readonly';} ?> name="username" id="username" value="<?php echo $username; ?>" required="required" placeholder="">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email:</td>
        <td align="left" valign="middle">
        	<input type="text" name="email" id="email" value="<?php echo $email; ?>" maxlength="200" required="required" placeholder="">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><?php if(isset($_REQUEST['ID'])) echo 'Change'; ?> Password:</td>
        <td  align="left" valign="middle">
        	<input type="test" <?php if(!isset($_REQUEST['ID'])) echo 'required'; ?> name="password" id="password" maxlength="200" <?php if(isset($_REQUEST['ID'])) echo 'placeholder="This will change the user\'s current password"'; ?>>
        </td>
      </tr>
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td  align="left" valign="top">
        <input type="hidden" <?php if(isset($_REQUEST['ID'])){ echo 'name="update"'; } else {echo 'name="save"';} ?> />
        <input type="hidden" name="user" value="<?php echo $id; ?>" />
        <input type="hidden" name="staff" value="<?php echo $staff_id; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="submit" value="1" type="submit">Save User</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Processing...</div>
        </td>
      </tr>
   </table>
   </div>
 </div>
<?php }
if(isset($_GET['keyword']))
{
$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', $term);
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look){
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))  {
	         $clauses[] = "username LIKE '%$term%' OR email LIKE '%$term%' OR name LIKE '%$term%' OR phone LIKE '%$term%' ";
	    } else {
	         $clauses[] = "name LIKE '%%' OR username LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query= "select * FROM users WHERE school_id = '$school_id' AND role_id > '1' AND $filter LIMIT $pageLimit,$setLimit";

	if(userRole($userID) == 1 ) {
	 $sql=$query= "select * FROM users WHERE school_id = '$school_id' AND $filter LIMIT $pageLimit,$setLimit";
	}

	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1"){
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	 $sql=$query= "SELECT * FROM users WHERE role_id > 1 AND school_id = '$school_id' ORDER BY name DESC LIMIT $pageLimit,$setLimit";
	 if(userRole($userID) == 1) {
	 		$sql=$query= "SELECT * FROM users WHERE school_id = '$school_id' ORDER BY name DESC LIMIT $pageLimit,$setLimit";
	}
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1"){
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
        Search Users: <input type="search" name="keyword" placeholder="Search users"/>
        <button class="submit"><i class="fa fa-search"></i></button> <a href="admin/adminuser?edit"><button type="button" class="submit">Add User</button></a>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">Type</th>
                <th width="20%" scope="col">Name</th>
                <th width="15%" scope="col">Username</th>
                <th width="28%" scope="col">Email</th>
                <th width="12%" scope="col">Last Login</th>
                <th width="14%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
						$lastLogin = date('d/m/Y H:i', strtotime($row['last_login']));
						if($row['last_login'] == '0000-00-00 00:00:00')
						$lastLogin = 'Never';
						$id = $row['id'];
						$username = $row['username'];
						$email = $row['email'];
						$name = $row['name'];
						$role = roleName($row['role_id']);
						?>
	              <tr class="inner">
	                <td width="10%"> <?php echo $role; ?></td>
	                <td width="20%"> <?php echo $name; ?></td>
	                <td width="15%"> <?php echo $username; ?></td>
	                <td width="28%"> <?php echo $email; ?></td>
	                <td width="12%"> <?php echo $lastLogin; ?></td>
	                <td width="14%" valign="middle">
	                <a href="admin/adminuser?edit&ID=<?php echo $id;?>"><button>Edit</button></a>
	                <a href="admin/adminuser?delete&ID=<?php echo $id;?>"><button class="btn-danger">Delete</button></a>
	                </td>
	              </tr>
	          <?php
						$i++;
				} ?>
	            </table>
<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
<?php     ?>
</div>
