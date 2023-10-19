<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;
  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;


$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());
$message = '';
if(isset($_POST['savePrice'])) {
	$title = mysqli_real_escape_string($server,$_POST['title']);
	$rate = mysqli_real_escape_string($server,$_POST['rate']);
	$symbul = mysqli_real_escape_string($server,$_POST['symbul']);	

	//create associated customer.	 
		$add = mysqli_query($server,"INSERT INTO currency (`id`, `title`, `rate`, `symbul`, `school_id`) 
		VALUES (NULL, '$title', '$rate', '$symbul', '$school_id');") or die(mysqli_error($server));			
		
	$message = "The new currency successfully added.";
	$class="green";	

}
if(isset($_POST['update'])) {
	$title = mysqli_real_escape_string($server,$_POST['title']);
	$rate = mysqli_real_escape_string($server,$_POST['rate']);
	$symbul = mysqli_real_escape_string($server,$_POST['symbul']);	
	$id = mysqli_real_escape_string($server,$_POST['prid']);	

		mysqli_query($server,"UPDATE `currency` SET 
		`title` = '$title',
		`symbul` = '$symbul',
		`rate` = '$rate'
		 WHERE `id` = '$id'") or die(mysqli_error($server));		

	$message = "Your changes was successfully saved.";
	$class="green";	
}

if(isset($_REQUEST['default'])) {
	$id = mysqli_real_escape_string($server,$_REQUEST['default']);	

		mysqli_query($server,"UPDATE `schools` SET 
		`currency_id` = '$id'
		 WHERE `id` = '$school_id'") or die(mysqli_error($server));		

	$message = "The new default currency was successfully set. Remember to update your exchange rates accordingly..";
	$class="green";	
}

if(isset($_REQUEST['delete'])) {
	$id = filterinp($_REQUEST['ID']);
	$default = getSetting('currency_id');
	if(($id == $default) || ($id < 3)) {
		$message = "You cannot delete system currencies or default currency of your school!";
		$class="yellow";		
	}else {
		$sql = "DELETE FROM currency WHERE id = '$id'";
		$result = mysqli_query($server,$sql) or die(mysql_error($server));
		$message = "The selected currency has been deleted.";
		$class="green";		
	}
}
?>
<?php 
if(isset($_REQUEST['edit'])) {
	if(isset($_REQUEST['ID'])) {
		$id = filterinp($_REQUEST['ID']);
		$sql = "SELECT * FROM currency WHERE id = '$id'";
		$result = mysqli_query($server,$sql);
		$num = mysqli_num_rows($result);
	
		$row = mysqli_fetch_assoc($result); 
		$title = $row['title'];
		$symbul = $row['symbul'];
		$rate = $row['rate'];
		$prid = $id;
		$hed = 'Edit Currency';
	} else {
		$title = '';
		$rate = '1';
		$symbul = '';
		$prid = '';
		$hed = 'Create New Currency';
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
    <form method="post" action="admin/currency" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Currency Name: </td>
        <td  align="left" valign="middle">
        	<input type="text" name="title" id="title2" value="<?php echo $title; ?>" required="required" placeholder="">  </td>
      </tr>       
      <tr>
        <td align="left" valign="middle">Exchange Rate: <small>Relative to your default currency</small></td>
        <td  align="left" valign="middle">
        	<input type="text" name="rate" id="rate2" value="<?php echo $rate; ?>" required="required" placeholder="">  </td>
      </tr>      
      <tr>
        <td align="left" valign="middle">Currency Symbol: </td>
        <td  align="left" valign="middle">
        	<input type="text" name="symbul" id="symbul" value="<?php echo $symbul; ?>" required="required" placeholder="">        </td>
      </tr>          
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" <?php if(isset($_REQUEST['ID'])){ echo 'name="update"'; } else {echo 'name="savePrice"';} ?> />
        <input type="hidden" name="prid" value="<?php echo $prid; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="submit" value="1" type="submit">Save</button>
	</form>     
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Processing...</div>     
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
        <td align="left" valign="top"><br /><a href="admin/currency?edit"><button type="button" class="submit">Add Currency</button></a></td>
      </tr>               
    </table>
<?php 
	 $sql = "SELECT * FROM currency WHERE school_id = '$school_id' ORDER BY id DESC";
	$result = mysqli_query($server,$sql);
	$num = mysqli_num_rows($result);	
	if($num < "1")
		{
		$message = "No currencies found! Please add a gateway.";
		$class="blue";
		}	
?>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="30%" scope="col">Currency Name</th>
                <th width="10%" scope="col">Symbol</th>
                <th width="20%" scope="col">Exchange Rate</th>
                <th width="25%" scope="col">Action</th>
              </tr>
               <?php			

				while($rows=mysqli_fetch_assoc($result)){
					$id = $rows['id'];
					$name = $rows['title'];
					$symbul = $rows['symbul'];
					$rate = $rows['rate'];
				?>	         
              <tr class="inner">
                <td width=""> <?php echo $id; ?></td>
                <td width=""> <?php echo $name; ?></td>
                <td width=""> <?php echo $symbul; ?></td>
                <td width="" align="center"> <?php echo $rate; ?></td>
                <td width="" valign="middle">
                <a href="admin/currency?edit=1&ID=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/currency?delete=1&ID=<?php echo $id;?>"><button class="btn-danger">Delete</button></a> 
                <?php if(getSetting('currency_id') !== $id) { ?>
                <a href="admin/currency?default=<?php echo $id;?>"><button class="btn-success">Set as Default</button></a> 
                <?php } ?>
                </td>
              </tr>
              <?php } ?>
            </table>
      </div>
      </div>
  </div>

</div>
