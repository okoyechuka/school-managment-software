<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;


global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());
$message = '';
if(userRole($userID) > 2) {
header('location: admin.php');
}

if(isset($_POST['save'])) {
	foreach ($_REQUEST as $key => $value ){
		${$key} = $value = mysqli_real_escape_string($server,$value);
	}
	if($custom_fields < 1) {
		mysqli_query($server, "INSERT INTO custom_fields (`school_id`, `label`,`type`, `form`) VALUES ('$school_id', '$title','$type', '$form');") or die(mysqli_error($server));
		$message = "Your new custom field was successfully created.";
		$class="green";
	} else {
		mysqli_query($server, "UPDATE custom_fields SET 
									`label` = '$title' ,
									`type` = '$type' ,
									`form` = '$form'  
			WHERE id = '$custom_fields'") or die(mysqli_error($server));
		$message = "Your changes was successfully saved.";
		$class="green";		
	}

}
if(isset($_REQUEST['delete'])) {
	$id = filterinp($_REQUEST['ID']);
	$sql=$query = "DELETE FROM custom_fields WHERE id = '$id' AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$message = "The selected custom field has been deleted.";
	$class="green";
}
?>


<?php
if(isset($_REQUEST['edit'])) {
	if(isset($_REQUEST['ID'])) {
		$id = filterinp($_REQUEST['ID']);
		$sql=$query = "SELECT * FROM custom_fields WHERE id = '$id' AND school_id = '$school_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$title = $row['label'];
		$type = $row['type'];
		$form = $row['form'];
		$prid = $id;
		$hed = 'Edit Custom Field';
	} else {
		$hed = 'Create Custom Field';
		$id = 0;
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
    <form method="post" action="admin/customfields" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Field Type</td>
        <td  align="left" valign="middle">
        	 <select name="form" required style="width:90%" >
             <option <?php if(@$form =='student') { echo 'selected';} ?> value="student" >Students Field</option>
			 <option <?php if(@$form =='staff') { echo 'selected';} ?> value="staff" >Staff Field</option>
			</select>
      </tr>
       <td align="left" valign="middle">Input Type</td>
        <td  align="left" valign="middle">
        	 <select name="type" required style="width:90%" >
             <option <?php if(@$type =='text') { echo 'selected';} ?> value="text" >Text Box</option>
			</select>
      </tr>
      <tr>
        <td align="left" valign="middle">Title</td>
        <td  align="left" valign="middle">
        	<input type="text" name="title" value="<?php echo @$title; ?>" required="required" placeholder="">
         </td>
      </tr>
		
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="custom_fields" value="<?php echo $id; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="save" value="1" type="submit">Save Field</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving...</div>
        </td>
      </tr>
   </table>
   </div>
 </div>
<?php } ?>

<div class="wrapper">
	<div class="inner-left" style="width: 100%;">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
    <table >
      <tr>
        <td align="left" width="80%" valign="top"><br /><br /><strong></strong></td>
        <td align="left" valign="top"><br /><a href="admin/customfields?edit"><button type="button" class="submit">Add Field</button></a></td>
      </tr>
    </table>
<?php
	 $sql=$query = "SELECT * FROM custom_fields WHERE school_id = '$school_id' ORDER BY id DESC";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "No Payment Gateways found! Please add a gateway.";
		$class="blue";
		}
?>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="30%" scope="col">Title</th>
                <th width="20%" scope="col">Field Type</th>
                <th width="20%" scope="col">Input Type</th>
                <th width="15%" scope="col">Action</th>
              </tr>
               <?php
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$name = $row['label'];
					$form = $row['form'];
					$type = ucfirst($row['type']);
				?>
              <tr class="inner">
                <td width=""> <?php echo $id; ?></td>
                <td width=""> <?php echo $name; ?></td>
                <td width=""> <?php echo $form; ?></td>
                <td width=""> <?php echo $type; ?></td>
                <td width="" valign="middle">
                <a href="admin/customfields?edit=1&ID=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/customfields?delete=1&ID=<?php echo $id;?>"><button class="btn-danger">Delete</button></a>
                </td>
              </tr>
              <?php
					} ?>
            </table>

<?php displayPagination($setLimit,$page,$query) ?>
  </div>

</div>
