<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		timetable.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			10/03/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());


//set defaults
if(userRole($userID) == 6) {
$session_id = getSetting('current_session');
$term_id = getSetting('current_term');
$class_id = getClass(userProfile($userID),$session_id);

?>
<div class="wrapper">

	<div class="panel" style="border-color: #036; width: 95%; margin-right: 2%">
    <div class="panel-head"> Mondays</div>
        <div class="panel-body">
        <?php
		$sql=$query = "SELECT * FROM timetable WHERE school_id = '$school_id' AND class_id = '$class_id' AND session_id = '$session_id' AND term_id = '$term_id' AND day = 'Monday' ORDER BY id ASC";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);

		if($num < 1) { echo "No activities to display"; } else { ?>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="30%" scope="col">Period</th>
                <th width="50%" scope="col">Activity</th>
              </tr>
             </table>
               <?php
		}
				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
              <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
              </tr>
              </table>
              <?php $i++;
						} ?>
          </div>
        </div>

	<div class="panel" style="border-color: #036; width: 95%; margin-right: 2%">
    <div class="panel-head"> Wednesdays</div>
        <div class="panel-body">
        <?php
		$sql=$query = "SELECT * FROM timetable WHERE school_id = '$school_id' AND class_id = '$class_id' AND session_id = '$session_id' AND term_id = '$term_id' AND day = 'Wednesday' ORDER BY id ASC";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);

		if($num < 1) { echo "No activities to display"; } else {?>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="30%" scope="col">Period</th>
                <th width="50%" scope="col">Activity</th>
              </tr>
             </table>
               <?php
		}
				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
             <div class="virtualpage hidepeice">
              <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
              </tr>
              </table>
              </div>
              <?php $i++; } ?>
          </div>
        </div>


	<div class="panel" style="border-color: #036; width: 95%; margin-right: 2%">
    <div class="panel-head"> Fridays</div>
        <div class="panel-body">
        <?php
		$sql=$query = "SELECT * FROM timetable WHERE school_id = '$school_id' AND class_id = '$class_id' AND session_id = '$session_id' AND term_id = '$term_id' AND day = 'Friday' ORDER BY id ASC";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);

		if($num < 1) { echo "No activities to display"; } else {?>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="30%" scope="col">Period</th>
                <th width="50%" scope="col">Activity</th>
              </tr>
             </table>
               <?php
		}
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
             <div class="virtualpage hidepeice">
              <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
              </tr>
              </table>
              </div>
              <?php $i++; } ?>
          </div>
        </div>

	<div class="panel" style="border-color: #036; width: 95%; margin-right: 0%">
    <div class="panel-head"> Tuesdays</div>
        <div class="panel-body">
        <?php
		$sql=$query = "SELECT * FROM timetable WHERE school_id = '$school_id' AND class_id = '$class_id' AND session_id = '$session_id' AND term_id = '$term_id' AND day = 'Tuesday' ORDER BY id ASC";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);

		if($num < 1) { echo "No activities to display"; } else {?>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="30%" scope="col">Period</th>
                <th width="50%" scope="col">Activity</th>
              </tr>
             </table>
               <?php
		}
				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
             <div class="virtualpage hidepeice">
              <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
              </tr>
              </table>
              </div>
              <?php $i++; } ?>
          </div>
        </div>




	<div class="panel" style="border-color: #036; width: 95%; margin-right: 2%">
    <div class="panel-head"> Thursdays</div>
        <div class="panel-body">
        <?php
		$sql=$query = "SELECT * FROM timetable WHERE school_id = '$school_id' AND class_id = '$class_id' AND session_id = '$session_id' AND term_id = '$term_id' AND day = 'Thursday' ORDER BY id ASC";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);

		if($num < 1) { echo "No activities to display"; } else { ?>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="30%" scope="col">Period</th>
                <th width="50%" scope="col">Activity</th>
              </tr>
             </table>
               <?php
		}
		$i=0;
		while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
             <div class="virtualpage hidepeice">
              <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
              </tr>
              </table>
              </div>
              <?php $i++; } ?>
          </div>
        </div>



	<div class="panel" style="border-color: #036; width: 95%; margin-right: 2%">
    <div class="panel-head"> Saturdays</div>
        <div class="panel-body">
        <?php
		$sql=$query = "SELECT * FROM timetable WHERE school_id = '$school_id' AND class_id = '$class_id' AND session_id = '$session_id' AND term_id = '$term_id' AND day = 'Saturday' ORDER BY id ASC";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);

		if($num < 1) { echo "No activities to display"; } else {?>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="30%" scope="col">Period</th>
                <th width="50%" scope="col">Activity</th>
              </tr>
             </table>
               <?php
		}
		$i=0;
		while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
             <div class="virtualpage hidepeice">
              <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
              </tr>
              </table>
              </div>
              <?php $i++; } ?>

        </div>
    </div>

<?php } ?>

</div>