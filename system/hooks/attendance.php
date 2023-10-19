<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		class.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			22/02/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) > 3 && userRole($userID) != 4) {
header('location: admin.php');
}

if (getSetting('current_term') < 1) {
$message = 'You have not defined the current accademic term yet!. <br>You must fix this before you can start taking attendance. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/generalsetting" title="Difine Active Term">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
}

if(isset($_REQUEST['delete']))
{
	$date = filterinp($_REQUEST['delete']);
	$class = filterinp($_REQUEST['class']);
	if(isset($_REQUEST['yes'])) {
		$sql=$query = "DELETE FROM student_attendance WHERE date = '$date' AND class_id = '$class'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));

		$message = "The selected attendance record was successfully deleted.";
		$class="green";
	} else {
		$message = "Are you sure you want to delete the selected attendance record? <br><a href='Attendance.php?date=".$date."&class".$class."&yes=1'>Yes I'm sure</a> <a href='Attendance.php'>Cancel</a>";
		$class="yellow";
	}
}

//start initiation
if(isset($_GET['new']))
{
	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');
?>
<div id="add-new">
   <div id="add-new-head">Take Attendance

            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/attendance" enctype="multipart/form-data">
    <table class="data_form" width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
       <tr>
        <td  align="left" valign="middle">Select Session:</td>
        <td  align="left" valign="middle">
        <select name="session" id="e1" style="width: 90%;" >
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
				            <?php
										$i++;
								}   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Select Term:</td>
        <td  align="left" valign="middle">
        <select name="term" id="e2" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row=mysqli_fetch_assoc($result)){
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
        <td align="left" valign="middle">Select Class:</td>
        <td  align="left" valign="middle">
        <select name="class" id="e3" style="width: 90%" >
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
				               <option  value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
				            <?php
									$i++;
								}  ?>
			</select>
        </td>
      </tr>

 	  <tr>
        <td align="left" valign="middle">Attendance Date:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="mark" id="date1" required="required"  value="<?php echo date('Y-m-d');?>">
      </td>
      </tr>
      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Continue &raquo;</button>
      </td>
      <td>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Fetching Students...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}

//view record
if(isset($_REQUEST['view']))
{

	if(isset($_REQUEST['view'])) {
	$date = filterinp($_REQUEST['view']);
	$class = filterinp($_REQUEST['class']);

		$sql=$query="SELECT * FROM student_attendance WHERE date = '$date' AND class_id = '$class'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$num = mysqli_num_rows($result);
?>
<div id="add-new">
   <div id="add-new-head"><?php echo date('d M, Y', strtotime($date)); ?> Attendance for <?php echo className($class); ?>
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
	<div id="Attendance">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right"  valign="middle"><strong><blue></blue></strong></td>
        <td>
        <a href="" class="no-print" onClick="javascript:printDiv('Attendance')"> <button class="submit">Print Report</button></a>
        </td>
      </tr>
      <tr>
        <td align="right" colspan="2" valign="middle"><strong><blue></blue></strong><br /></td>
        <td>
      </tr>
      <tr>
        <td align="left"   class="tr-heading"  valign="middle"><strong>Student</strong></td>
        <td  align="left"  class="tr-heading" valign="middle"><strong>Attendance</strong></td>
      </tr>
   <?php
	 $i=0;
	 while($row = mysqli_fetch_assoc($result)){
		   $attend = $row['attendance'];
		   if($attend != 'Present') {$tag1 = '<red>'; $tag2 = '</red>';} else {$tag1 = '<green>'; $tag2 = '</green>';}
		   ?>
		   		<tr>
		    	    <td align="left" valign="middle"><?php echo studentName($row['student_id']); ?></td>
		            <td  align="left" valign="middle">
					<?php echo $tag1.$row['attendance'].$tag2; ?>
		            </td>
		    	</tr>
				<?php
   		$i++;
	}
		?>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        </td>
      </tr>
    </table>
	</div>
	</div>
</div>
<?php
}


//mark or edit
if(isset($_REQUEST['edit']) || isset($_REQUEST['mark']))
{

	if(isset($_REQUEST['edit'])) {
	$date = filterinp($_REQUEST['edit']);
	$class = filterinp($_REQUEST['class']);

		$sql=$query="SELECT * FROM student_attendance WHERE date = '$date' AND class_id = '$class'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$num = mysqli_num_rows($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo className($class).' attendance record for '.date('d M, Y', strtotime($date)); ?>
<?php
	} else {
	$class = filterinp($_REQUEST['class']);
	$date = filterinp($_REQUEST['mark']);
	$school = $school_id;
	$session = filterinp($_REQUEST['session']);
	$term = filterinp($_REQUEST['term']);


	$sql=$query="SELECT * FROM student_class WHERE class_id = '$class' AND session_id = '$session'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
?>
<div id="add-new">
   <div id="add-new-head"><?php echo date('d M, Y', strtotime($date)); ?> Class Attendance for <?php echo className($class); ?>
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>

<?php
	if(isset($_REQUEST['mark']) && attendanceTaken($school_id, $class,$date)) {
    $message = 'Sorry but you have already taken attendance for the selected class and date!';
	$class = 'yellow';
	if(!empty($message)) { showMessage($message,$class); }
    ?>
    <table>
    <tr>
    <td>
        <a href="admin/attendance"><button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Close</button></a>
	</form>
        <td align="left" valign="top">&nbsp;</td>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Reverting Changes...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php	} else {

?>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/attendance" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td  class="tr-heading" align="left" valign="middle">Student</td>
        <td  class="tr-heading" align="left" valign="middle">Attendance</td>
      </tr>
   <?php
	 $i=0;
	 while($row = mysqli_fetch_assoc($result)){ ?>
   		<tr>
            <?php if(isset($_REQUEST['edit'])) { ?>
            <td align="left" valign="middle"><?php echo studentName($row['student_id']); ?></td>
            <td  align="left" valign="middle"><select name="attendance[<?php echo $row['id']; ?>]">
            	<option <?php if($row['attendance'] == 'Present') {echo 'selected';}?> value="Present">Present</option>
                <option <?php if($row['attendance'] == 'Absent') {echo 'selected';}?> value="Absent">Absent</option>
                <option <?php if($row['attendance'] == 'Late') {echo 'selected';}?> value="Late">Late</option>
                <option <?php if($row['attendance'] == 'Excused') {echo 'selected';}?> value="Excused">Excused</option>
             </select></td>
    	</tr>
        <?php } else { ?>
        <td align="left" valign="middle"><?php echo studentName($row['student_id']); ?></td>
        <td  align="left" valign="middle"><select name="attendance[<?php echo $row['student_id']; ?>]">
            	<option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Late">Late</option>
                <option value="Excused">Excused</option>
             </select></td>
    	</tr>
        <?php }
				$i++;
   		}
		?>

      <!-- Submit Buttons -->
      <tr>
        <td width="60%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="class_id" value="<?php echo $class; ?>" />
        <input type="hidden" name="date" value="<?php echo $date; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Record</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <input type="hidden" name="school_id" value="<?php echo $school; ?>" />
        <input type="hidden" name="session_id" value="<?php echo $session; ?>" />
        <input type="hidden" name="class_id" value="<?php echo $class; ?>" />
        <input type="hidden" name="term_id" value="<?php echo $term; ?>" />
        <input type="hidden" name="date" value="<?php echo $date; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Submit Attendance</button>
        <?php } ?>
	</form>
        <td align="left" valign="top">&nbsp;</td>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Changes...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
	}
}

if(isset($_POST['add']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
		//create new prents
		foreach($_POST['attendance'] as $student_id => $attendance){
			$sql=$query= "INSERT INTO student_attendance (`id`, `school_id`, `session_id`, `class_id`, `term_id`, `student_id`, `date`, `attendance`)
			VALUES (NULL, '$school_id', '$session_id', '$class_id', '$term_id', '$student_id', '$date', '$attendance');";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	$message = 'The new attendance record was succesfully created.';
	$class = 'green';

}

if(isset($_POST['save']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

		//create new prents
		foreach($_POST['attendance'] as $id => $attendance){
			$sql=$query="UPDATE `student_attendance` SET `attendance` =  '$attendance' WHERE `id` = '$id';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}

	$message = 'The selected attendance record was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword']))
{
$class_id = filterinp($_GET['class_id']);
$session_id = filterinp($_GET['session_id']);
$term_id = filterinp($_GET['term_id']);

$school_id = $school_id;

$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', filterinp($_GET['keyword']));
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "a.session_id = '$session_id' AND a.class_id = '$class+id' AND a.term_id = '$term_id' OR t.title LIKE '%$term%' OR ss.title LIKE '%$term%' OR a.date LIKE '%$term%' OR a.attendance LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "a.attendance LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM student_attendance a JOIN students s ON s.id = a.student_id JOIN classes c ON c.id = a.class_id JOIN terms t ON t.id = a.term_id JOIN sessions ss ON ss.id = a.session_id WHERE a.school_id = '$school_id' AND $filter ";
	if(userRole($userID) == 4) {
	$class_id = getTeacherClass(userProfile($userID));
	$sql=$query= "select * FROM student_attendance a JOIN students s ON s.id = a.student_id JOIN classes c ON c.id = a.class_id JOIN terms t ON t.id = a.term_id JOIN sessions ss ON ss.id = a.session_id WHERE a.school_id = '$school_id' AND a.class_id = '$class_id' AND $filter ";
	}
 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM student_attendance WHERE school_id = '$school_id' GROUP BY date, class_id  ORDER BY date DESC LIMIT $pageLimit,$setLimit";
	if(userRole($userID) == 4) {
	$class_id = getTeacherClass(userProfile($userID));
	$sql=$query = "SELECT * FROM student_attendance WHERE school_id = '$school_id' AND class_id = '$class_id' GROUP BY date, class_id ORDER BY date DESC LIMIT $pageLimit,$setLimit";
	}
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are no attendance records in your school";
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
                $sql3=$query3="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY start_date DESC";
                $result3 = mysqli_query($server, $query3);
                $num3 = mysqli_num_rows($result3);

                $i=0;
								while($row = mysqli_fetch_assoc($result3)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$session_id == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php
										$i++;
								}   ?>
            	<option selected value="" disabled>Select Session</option>
			</select>
        &nbsp;
        <select name="term_id" id="e2"  >
			<?php
                $sql2=$query2="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result2 = mysqli_query($server, $query2);
                $num2 = mysqli_num_rows($result2);

                while($row=mysqli_fetch_assoc($result2)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$term_id == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php }   ?>
            	<option selected value="" disabled>Select Term</option>
			</select>
		&nbsp;
        <select name="class_id" id="e3" >
			<?php
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
			if(userRole($userID) == 4) {
				$class_id = getTeacherClass(userProfile($userID));
				$sqlC=$queryC="SELECT * FROM classes WHERE id = '$class_id' ORDER BY title ASC";
			}
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);
                while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option <?php if(@$class_id == $c_id) {echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php	}  ?>
            <option selected value="" disabled>Select Class</option>
			</select> &nbsp;

        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3 || userRole($userID) == 4) { ?>
        <a href="admin/attendance?new"><button type="button" class="submit">Take Attendance</button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">Date</th>
                <th width="15%" scope="col">Session</th>
                <th width="15%" scope="col">Term</th>
                <th width="15%" scope="col">Class</th>
                <th width="7%" scope="col">Present</th>
                <th width="7%" scope="col">Absent</th>
                <th width="16%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row=mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$session = $row['session_id'];
					$term = $row['term_id'];
					$class = $row['class_id'];
					$date = $row['date'];


					?>
              <tr class="inner">
                <td width="10%"> <?php echo date('d/m/Y',strtotime($date)); ?></td>
                <td width="15%"> <?php echo sessionName($session); ?></td>
                <td width="15%"> <?php echo termName($term); ?></td>
                <td width="15%"> <?php echo className($class); ?></td>
                <td width="7%"> <?php echo countPresent($date, $class); ?></td>
                <td width="7%"> <?php echo countAbsent($date, $class); ?></td>
                <td width="16%" valign="middle">
                <a href="admin/attendance?view=<?php echo $date;?>&class=<?php echo $class;?>"><button class="success">View </button></a>
                <a href="admin/attendance?edit=<?php echo $date;?>&class=<?php echo $class;?>"><button>Edit </button></a>
                <a href="admin/attendance?delete=<?php echo $id;?>"><button class="danger">Remove</button></a>
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
