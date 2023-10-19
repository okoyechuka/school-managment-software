<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		timetable.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			16/10/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());


if(isset($_REQUEST['delete']))
{
	 if(userRole($userID)>2) { header('location: TimeTable.php'); }

	$book = filterinp($_REQUEST['delete']);
		$sql=$query = "DELETE FROM timetable WHERE id = '$book' AND school_id = '$school_id'";
		$result = mysqli_query($server, $sql) or die(mysqli_error($server));

		$message = "The selected period was successfully deleted.";
		$class="green";

}

//set defaults
$session_id = getSetting('current_session');
$term_id = getSetting('current_term');
$class_id = '';

//add culk periods
if(isset($_POST['add_bulk']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

		//create new
		for($i = 0; $i<10; $i++){
			$start_time = $_POST['start_time'.$i];
			$end_time = $_POST['end_time'.$i];

			if(!empty($start_time)) {
				$monday = $_POST['monday'.$i];
				$teusday = $_POST['tuesday'.$i];
				$wednesday = $_POST['wednesday'.$i];
				$thursday = $_POST['thursday'.$i];
				$friday = $_POST['friday'.$i];
				$saturday = $_POST['saturday'.$i];

				//do for monday
				if(!empty($monday)) {
					$day = 'Monday';
					$activity = $monday;
					$sql=$query ="INSERT INTO timetable (`id`, `school_id`, `session_id`, `term_id`, `class_id`, `day`,`start_time`,
						`end_time`,`activity`) VALUES (NULL, '$school_id', '$session_id', '$term_id', '$class_id', '$day','$start_time', '$end_time',
							'$activity');";
					mysqli_query($server, $query) or die(mysqli_error($server));
				}

				//do for monday
				if(!empty($teusday)) {
					$day = 'Tuesday';
					$activity = $teusday;
					$sql=$query = "INSERT INTO timetable (`id`, `school_id`, `session_id`, `term_id`, `class_id`,
						`day`,`start_time`, `end_time`,`activity`) VALUES (NULL, '$school_id', '$session_id', '$term_id', '$class_id',
							'$day','$start_time', '$end_time', '$activity');";
					mysqli_query($server, $query) or die(mysqli_error($server));
				}

				//do for monday
				if(!empty($wednesday)) {
					$day = 'Wednesday';
					$activity = $wednesday;
					$sql=$query = "INSERT INTO timetable (`id`, `school_id`, `session_id`, `term_id`, `class_id`, `day`,`start_time`,
						`end_time`,`activity`) VALUES (NULL, '$school_id', '$session_id', '$term_id', '$class_id', '$day','$start_time',
						'$end_time', '$activity');";
					mysqli_query($server, $query) or die(mysqli_error($server));
				}

				//do for monday
				if(!empty($thursday)) {
					$day = 'Thursday';
					$activity = $thursday;
					$sql=$query = "INSERT INTO timetable (`id`, `school_id`, `session_id`, `term_id`, `class_id`, `day`,`start_time`,
						`end_time`,`activity`) VALUES (NULL, '$school_id', '$session_id', '$term_id', '$class_id', '$day','$start_time',
							'$end_time', '$activity');";
					mysqli_query($server, $query) or die(mysqli_error($server));
				}

				//do for monday
				if(!empty($friday)) {
					$day = 'Friday';
					$activity = $friday;
					$sql=$query = "INSERT INTO timetable (`id`, `school_id`, `session_id`, `term_id`, `class_id`, `day`,`start_time`,
						`end_time`,`activity`) VALUES (NULL, '$school_id', '$session_id', '$term_id', '$class_id', '$day','$start_time',
							'$end_time', '$activity');";
					mysqli_query($server, $query) or die(mysqli_error($server));
				}

				//do for monday
				if(!empty($saturday)) {
					$day = 'Saturday';
					$activity = $saturday;
					$sql=$query = "INSERT INTO timetable (`id`, `school_id`, `session_id`, `term_id`, `class_id`, `day`,`start_time`,
					`end_time`,`activity`) VALUES (NULL, '$school_id', '$session_id', '$term_id', '$class_id', '$day','$start_time',
						'$end_time', '$activity');";
					mysqli_query($server, $query) or die(mysqli_error($server));
				}

			}
		}
	$message = 'The new periods were succesfully created.';
	$class = 'green';

}
//add single perion
if(isset($_REQUEST['bulk']))
{
	 if(userRole($userID)>2) { header('location: TimeTable.php'); }

		$period = '';
		$currentSession = getSetting('current_session');
		$currentTerm = getSetting('current_term');
		$currentClass = '';
?>
<div id="add-new">
   <div id="add-new-head">Create Multiple Periods

            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/timetable" enctype="multipart/form-data">
    <table class="data_form" width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
       <tr>
        <td  align="left" valign="middle">Select Session:</td>
        <td  align="left" valign="middle">
        <select name="session_id" id="e1" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY start_date DESC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($currentSession == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php  $i++;
								}   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Select Term:</td>
        <td  align="left" valign="middle">
        <select name="term_id" id="e2" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($currentTerm == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php
									$i++;
								}   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Select Class:</td>
        <td  align="left" valign="middle">
        <select name="class_id" id="e3" style="width: 90%" >
			<?php
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
			if(userRole($userID) == 4) {
				$class_id = getTeacherClass(userProfile($userID));
				$sqlC=$queryC="SELECT * FROM classes WHERE id = '$class_id' ORDER BY title ASC";
			}
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

								$i=0;
								while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option <?php if($currentClass == $c_id) { echo 'selected ';}?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  $i++; }  ?>
			</select>
        </td>
      </tr>
      </table>
      
<!-- Draw new table for periods -------------------->
      <div style="overflow-x:auto; width: 100%;">
       		<table class="list" style="width:100%;min-width:800px; border: 0" border="0" cellspacing="0" cellpadding="0" >
              <tr style="border-bottom: 0">
                <td style="width:200px;" align="left" valign="middle">Periods</td>
                <td align="left" valign="middle">Activities</td>
              </tr>
             </table>
             <table class="list" style="width:100%;min-width:800px; border:0" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="100" style="width:100px;" align="middle" valign="middle"> Start</th>
                <th width="11%" align="middle" valign="middle"> End</th>
                <th width="13%" align="middle" valign="middle"> Monday</th>
                <th width="13%" align="middle" valign="middle"> Tuesday</th>
                <th width="13%" align="middle" valign="middle"> Wednesday</th>
                <th width="13%" align="middle" valign="middle"> Thursday</th>
                <th width="13%" align="middle" valign="middle"> Friday</th>
                <th width="13%" align="middle" valign="middle"> Saturday</th>
              </tr>
     <?php for($count = 0; $count < 10; $count++) {?>
              <tr>
              	<td align="left" style="width:100px;" valign="middle"><input type="text" class="timepicker" name="start_time<?php echo $count ?>" placeholder="HH:MM AM" value=""></td>
                <td align="left" valign="middle"><input type="text" class="timepicker" name="end_time<?php echo $count ?>" placeholder="HH:MM AM" value=""></td>
                <td align="left" valign="middle"><input type="text" name="monday<?php echo $count ?>" placeholder="Activity" value=""></td>
                <td align="left" valign="middle"><input type="text" name="tuesday<?php echo $count ?>" placeholder="Activity" value=""></td>
                <td align="left" valign="middle"><input type="text" name="wednesday<?php echo $count ?>" placeholder="Activity" value=""></td>
                <td align="left" valign="middle"><input type="text" name="thursday<?php echo $count ?>" placeholder="Activity" value=""></td>
                <td align="left" valign="middle"><input type="text" name="friday<?php echo $count ?>" placeholder="Activity" value=""></td>
                <td align="left" valign="middle"><input type="text" name="saturday<?php echo $count ?>" placeholder="Activity" value=""></td>
              </tr>
     <?php } ?>
             </table>
     </div>

      <!-- Submit Buttons -->
    <table width="100%">
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td  align="left" valign="top">
        <input type="hidden" name="add_bulk" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Save Periods</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}

//add single perion
if(isset($_REQUEST['edit']) || isset($_REQUEST['new']))
{
	 if(userRole($userID)>2) { header('location: TimeTable.php'); }

	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM timetable WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$period = $row['id'];
		$currentSession = $row['session_id'];
		$currentTerm = $row['term_id'];
		$currentClass = $row['class_id'];
		$day = $row['day'];
		$start_time = $row['start_time'];
		$end_time = $row['end_time'];
		$activity = $row['activity'];
?>
<div id="add-new">
   <div id="add-new-head">Update Period
<?php
	} else {
		$period = '';
		$currentSession = getSetting('current_session');
		$currentTerm = getSetting('current_term');
		$currentClass = '';
		$day = '';
		$start_time = '8:00 AM';
		$end_time = '9:00 AM';
		$activity = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Period
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/timetable" enctype="multipart/form-data">
    <table class="data_form" width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
       <tr>
        <td  align="left" valign="middle">Select Session:</td>
        <td  align="left" valign="middle">
        <select name="session_id" id="e1" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY start_date DESC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($currentSession == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php  $i++;
								}   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Select Term:</td>
        <td  align="left" valign="middle">
        <select name="term_id" id="e2" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($currentTerm == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php  $i++;
								$i++;
								}   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Select Class:</td>
        <td  align="left" valign="middle">
        <select name="class_id" id="e3" style="width: 90%" >
			<?php
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
			if(userRole($userID) == 4) {
				$class_id = getTeacherClass(userProfile($userID));
				$sqlC=$queryC="SELECT * FROM classes WHERE id = '$class_id' ORDER BY title ASC";
			}
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

								$i=0;
								while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option <?php if($currentClass == $c_id) { echo 'selected ';}?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  $i++;
								}  ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Week Day:</td>
        <td  align="left" valign="middle">
        	<select name="day" id="e5" style="width: 90%" >
               <option <?php if($day == 'Monday') { echo 'selected ';}?> value="Monday">Monday</option>
               <option <?php if($day == 'Tuesday') { echo 'selected ';}?> value="Tuesday">Tuesday</option>
               <option <?php if($day == 'Wednesday') { echo 'selected ';}?> value="Wednesday">Wednesday</option>
               <option <?php if($day == 'Thursday') { echo 'selected ';}?> value="Thursday">Thursday</option>
               <option <?php if($day == 'Friday') { echo 'selected ';}?> value="Friday">Friday</option>
               <option <?php if($day == 'Saturday') { echo 'selected ';}?> value="Saturday">Saturday</option>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Start Time:</td>
        <td  align="left" valign="middle">
        	<input type="text" name="start_time" class="timepicker" required="required" placeholder="HH:MM AM" value="<?php echo $start_time; ?>">
        </td>
       </tr>
      <tr>
        <td align="left" valign="middle">End Time:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="end_time" class="timepicker" required="required" placeholder="HH:MM AM" value="<?php echo $end_time; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Activity:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="activity" required="required" placeholder="" value="<?php echo $activity; ?>">
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td  align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="period" value="<?php echo $period; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Period</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Period</button>
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
			$sql=$query = "INSERT INTO timetable (`id`, `school_id`, `session_id`, `term_id`, `class_id`, `day`,`start_time`,
				`end_time`,`activity`) VALUES (NULL, '$school_id', '$session_id', '$term_id', '$class_id', '$day','$start_time',
					'$end_time', '$activity');";
			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new period was succesfully created.';
	$class = 'green';

}


if(isset($_POST['save']))
{
$period = $_POST['period'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "period") && ($key != "save") && ($key != "photo") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
			mysqli_query($server, "UPDATE `timetable` SET `$key` =  '$value' WHERE `id` = '$period';") or die(mysqli_error($server));
		}
	}



	$message = 'The selected period was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['class_id']))
{
$school_id = $school_id;
$session_id = filterinp($_GET['session_id']);
$term_id = filterinp($_GET['term_id']);
$class_id = filterinp($_GET['class_id']);

	$sql=$query = "SELECT * FROM timetable WHERE school_id = '$school_id' AND class_id = '$class_id' AND session_id = '$session_id' AND term_id = '$term_id'";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if(($num < "1") && !isset($_REQUEST['day']))
		{
		$message = "There are currently no period defined for the selected class";
		$class="blue";
		}
} else {
		if(!isset($_REQUEST['day']) && !isset($_REQUEST['add']) && !isset($_REQUEST['add_bulk']) && !isset($_REQUEST['bulk']) && !isset($_REQUEST['new']) && !isset($_REQUEST['edit'])) {
			$message = "Choose a Session, Term and Class to view time table.";
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
        <select name="session_id" id="e1"  >
			<?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY start_date DESC";
                $result = mysqli_query($server, $query);
                $numS = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($session_id == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php  $i++ ;}   ?>
            <option selected value="" disabled>Select Session</option>
		</select>
        &nbsp;
        <select name="term_id" id="e2"  >
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $numT = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($term_id == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php  $i++; }   ?>
            <option selected value="" disabled>Select Term</option>
		</select>
        &nbsp;
        <select name="class_id" id="e3"  >
			<?php
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
			if(userRole($userID) == 4) {
				$class_id = getTeacherClass(userProfile($userID));
				$sqlC=$queryC="SELECT * FROM classes WHERE id = '$class_id' ORDER BY title ASC";
			}
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

								$i=0;
								while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option <?php if($class_id == $c_id) { echo 'selected ';}?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  $i++; }  ?>
            <option selected value="" disabled>Select Class</option>
			</select>

        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/timetable?new=<?php echo $id;?>"><button type="button">Create Single Period</button></a>
         <a href="admin/timetable?bulk=<?php echo $id;?>"><button type="button" class="success">Create Bulk Periods</button></a>
        <?php } ?>
        </form>
    </div>
    <?php
	if(isset($_GET['class_id'])) {
	?>

	<div class="panel" style="border-color: #036; width: 95%; margin-right: 2%">
    <div class="panel-head"> Mondays</div>
        <div class="panel-body">
        <?php
		$sql=$query = "SELECT * FROM timetable WHERE school_id = '$school_id' AND class_id = '$class_id' AND session_id = '$session_id' AND term_id = '$term_id' AND day = 'Monday' ORDER BY start_time DESC";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);

		if($num < 1) { echo "No activities to display"; } else { ?>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="30%" scope="col">Period</th>
                <th width="50%" scope="col">Activity</th>
                <th width="20%" scope="col"></th>
              </tr>
               <?php
		}
		$i=0;
		while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
                <td width="20%" valign="middle">
                 <?php if(userRole($userID)<3) { ?>
                <a href="admin/timetable?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/timetable?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php $i++;
							} ?>
              </table>
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
                <th width="20%" scope="col"></th>
              </tr>
               <?php
		}
		$i=0;
		while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
                <td width="20%" valign="middle">
                 <?php if(userRole($userID)<3) { ?>
                <a href="admin/timetable?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/timetable?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php $i++;
							} ?>
              </table>
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
                <th width="20%" scope="col"></th>
              </tr>
               <?php
		}
		$i=0;
		while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
                <td width="20%" valign="middle">
                 <?php if(userRole($userID)<3) { ?>
                <a href="admin/timetable?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/timetable?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php $i++;
						} ?>
              </table>
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
                <th width="20%" scope="col"></th>
              </tr>
               <?php
		}
		$i=0;
		while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
                <td width="20%" valign="middle">
                 <?php if(userRole($userID)<3) { ?>
               <a href="admin/timetable?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/timetable?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php $i++; } ?>
                     </table>
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
                <th width="20%" scope="col"></th>
              </tr>
               <?php
		}
		$i=0;
		while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
                <td width="20%" valign="middle">
                 <?php if(userRole($userID)<3) { ?>
                <a href="admin/timetable?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/timetable?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php $i++; } ?>
              </table>
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
                <th width="20%" scope="col"></th>
              </tr>
               <?php
		}
		$i=0;
		while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$activity = $row['activity'];
					$period = $row['start_time'].' -- '.$title = $row['end_time'];

				?>
              <tr class="inner">
                <td width="30%"> <?php echo $period; ?></td>
                <td width="50%"> <?php echo $activity; ?></td>
                <td width="20%" valign="middle">
                 <?php if(userRole($userID)<3) { ?>
                <a href="admin/timetable?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/timetable?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php $i++;
						} ?>

              </table>
        </div>
    </div>

<?php }   ?>


</div>
