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

	$tempName = paymentGatewayTemplateData($type,'title');

	$currency_id = paymentGatewayTemplateData($type,'currency_id');
	if($currency_id<1) {
		$currency_id = $Currency->Code(getUser());
	}
	if($gateway_id < 1) {
		mysqli_query($server, "INSERT INTO paymentgateways (`customer_id`, `title`,`type`, `enabled`, `text`, `param1`, `param2`, `param3`, `currency_id`) VALUES ('$school_id', '$title','$type', '$enabled', '$text', '$param1', '$param2', '$param3', '$currency_id');") or die(mysqli_error($server));
		$message = "Your changes was successfully saved.";
		$class="green";
	} else {
		mysqli_query($server, "UPDATE paymentgateways SET 
									`title` = '$title' ,
									`type` = '$type' ,
									`enabled` = '$enabled' ,
									`text` = '$text' ,
									`param1` = '$param1' ,
									`param2` = '$param2' ,
									`param3` = '$param3' ,
									`currency_id` = '$currency_id' 
			WHERE id = '$gateway_id'") or die(mysqli_error($server));
		$message = "Your changes was successfully saved.";
		$class="green";		
	}

}
if(isset($_REQUEST['delete'])) {
	$id = filterinp($_REQUEST['ID']);
	$sql=$query = "DELETE FROM paymentgateways WHERE id = '$id' AND customner_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$message = "The selected payment gateway has been deleted.";
	$class="green";
}
?>


<?php
if(isset($_REQUEST['edit'])) {
	if(isset($_REQUEST['ID'])) {
		$id = filterinp($_REQUEST['ID']);
		$sql=$query = "SELECT * FROM paymentgateways WHERE id = '$id' AND customer_id = '$school_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$title = $row['title'];
		$type = $row['type'];
		$enabled = $row['enabled'];
		$text = $row['text'];
		$param1 = $row['param1'];
		$param2 = $row['param2'];
		$param3 = $row['param3'];
		
		$lab1=$lab2=$lab3='';
		$dis1=$dis2=$dis3='display:none;';
		if(paymentGatewayTemplateData($type,'param1_label')!='') {
			$dis1="display: block;";
			$lab1=paymentGatewayTemplateData($type,'param1_label');
		}
		if(paymentGatewayTemplateData($type,'param2_label')!='') {
			$dis2="display: block;";
			$lab2=paymentGatewayTemplateData($type,'param2_label');
		}
		if(paymentGatewayTemplateData($type,'param3_label')!='') {
			$dis3="display: block;";
			$lab3=paymentGatewayTemplateData($type,'param3_label');
		}
		
		$prid = $id;
		$hed = 'Edit Payment Gateway';
	} else {
		$hed = 'Create Payment Gateway';
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
    <form method="post" action="admin/paymentgateway" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Payment Gateway Type</td>
        <td  align="left" valign="middle">
        	 <select name="type" id="gtype" required style="width:90%" >
             <option <?php if(empty($type)) { echo 'selected';} ?> value="" disabled >Choose a Gateway Type</option>
			<?php
                $sql=$query="SELECT * FROM paymentgateway_templates ORDER BY id ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);
			while($row = mysqli_fetch_assoc($result)){
                $alias = $row['alias'];
                $title2 = $row['title'];
            ?>
               <option <?php if($alias==@$type) { echo 'selected';} ?> value="<?php echo $alias; ?>"><?php echo $title2; ?></option>
            <?php	}   ?>
			</select>
      </tr>
      <tr>
        <td align="left" valign="middle">Gateway Title</td>
        <td  align="left" valign="middle">
        	<input type="text" name="title" value="<?php echo @$title; ?>" required="required" placeholder="">
         </td>
      </tr>

      <tr id="show_p1" style="<?=$dis1?>">
        <td align="left" valign="middle"><span id="label_p1"><?=$lab1?></span></td>
        <td  align="left" valign="middle">
        	<input type="text" name="param1" id="param1" value="<?php echo @$param1; ?>"  placeholder="">
        </td>
      </tr>

      <tr id="show_p2" style="<?=$dis2?>">
        <td align="left" valign="middle"><span id="label_p2"><?=$lab2?></span></td>
        <td  align="left" valign="middle">
        	<input type="text" name="param2" id="param2" value="<?php echo @$param2; ?>"  placeholder="">
        </td>
      </tr>

      <tr id="show_p3" style="<?=$dis3?>">
        <td align="left" valign="middle"><span id="label_p3"><?=$lab3?></span></td>
        <td  align="left" valign="middle">
        	<input type="text" name="param3" id="param3" value="<?php echo @$param3; ?>"  placeholder="">
        </td>
      </tr>
      
      <tr>
        <td align="left" valign="top">Custom Gateway Text:</td>
        <td  align="left" valign="middle">
            	<textarea id="test" name="text" placeholder="Type the text that is shown to users when they choose to pay with this gateway" style="height: 100px; width: 90%" ><?php echo @$text; ?></textarea>
        </td>
      </tr>
		
        <td align="left" valign="middle">Enable This Gateway?</td>
        <td  align="left" valign="middle">
        	 <select name="enabled" required style="width:90%" >
             <option <?php if($enabled ==1) { echo 'selected';} ?> value="1" >Yes</option>
			 <option <?php if($enabled ==0) { echo 'selected';} ?> value="0" >No</option>
			</select>
      </tr>
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="gateway_id" value="<?php echo $id; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="save" value="1" type="submit">Save Gateway</button>
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
        <td align="left" valign="top"><br /><a href="admin/paymentgateway?edit"><button type="button" class="submit">Add Gateway</button></a></td>
      </tr>
    </table>
<?php
	 $sql=$query = "SELECT * FROM paymentgateways WHERE customer_id = '$school_id' ORDER BY id DESC";
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
                <th width="40%" scope="col">Title</th>
                <th width="20%" scope="col">Type</th>
                <th width="10%" scope="col">Status</th>
                <th width="15%" scope="col">Action</th>
              </tr>
               <?php
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$name = $row['title'];
					$status = $row['enabled'];
					$alias = ucfirst($row['type']);
					if($status == '1') { $status = 'Active';} else { $status = 'Disabled';}
				?>
              <tr class="inner">
                <td width=""> <?php echo $id; ?></td>
                <td width=""> <?php echo $name; ?></td>
                <td width=""> <?php echo $alias; ?></td>
                <td width=""> <?php echo $status; ?></td>
                <td width="" valign="middle">
                <a href="admin/paymentgateway?edit=1&ID=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/paymentgateway?delete=1&ID=<?php echo $id;?>"><button class="btn-danger">Delete</button></a>
                </td>
              </tr>
              <?php
					} ?>
            </table>

<?php displayPagination($setLimit,$page,$query) ?>
  </div>

</div>
