<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		school.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			12/05/2020
*/
global $server;
function etStudent($school) {
	global $server;
	$result = mysqli_query($server, "SELECT COUNT(id) AS value FROM students WHERE school_id = '$school'") or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['value'];
	return $value;
}
$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(!isOwner($userID)) {
header('location: admin.php');
}
$currentSession = getSetting('current_session');
$currentTerm = getSetting('current_term');

if( (HASENTERPRISE > 1) && !isset($_REQUEST['enterprise'])) {
//Show school managmenmt view
if(isset($_REQUEST['delete'])){
	$book = filterinp($_REQUEST['delete']);
	$sql=$query = "DELETE FROM schools WHERE id = '$book' ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query = "DELETE FROM users WHERE school_id = '$book' AND is_supper = 0";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query = "DELETE FROM teachers WHERE school_id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query = "DELETE FROM staffs WHERE school_id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query = "DELETE FROM students WHERE school_id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query = "DELETE FROM parents WHERE school_id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	if($book==1) {
		$result = mysqli_query($server, "DELETE FROM settings WHERE school_id = '$book' AND id > 8") or die(mysqli_error($server));
		$result = mysqli_query($server, "UPDATE settings SET `name` = '' WHERE school_id = '$book' ") or die(mysqli_error($server));
	}else {
		$sql=$query = "DELETE FROM settings WHERE school_id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));		
	}
	error_log("You deleted school record with ID: ".$book." from SOA");
	$message = "The selected school with all its data has been successfully deleted.";
	$class="green";
}

if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])){
	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
	$sql=$query="SELECT * FROM schools WHERE id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$placeholder = 'Leave empty except if you wish to change the admin password';
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['name']; ?>
<?php
} else {
	$book = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New School
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/schools" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">School ID:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="category" id="id" readonly="readonly" placeholder="Auto Assigned" value="<?php echo @$row['id']; ?>">
        </td>
       </tr>
      <tr>
        <td align="left" valign="middle">School Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="name" id="name" required="required" placeholder="" value="<?php echo @$row['name']; ?>">
        </td>
      </tr>
      <?php if(empty($row['username'])) { ?>
      <tr>
        <td align="left" valign="middle">Assign Username:<br><small>This form part of the school link and also serve as the School's Suppoer Admin username</small></td>
        <td  align="left" valign="middle">
        	<input type="text"  name="username" id="username" required="required" placeholder="Unique Userame" value="<?php echo @$row['username']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">School Admin's Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="adminname" id="adminname" required="required" placeholder="<?=@$row['adminname']?>" >
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">School Admin's Email:</td>
        <td  align="left" valign="middle">
        	<input type="email"  name="email" id="email" required="required" placeholder="<?=@$row['email']?>" >
        </td>
      </tr> 
      <tr>
        <td align="left" valign="middle">School Admin's Password:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="password" id="password" required="required" placeholder="" >
        </td>
      </tr>
      <?php } else { ?>
      	<tr>
        <td align="left" valign="middle">Update Admin's Password:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="password" id="password" placeholder="<?=@$placeholder?>" >
        </td>
      </tr>
      <input type="hidden" name="username2" value="<?php echo @$row['username']; ?>" />
      <?php } ?>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="hostel" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update School</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add School</button>
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
	if(SCHOOLLIMIT < 1) {
		//==========================================================================
		//	Dont even bother removing this line because it will only make matters worse
		//	Just purchase an Enterprise license
		//==========================================================================
		$message = 'Sorry but you have reached the maximum number of schools you can manage with your license. Please buy an Enterprise license to manage morte schools';
		$class = 'red';
	} else {
		$error = 0;
		$username = strtolower($username);
		if(schoolUsernameTakens($username)>0) {
			$error = 1;
			$message = "The username you typed is already assigned to another school";
			$class='red';
		}
		if(adinTakenUsername($username)>0) {
			$error = 1;
			$message = "The username you typed is already being used by another user";
			$class='red';
		}
		if(strtolower($username) == "admin"){
			$error = 1;
			$message = "You can not create a school with username 'admin'";
			$class='red';
		}
		if($error < 1) {	
			//create new school
			$sql=$query = "INSERT INTO schools (`username`, `name`, `email`, `currency_id`, `defaultTimeZone`,`theme`,`nextscheduled`)	VALUES ('$username', '$name', '$email','1','Africa/Lagos','DarkRed.css','".time()."');";
			mysqli_query($server, $query) or die(mysqli_error($server));
			$new_school_id = getInsertedID('schools');
			saveSettings('name',$name,$new_school_id);
			saveSettings('currency_id',1,$new_school_id);
			saveSettings('defaultTimeZone','Africa/Lagos',$new_school_id);
			//create admin user
			$salt = genRandomPassword(32);
			$crypt = getCryptedPassword($password, $salt);
			$password2 = $crypt.':'.$salt;

			$last_login_date = date('Y-m-d H:i:s');
			$name = $first_name.' '.$last_name;
			$today = date('Y-m-d');
			$sql=$query = "INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `role_id`, `last_login`, `profile_id`, `school_id`) VALUES (NULL, '$username', '$password2', '$adminname', '$email', '1', '$last_login_date', '0', '$new_school_id');";
			mysqli_query($server, $query);
			//create samples
			$sql=$query = "INSERT INTO `subject` (`id`, `school_id`, `title`, `class_id`, `assessment_max`, `exam_max`) VALUES
	(NULL, 1, 'Mathematics', '$new_school_id', '30.00', '70.00'), (2, 1, 'English', 1, '30.00', '70.00');";
			mysqli_query($server, $query);
			$sql=$query = "INSERT INTO `paymentgateways` (`id`, `name`, `alias`, `status`, `custom`, `text`, `image`, `url`, `school_id`) VALUES (NULL, 'Cash Payment', 'cash', 1, 1, 'Pay the total fee amount in cash to the school bossier ', 'custom.png', '', '$new_school_id')";
			mysqli_query($server, $query);
			$sql=$query = "INSERT INTO `grades` (`id`, `school_id`, `title`, `code`, `start_mark`, `end_mark`) VALUES
			(NULL, '$new_school_id', 'Distinction', 'A', '70.00', '100.00'),
			(NULL, '$new_school_id', 'Good', 'B', '60.00', '69.99'),
			(NULL, '$new_school_id', 'Credit', 'C', '45.00', '59.99');";
			mysqli_query($server, $query);

			//create currency
			$query = mysqli_query($server, "INSERT INTO `currency` (`title`, `rate`, `symbul`, `school_id`, `code`) VALUES	('Naira', '1.000000', '&#8358;', '$new_school_id', 'NGN');");
			$currency_id = mysqli_insert_id($server);
			$query = mysqli_query($server, "INSERT INTO `currency` (`title`, `rate`, `symbul`, `school_id`, `code`) VALUES	('US Dollars', '0.005100', '$', '$new_school_id', 'USD');");
			saveSettings('currency_id',$currency_id,$new_school_id);	
			mysqli_query($server, "UPDATE  `schools` SET `currency_id` =  '$currency_id' WHERE `id` = '$new_school_id';");

			$message = 'Congratulations!<br>Your new school has been created. The new school Students portal can be accessed via '.home_base_url().'schools/'.$username." while the admin/staff portal can be accessed at ".home_base_url()."admin.php";
			$class = 'green';
		}
	}

}



if(isset($_POST['save'])){
	$hostel = $_POST['hostel'];
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$sql=$query="UPDATE `schools` SET `name` =  '$name' WHERE `id` = '$hostel';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$message = 'The selected school was succesfully updated.';
	$class = 'green';
	
	if(isset($_POST['username'])&&!empty($_POST['username'])){
		$error = 0;
		$username = strtolower($username);
		if(schoolUsernameTakens($username)>0) {
			$error = 1;
			$message = "The username you typed is already assigned to another school";
			$class='red';
		}
		if(adinTakenUsername($username)>0) {
			$error = 1;
			$message = "The username you typed is already being used by another user";
			$class='red';
		}
		if($error <1) {
			$last_login_date = date('Y-m-d H:i:s');
			$query="UPDATE `schools` SET `username` =  '$username' WHERE `id` = '$hostel';";
			$username2 = $username;
			$query = "INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `role_id`, `last_login`, `profile_id`, `school_id`) VALUES (NULL, '$username', '', '$adminname', '$email', '1', '$last_login_date', '0', '$hostel');";
			mysqli_query($server, $query);
		}
	}
	if(isset($_POST['password'])&&!empty($_POST['password'])){
			$salt = genRandomPassword(32);
			$crypt = getCryptedPassword($password, $salt);
			$password2 = $crypt.':'.$salt;
			$query="UPDATE `users` SET `password` =  '$password2' WHERE `school_id` = '$hostel' AND `username` = '$username2'";
	}
}

if(isset($_GET['view'])){
	$book = $_REQUEST['view'];
$sql=$query="SELECT * FROM schools WHERE id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">View School Details

       <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>

   <div class="inside" style="background-color: #fff;">

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
     <td align="left" colspan="2" valign="middle"><br /></td>
    </tr>
     <tr>
        <td align="left" valign="middle">School Name: </td>
        <td  align="left" valign="middle"><?php echo $row['name']; ?> </td>
     </tr>
     <tr>
        <td align="left" valign="middle">Unique Username: </td>
        <td  align="left" valign="middle"><?php echo $row['username']; ?> </td>
     </tr>
     <tr>
        <td align="left" valign="middle">Student's Portal URL: </td>
        <td  align="left" valign="middle"><a href="<?php echo home_base_url().'schools/'.$row['username']; ?>" target="_blank"><?php echo home_base_url().'schools/'.$row['username']; ?></a> </td>
     </tr>
      <tr>
        <td align="left" valign="middle">Admin/Staff Portal URL: </td>
        <td  align="left" valign="middle"><a href="<?php echo home_base_url()?>admin.php" target="_blank"><?php echo home_base_url().'admin.php'; ?></a> </td>
     </tr>
      <tr>
        <td align="left" valign="middle">Applicants' Portal URL: </td>
        <td  align="left" valign="middle"><a href="<?php echo home_base_url().'applicant/'.$row['username']; ?>" target="_blank"><?php echo home_base_url().'applicant/'.$row['username']; ?></a> </td>
     </tr>
   	<tr>
        <td align="left" valign="middle">Admin Email Address: </td>
        <td  align="left" valign="middle"><?php echo $row['email']; ?> </td>
     </tr>
    <tr>
        <td align="left" valign="middle">Students Count: </td>
        <td  align="left" valign="middle"><?php echo etStudent($book); ?> </td>
     </tr>
     <tr>
        <td align="left" valign="middle">Parents Count: </td>
        <td  align="left" valign="middle"><?php echo countParent($book); ?> </td>
     </tr>
     <tr>
        <td align="left" valign="middle">Teachers Count: </td>
        <td  align="left" valign="middle"><?php echo countTeacher($book); ?> </td>
     </tr>
     <tr>
        <td align="left" valign="middle">Other Staff Count: </td>
        <td  align="left" valign="middle"><?php echo countStaff($book); ?> </td>
     </tr>
      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;</td>
      </tr>
    </table>

	</div>
</div>
<?php
}


if(isset($_GET['keyword'])){
$school_id = $school_id;
	$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);

	$ser = explode(' ', mysqli_real_escape_string($server,$_GET['keyword']));
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "p.name LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "p.username LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM schools p WHERE 1 AND $filter ";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search term.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM schools WHERE 1 ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no schools to manage";
		$class="blue";
		}
}

if(isset($_GET['success'])) {
		$message = "Congratulations!<br>Your enterprise license has been activated. You can now manage up to ".SCHOOLLIMIT." additional schools on with your SOA. ";
		if(TOTAL_SCHOOL==1) {
			$message = "Don't forget to update the Admin Username, Email and Password for your existing school before you proceed.";
		}
		$class="green";
}
?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <input type="search" name="keyword" placeholder="Search Schools" />
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(isOwner($userID) && SCHOOLLIMIT > 0) { ?>
        <a href="admin/schools?new"><button type="button" class="submit btn-success">Add School</button></a>
        <?php } ?>
         <?php if(isOwner($userID) ) { ?>
        <a href="admin/schools?enterprise"><button type="button" class="submit btn-success">Add License</button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="35%" scope="col">School Name</th>
                <th width="20%" scope="col">Students Count</th>
                <th width="20%" scope="col">Action</th>
              </tr>
             </table>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['name'];
					$available = etStudent($id).' Student(s)';

				?>
             <div class="virtualpage hidepeice">
              <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner">
                <td width="10%"> <?php echo $id; ?></td>
                <td width="35%"> <?php echo $title; ?></td>
                <td width="20%"> <?php echo $available; ?></td>
                <td width="20%" valign="middle">
                <a href="admin/schools?view=<?php echo $id;?>"><button class="btn-success">View</button></a>
                <a onclick="return confirm('You are about to launch SOA under <?=$title?>. Click OK to continue');" href="admin/schools?switch_school=<?php echo $id;?>"><button>Manage</button></a>
                <a href="admin/schools?edit=<?php echo $id;?>"><button class="btn-warning">Edit</button></a>
                <a onclick="return confirm('Are you sure you want to permanently delete <?=$title?> and all its data? Click OK to continue');" href="admin/schools?delete=<?php echo $id;?>"><button class="btn-danger">Remove</button></a>
                </td>
              </tr>
              </table>
              </div>
              <?php
				$i++;
				} ?>
<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
</div>
<?php } else {
$message = '<div style="background:#E1F1FD" class="message info">SOA Enterprise License allows you to manage up to 1000 different schools from a single SOA installation.<br>	Visit <a href="https://my.ynetinteractive.com/cart.php?gid=12" target="_blank">https://my.ynetinteractive.com/cart.php?gid=12</a> to purchase an Enterprise License that suites your requirements to get started.<br>If you have your Enterprise license key, please enter it below to activate. Ensure you have a working internet connection </div>';	
if(isset($_GET['error'])) {
	$message = '<div style="background:#FFCCCC" class="message error">Sorry but the Enterprise key you provided is not valid. Please check your key and try again or Visit <a href="https://my.ynetinteractive.com/cart.php?gid=12" target="_blank">https://my.ynetinteractive.com/cart.php?gid=12</a> to purchase an SOA Enterprise License Package</div>';
}?>
<div class="wrapper">
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
<?php
echo '<div class="container">
  <div class="content">
	<div class="box" style="text-align:center;">
        	'.$message.'
            <form action="" method="POST">
            	<input style="min-width: 70%" name="enterprise_key" type="text" id="key" required value="Your Enterprise License Key" maxlength="60" onfocus="if(this.value  == \'Your Enterprise License Key\') { this.value = \'\'; } " onblur="if(this.value == \'\') { this.value = \'Your Enterprise License Key\'; } ">
             <p class="in"> <button type="submit" class="submit" onclick="return confirm(\'Your Enterprise key will be applied to this SOA installation. Click OK to confirm\')" name="submit_e_key">Add License</button></p>
            </form>
        </div>
  	</div>
  </div>';

?>
        </div>
    </div>
</div>
<?php } ?>
