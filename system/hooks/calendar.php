<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		calendar.php
Description:	This is the schedule page
Developer: 		Ynet Interactive
Date: 			16/05/201
*/

global $server;
define('calender',1);
$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());
$calender = true;

if(isset($_REQUEST['delete'])){
	 if(userRole($userID)>2) { header('location: admin/calendar'); }

	$book = filterinp($_REQUEST['delete']);
		$sql=$query = "DELETE FROM schedules WHERE id = '$book'";
		$result = mysqli_query($server,$query) or die(mysqli_error($server));

		$message = "The selected event was successfully deleted.";
		$class="green";

}

//set defaults
$session_id = getSetting('current_session');
$term_id = getSetting('current_term');


if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])){
	 if(userRole($userID)>2) { header('location: admin/calendar'); }

	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM schedules WHERE id = '$book'";
		$result = mysqli_query($server,$query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$period = $row['id'];
		$date = $row['date'];
		$schedule = $row['schedule'];
?>
<div id="add-new">
   <div id="add-new-head">Update Period
<?php
	} else {
		$period = '';
		$date = date('Y-m-d');
		$schedule = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Event
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/calendar" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Date:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="date" required="required" placeholder="" value="<?php echo $date; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Event:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="schedule" required="required" placeholder="" value="<?php echo $schedule; ?>">
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="60%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="period" value="<?php echo $period; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Event</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Event</button>
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

if(isset($_POST['add']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

		//create new prents
			$sql=$query = "INSERT INTO schedules (`id`, `school_id`, `date`,`schedule`) VALUES (NULL, '$school_id', '$date', '$schedule');";

			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new event was succesfully created.';
	$class = 'green';

}

if(isset($_POST['save']))
{
$period = $_POST['period'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "period") && ($key != "save") && ($key != "photo") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
			$sql=$query="UPDATE `schedules` SET `$key` =  '$value' WHERE `id` = '$period';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}

	$message = 'The selected event was succesfully updated.';
	$class = 'green';
}

$date = date('Y-m-d');
$today20 = increaseDate($date, '20');
$today10 = reduceDate($date, '10');
?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/calendar?new"><button type="button" class="submit success">Add Event</button></a>
        <?php } ?>
        </form>
    </div>

<?php
$sql=$query = "SELECT * FROM schedules WHERE school_id = '$school_id' AND (date BETWEEN '$today10' AND '$today20') ORDER BY date DESC LIMIT 24";

if(isset($_GET['keyword'])) {
	$today10 = $_GET['from'];
	$today20 = $_GET['to'];

	$school_id = $school_id;

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
	         $clauses[] = "p.schedule LIKE '%$term%' OR p.date LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "p.schedule LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM schedules p WHERE p.school_id = '$school_id' AND (date BETWEEN '$today10' AND '$today20') AND $filter ORDER BY date DESC";
}
$result = mysqli_query($server, $query) or die(mysqli_error($server));
$number = mysqli_num_rows($result);
?>
	<div class="panel">
    	<div class="panel-head"><i class="fa fa-calendar"></i> School Calendar</div>
			<div class="panel-bodyC">
        	<div id="calendar"></div>
        </div>
    </div>

</div>
