<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		pin.php
Description:	This is main PIN management page
Developer: 		Ynet Interactive
Date: 			04/5/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) > 2) {
header('location: admin.php');
}

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');



if(isset($_POST['save'])) {
	$quantity = $_POST['quantity'];
	$uses = $_POST['uses'];
	$validity_type = $_POST['validity_type'];

	if($validity_type == 'session') {
		$session_id = getSetting('current_session');
		$term_id = '0';
	} else {
		$session_id = getSetting('current_session');
		$term_id = getSetting('current_term');
	}

		$description = generatePIN($quantity, $school_id, $validity_type, $session_id, $term_id, $uses) ;
	if($validity_type == 'session') {
	$message = 'A total of '.$quantity.' PINs has been generated and saved as "'.$description.'". These PINs are valid only for this current school session. You can either prin these PINs now or later.';
	} else {
	$message = 'A total of '.$quantity.' PINs has been generated and saved as "'.$description.'". These PINs are valid only for this current school term. You can either prin these PINs now or later.';
	}
	$class="green";

}

if(isset($_REQUEST['print']))
{
	$desc = filterinp($_REQUEST['ID']);
	if(isset($_REQUEST['all'])) {
	 $sql=$query = "SELECT * FROM pin WHERE school_id = '$school_id' AND description = '$desc' ORDER BY id DESC LIMIT 1000";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	} else {
	 $sql=$query = "SELECT * FROM pin WHERE school_id = '$school_id' AND description = '$desc' AND student_id < '1' AND parent_id < '1' ORDER BY id DESC LIMIT 1000";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	}
?>
<div id="add-new">
   <div id="add-new-head"><?php echo $desc; ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
        	<div class="action-box" style="width: 100%;">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print All PINs</button></a>
            </div>
        <div id="print-this1" style="width:100%">
<?php
				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$date = date('d F, Y', strtotime($row['date_generated']));
					$serial = $row['serial'];
					$type = $row['validity_type'];
					$valid = $row['valid'];
					$used = $row['student_id'] + $row['parent_id'];
					$session = sessionName($row['session_id']);
					$term = termName($row['term_id']);

					if($type == 'session') {
						$type = $session;
					} else {
						$type = $term.' '.$session;
					}
				?>
			<div class="pins" <?php if(isset($_REQUEST['all']) && $used > 1) echo 'style="border-color:red; color: #999"' ?>>
            	<table style="width:100%">
                	<tr><td class="pin-title">Serial No.: </td><td  class="pin-desc"><?php echo @$serial; ?></td></tr>
                    <tr><td class="pin-title">PIN: </td><td  class="pin-desc"><?php echo @$valid; ?></td></tr>
                    <tr><td class="pin-title">Created on: </td><td  class="pin-desc"><?php echo @$date; ?></td></tr>
                    <tr><td class="pin-title">Validity:</td><td  class="pin-desc"><?php echo @$type; ?></td></tr>
                </table>
            </div>
            <?php
					$i++;
				} ?>
  </div>

</div>
</div>

 <?php
}


if(isset($_REQUEST['delete'])) {
	$id = filterinp($_REQUEST['ID']);
	$sql=$query = "DELETE FROM pin WHERE description = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$message = "The selected PIN was successfully deleted.";
	$class="green";
}

if(isset($_REQUEST['new'])) {

?>
<div id="add-new">
	<div id="add-new-head">Generate New Scratch Card PINs
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
     <div class="inside">
		<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
    <form method="post" action="admin/pin" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">PIN Validity Type:</td>
        <td  align="left" valign="middle">
        	<select name="validity_type" id="sex"  >
               <option  value="session"><?php echo 'PIN is valid only for Current Session'; ?></option>
                <option  value="term"><?php echo 'PIN is only valid for Current Term'; ?></option>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Portal Login Limit:</td>
        <td  align="left" valign="middle">
        	<select name="uses" id="uses"  >
               <option  value="0"><?php echo 'None (Unlimited portal access while valid)'; ?></option>
               <option  value="1"><?php echo '1 (Single portal login while valid)'; ?></option>
               <option  value="3"><?php echo '3 (Up to 3 portal logins while valid)'; ?></option>
               <option  value="5"><?php echo '5 (Up to 5 portal logins while valid)'; ?></option>
               <option  value="7"><?php echo '7 (Up to 7 portal logins while valid)'; ?></option>
               <option  value="10"><?php echo '10 (Up to 10 portal logins while valid)'; ?></option>
               <option  value="15"><?php echo '15 (Up to 15 portal logins while valid)'; ?></option>
               <option  value="20"><?php echo '20 (Up to 20 portal logins while valid)'; ?></option>
               <option  value="30"><?php echo '30 (Up to 30 portal logins while valid)'; ?></option>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Number of Cards to Create:</td>
        <td  align="left" valign="middle">
        	<input type="number" name="quantity" id="quantity" value="100" maxlength="1000" required="required" placeholder="">
        </td>
      </tr>
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" <?php if(isset($_REQUEST['ID'])){ echo 'name="update"'; } else {echo 'name="save"';} ?> />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="submit" value="1" type="submit">Generate PIN</button>
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
	$ser = explode(' ', $_GET['keyword']);
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "serial LIKE '%$term%' OR description LIKE '%$term%' ";
	    }
	    else
	    {
	         $clauses[] = "serial LIKE '%%' OR description LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM pin WHERE school_id = '$school_id' AND $filter ";

	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {

	 $sql=$query = "SELECT * FROM pin WHERE school_id = '$school_id' GROUP BY description LIMIT 1000";


	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "No records found!";
		$class="blue";
		}
	}

$state = '';
if (getSetting('current_session') < 1) {
$message = 'You have not defined the current accademic session yet!. <br>You must fix this before you can start admitting students. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/generalsetting" title="Difine Active Session">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
?>
<div class="wrapper">
	<div class="inner-left" style="width: 90%;">
  <div id="mess" style="position: relative; top: 0;"> <?php if(!empty($message)) { showMessage($message,$class); } ?> </div>
<?php
} elseif(getSetting('current_term') < 1) {
$message = 'You have not defined the current accademic term yet!. <br>You must fix this before you can start admitting students. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/generalsetting" title="Difine Active Term">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
} else { ?>
<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	 <a href="admin/pin?new"><button type="button" class="submit">Create PINs</button></a>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">Created On</th>
                <th width="20%" scope="col">Description</th>
                <th width="10%" scope="col">Quantity</th>
                <th width="25%" scope="col">Validity</th>
                <th width="10%" scope="col">Login Limit</th>
                <th width="25%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$date = date('d/m/Y', strtotime($row['date_generated']));
					$description = $row['description'];
					$type = $row['validity_type'];
					$uses = $row['uses'];
					$quantity = countPIN($row['description']);
					$session = sessionName($row['session_id']);
					$term = termName($row['term_id']);

					if($uses < 1) {
						$uses = 'Unlimited';
					}

					if($type == 'session') {
						$type = $session;
					} else {
						$type = $term.' '.$session;
					}
				?>
			<div class="virtualpage hidepeice">
              <tr class="inner">
                <td > <?php echo $date; ?></td>
                <td > <?php echo $description; ?></td>
                <td > <?php echo number_format($quantity); ?></td>
                <td > <?php echo $type; ?></td>
                <td > <?php echo $uses; ?></td>
                <td  valign="middle">
                <a href="admin/pin?print&all&ID=<?php echo $description;?>"><button class="btn-success">View All</button></a>
                <a href="admin/pin?print&unused&ID=<?php echo $description;?>"><button>View Unused</button></a>
                <a href="admin/pin?delete&ID=<?php echo $description;?>"><button class="btn-danger">Delete All</button></a>
                </td>
              </tr>
              <?php
						$i++;
					} ?>
              </div>
            </table>
<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
<?php }    ?>
</div>
