<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		exam.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			2/02/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) > 3 && userRole($userID) != 4) {
header('location: index.php');
}

	$session = getSetting('current_session');
	$term = getSetting('current_term');
	$class="0";

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

//check if subject exist
$sqlC=$queryC="SELECT * FROM subject WHERE school_id = '$school_id' ORDER BY title ASC";
   $resultC = mysqli_query($server, $queryC);
   $numS = mysqli_num_rows($resultC);

if (getSetting('current_session') < 1) {
$message = 'You have not defined the current accademic session yet!. <br>You must fix this before you can manage fees. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/generalsetting" title="Difine Active Session">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
}

if ($numS < 1) {
$message = 'You have not created any subjects yet!. <br>You must fix this before you can manage fees. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/subject?new" title="Create Subjects">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
}

if(isset($_REQUEST['delete']))
{
	$delete = filterinp($_REQUEST['delete']);
	if(isset($_REQUEST['yes'])) {
		$sql=$query = "DELETE FROM exams WHERE id = '$delete' ";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));

		$message = "The fee was successfully deleted.";
		$class="green";
	} else {
		$message = "Are you sure you want to delete the selected exam? <br><a href='admin/exam?delete=".$id."&yes=1'>Yes I'm sure</a> <a href='admin/exam'>Cancel</a>";
		$class="yellow";
	}
}

//start initiation
if(isset($_GET['new']) || isset($_REQUEST['edit']))
{
	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

	if(isset($_REQUEST['edit'])) {
	$edit = filterinp($_REQUEST['edit']);

		$sql=$query="SELECT * FROM exams WHERE id = '$edit'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);

		$currentSession = $row['session_id'];
		$currentTerm = $row['term_id'];
		$title = $row['title'];
		$is_cumulative = $row['is_cumulative'];
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['title']; ?>
<?php
	} else {
	$title = '';
	$class = '';
	$is_cumulative = 1;
?>
<div id="add-new">
   <div id="add-new-head">Create New Exam
<?php } ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/exam" enctype="multipart/form-data">
    <table  border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
 	  <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" id="titles" required="required"  value="<?php echo $title;?>">
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
                $title0 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($currentSession == $g_id) {echo 'selected';} ?>><?php echo $title0; ?></option>
            <?php
								$i++;
							}   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Select Term:</td>
        <td  align="left" valign="middle">
        <select name="term_id" id="e2" style="width: 90%;" >
       		 <option value="0" <?php if($currentTerm == 0) {echo 'selected';} ?>>All Terms</option>
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title1 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($currentTerm == $g_id) {echo 'selected';} ?>><?php echo $title1; ?></option>
            <?php
								$i++;
								}   ?>
			</select>
        </td>
      </tr>

       <tr>
        <td  align="left" valign="middle">Use Scores in Cumulative Result:<small><br>Scores from this exam will be included while computing cumulative result for the selected session</small></td>
        <td  align="left" valign="middle">
        <select name="is_cumulative" id="is_cumulative" style="width: 90%;" >
       		 <option value="1" <?php if($is_cumulative == 1) {echo 'selected';} ?>>Yes </option>
             <option value="0" <?php if($is_cumulative == 0) {echo 'selected';} ?>>No</option>
		</select>
        </td>
      </tr>

      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;

        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="fee" value="<?php echo $edit; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Exam</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Create Exam</button>
        <?php } ?>
        <br><div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Creating Exam...</div>
      </td>
	</form>
      </tr>
    </table>

	</div>
</div>
<?php
}


//start initiation
if(isset($_GET['remark']))
{
	$date = date('Y-m-d');
	$amount = '';

	if($_GET['remark'] > 0) {
		$student_id = $_GET['mark'];
		$exam_id = $_GET['exam'];
		$subject_id = $_GET['subject'];

		$sql=$query="SELECT * FROM exam_student_score WHERE exam_id = '$exam_id' AND student_id = '$student_id' AND subject_id = '$subject_id' AND school_id = '$school_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$exam_score = $row['exam_score'];
		$assessment_1 = $row['assessment_1'];
		$assessment_2 = $row['assessment_2'];
		$assessment_3 = $row['assessment_3'];
		$assessment_4 = $row['assessment_4'];
		$assessment_5 = $row['assessment_5'];
		$id = $row['id'];
	}
?>
<div id="add-new">
   <div id="add-new-head">Remark Exam
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/exam" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>

       <tr>
        <td  align="left" valign="middle">Student:</td>
        <td  align="left" valign="middle">
        <select name="student_id" id="e1" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM students WHERE id = '$student_id'";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title0 = $row['first_name']. ' '.$row['last_name']. ' '.$row['other_name'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($student_id == $g_id) { echo 'selected'; } ?>><?php echo $title0; ?></option>
            <?php
					 			$i++;
							}   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Exam:</td>
        <td  align="left" valign="middle">
        <select name="fee_id" id="e2" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM exams WHERE id = '$exam_id' ";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

					while($row = mysqli_fetch_assoc($result)){
		                $g_id = $row['id'];
		                $title1 = $row['title'];
		            ?>
		               <option value="<?php echo $g_id; ?>" <?php if($fee_id == $g_id) { echo 'selected'; } ?>><?php echo $title1; ?></option>
            <?php	}   ?>
			</select>
        </td>
      </tr>
      
 	  <tr>
        <td align="left" valign="middle"><?=getSetting('assessOneName')?>:</td>
        <td  align="left" valign="middle">
        	<input type="number" name="assessment_1" required="required"  value="<?php echo $assessment_1;?>">
      </td>
      </tr>
      <?php if(getSetting('num_assignment')>1) { ?>
      <tr>
        <td align="left" valign="middle"><?=getSetting('assessTwoName')?>:</td>
        <td  align="left" valign="middle">
        	<input type="number" name="assessment_2" required="required"  value="<?php echo $assessment_2;?>">
      </td>
      </tr>
     <?php } if(getSetting('num_assignment')>2) { ?> 
      <tr>
        <td align="left" valign="middle"><?=getSetting('assessThreeName')?>:</td>
        <td  align="left" valign="middle">
        	<input type="number" name="assessment_3" required="required"  value="<?php echo $assessment_3;?>">
      </td>
      </tr>
	<?php } if(getSetting('num_assignment')>3) { ?> 
      <tr>
        <td align="left" valign="middle"><?=getSetting('assessFourName')?>:</td>
        <td  align="left" valign="middle">
        	<input type="number" name="assessment_4" required="required"  value="<?php echo $assessment_4;?>">
      </td>
      </tr>
	<?php } if(getSetting('num_assignment')>4) { ?> 
      <tr>
        <td align="left" valign="middle"><?=getSetting('assessFiveName')?>:</td>
        <td  align="left" valign="middle">
        	<input type="number" name="assessment_5" required="required"  value="<?php echo $assessment_5;?>">
      </td>
      </tr>
	<?php } ?>
 	  <tr>
        <td align="left" valign="middle">Exam Score:</td>
        <td  align="left" valign="middle">
        	<input type="number" name="exam_score" id="exam_score" required="required"  value="<?php echo $exam_score; ?>">
      </td>
      </tr>

      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;
        	<input type="hidden" name="update_exam" value="<?php echo $id; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Score</button>
        <div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Processing ...</div>
      </td>
      <td>
	</form>
      </tr>
    </table>

	</div>
</div>
<?php
}


if(isset($_POST['marked'])){
	foreach ($_POST as $key => $value ){
		$$key = mysqli_real_escape_string($server,$value);
	}

	$sql=$query="SELECT * FROM exam_student_score WHERE exam_id = '$exam_id' AND subject_id = '$subject_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	/*if($num > 0) {
    $message = 'Sorry but you have already recorded marks for the selected exam and subject!';
	$class = 'yellow';
	} else {*/
	foreach($_POST['exam_score'] as $student_id => $exam_score){
		$session_id = examSession($exam_id);
		$class = getClass($student_id,$session_id);
		$assessment_1 = mysqli_real_escape_string($server,$_POST['assessment_1'][$student_id]);
		$assessment_2 = mysqli_real_escape_string($server,$_POST['assessment_2'][$student_id]);
		$assessment_3 = mysqli_real_escape_string($server,$_POST['assessment_3'][$student_id]);
		$assessment_4 = mysqli_real_escape_string($server,$_POST['assessment_4'][$student_id]);
		$assessment_5 = mysqli_real_escape_string($server,$_POST['assessment_5'][$student_id]);
		$assessment_score = $assessment_1+$assessment_2+$assessment_3;
		if(scoreExist($school_id,$subject_id,$exam_id,$student_id)) {
			//delete existing
			$sql=$query = "DELETE FROM exam_student_score WHERE school_id = '$school_id' AND subject_id = '$subject_id' AND exam_id = '$exam_id' AND student_id = '$student_id'";
			$result = mysqli_query($server, $query) or die(mysqli_error($server));

			//insert new record
			$sql=$query="INSERT INTO exam_student_score (`id`, `school_id`, `subject_id`, `exam_id`, `student_id`, `assessment_score`, `exam_score`,`session_id`, `class_id`, `assessment_1`, `assessment_2`, `assessment_3`, `assessment_4`, `assessment_5`)
		VALUES (NULL, '$school_id', '$subject_id', '$exam_id', '$student_id', '$assessment_score', '$exam_score', '$session_id','$class', '$assessment_1', '$assessment_2', '$assessment_3', '$assessment_4', '$assessment_5');";

			mysqli_query($server, $query) or die(mysqli_error($server));
		} else {
			$sql=$query="INSERT INTO exam_student_score (`id`, `school_id`, `subject_id`, `exam_id`, `student_id`, `assessment_score`, `exam_score`,`session_id`, `class_id`, `assessment_1`, `assessment_2`, `assessment_3`, `assessment_4`, `assessment_5`)
			VALUES (NULL, '$school_id', '$subject_id', '$exam_id', '$student_id', '$assessment_score', '$exam_score', '$session_id', '$class', '$assessment_1', '$assessment_2', '$assessment_3', '$assessment_4', '$assessment_5');";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}
	$message = 'The exam marks for selected subject was succesfully added. Note that any duplicate records will be over-written.<br>Select a different subject to keep marking';
	$class = 'green';
}

if(isset($_GET['mark_class'])){

	$exam = filterinp($_REQUEST['exam_id']);
	$sql2=$query2="SELECT * FROM exams WHERE id = '$exam'";
	$result2 = mysqli_query($server, $query2) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result2);
	$session = $row['session_id'];

	$school = $school_id;

?>
<div id="add-new">
   <div id="add-new-head"> Mark <?php echo examName($exam); ?>

       <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>

   <div class="inside" style="background-color: #fff;">

    <form method="get" action="admin/exam" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td width="30%" align="left" valign="middle">Select Class:</td>
        <td align="left" width="70%" valign="middle">
        <select name="class_id" id="e1" style="width: 90%" >
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
			               <option  value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
			            <?php
								}  ?>
			</select>
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td width="30%" align="left" valign="top">
        <input type="hidden" name="record_mark" value="yes" />
        <input type="hidden" name="session_id" value="<?php echo $session; ?>" />
        <input type="hidden" name="exam_id" value="<?php echo $exam; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Start Marking</button>
	</form>
        <td align="left" valign="top">&nbsp;</td>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Initiating...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}



if(isset($_GET['record_mark'])) {

	$exam = mysqli_real_escape_string($server,$_REQUEST['exam_id']);
	$class = $_REQUEST['class_id'];
	$session = $_REQUEST['session_id'];
	$school = $school_id;

?>
<div id="add-new">
   <div id="add-new-head">Mark <?php echo examName($exam); ?>

       <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>

   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/exam?record_mark&class_id=<?=$class?>&session_id=<?=$session?>" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td width="40%" align="left" valign="middle">Subject:</td>
        <td align="left" colspan="2" valign="middle">
        <select name="subject_id" id="e1" class="eam_subsj" style="width: 90%" >
		<?php
		     $sqlC=$queryC="SELECT * FROM subject WHERE school_id = '$school_id' ORDER BY title ASC";
			if(userRole($userID) == 4) {
				$class_id = getTeacherClass(userProfile($userID));
				$sql=$queryC="SELECT * FROM subject WHERE class_id = '$class_id' ORDER BY title ASC";
			}
            $resultC = mysqli_query($server, $queryC);
            $numC = mysqli_num_rows($resultC);

			while($row = mysqli_fetch_assoc($resultC)){
            $c_id = $row['id'];
            $title = $row['title'].' ('.className($row['class_id']).')';
            ?>
               <option  value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php	}  ?>
			</select>
        </td>
      </tr>
	<tr>
     <td align="left" colspan="3" valign="middle"><br /></td>
    </tr>
    <tr>
        <td width="40%" align="left" valign="middle">Student</td>
        <td align="left" valign="middle"><?=getSetting('assessOneName')?></td>
		<?php if(getSetting('num_assignment')>1) { ?>
        <td align="left" valign="middle"><?=getSetting('assessTwoName')?></td>
        <?php } if(getSetting('num_assignment')>2) { ?>
        <td align="left" valign="middle"><?=getSetting('assessThreeName')?></td>
        <?php }if(getSetting('num_assignment')>3) { ?>
        <td align="left" valign="middle"><?=getSetting('assessFourName')?></td>
        <?php }if(getSetting('num_assignment')>4) { ?>
        <td align="left" valign="middle"><?=getSetting('assessFiveName')?></td>
        <?php } ?>
        <td align="left" valign="middle">Exam Score</td>
    </tr>
    <div id="eampartis"></div>  
   <?php
   $sql=$query="SELECT * FROM student_class WHERE class_id = '$class' AND session_id = '$session'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	 while($row = mysqli_fetch_assoc($result)){		 ?>
   		<tr>
        <td align="left" valign="middle"><?php echo studentName($row['student_id']); ?></td>
        <td align="left" valign="middle">
        <input type="number" name="assessment_1[<?php echo $row['student_id']; ?>]" required="required"  value="0">
        </td>
        <?php if(getSetting('num_assignment')>1) { ?>
        <td align="left" valign="middle">
        <input type="number" name="assessment_2[<?php echo $row['student_id']; ?>]" required="required"  value="0">
        </td>
        <?php } if(getSetting('num_assignment')>2) { ?>
        <td align="left" valign="middle">
        <input type="number" name="assessment_3[<?php echo $row['student_id']; ?>]" required="required"  value="0">
        </td>
        <?php } if(getSetting('num_assignment')>3) { ?>
        <td align="left" valign="middle">
        <input type="number" name="assessment_4[<?php echo $row['student_id']; ?>]" required="required"  value="0">
        </td>
        <?php } if(getSetting('num_assignment')>4) { ?>
        <td align="left" valign="middle">
        <input type="number" name="assessment_5[<?php echo $row['student_id']; ?>]" required="required"  value="0">
        </td>
        <?php } ?>
        <td align="left" valign="middle">
        <input type="number" name="exam_score[<?php echo $row['student_id']; ?>]" id="exam_score[<?php echo $row['id']; ?>]" required="required"  value="0">
        </td>
    	</tr>
        <?php	} 	?>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" colspan="3" valign="top">&nbsp;</td>
        <input type="hidden" name="marked" value="yes" />
        <input type="hidden" name="school_id" value="<?php echo $school; ?>" />
        <input type="hidden" name="exam_id" value="<?php echo $exam; ?>" />
        <input type="hidden" id="exam_class_id" value="<?php echo $class; ?>" />
        <input type="hidden" id="exam_session_id" value="<?php echo $session; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Submit Mark</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Marks...</div>
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
			$sql=$query = "INSERT INTO exams (`id`, `school_id`, `session_id`, `term_id`, `title`, `is_cumulative`)
			VALUES (NULL, '$school_id', '$session_id', '$term_id', '$title', '$is_cumulative');";
			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new exam was succesfully created.';
	$class = 'green';

}


if(isset($_POST['update_exam']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
			$sql=$query="UPDATE `exam_student_score` SET `assessment_score` =  '$assessment_score' WHERE `id` = '$update_exam';";
			mysqli_query($server, $query) or die(mysqli_error($server));
			$sql=$query="UPDATE `exam_student_score` SET `exam_score` =  '$exam_score' WHERE `id` = '$update_exam';";
			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'Exam score succesfully updated.';
	$class = 'green';
}


if(isset($_POST['save']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

		//create new prents
			$sql=$query="UPDATE `exams` SET `is_cumulative` =  '$is_cumulative' WHERE `id` = '$fee';";
			mysqli_query($server, $query) or die(mysqli_error($server));
			$sql=$query="UPDATE `exams` SET `session_id` =  '$session_id' WHERE `id` = '$fee';";
			mysqli_query($server, $query) or die(mysqli_error($server));
			$sql=$query="UPDATE `exams` SET `term_id` =  '$term_id' WHERE `id` = '$fee';";
			mysqli_query($server, $query) or die(mysqli_error($server));
			$sql=$query="UPDATE `exams` SET `title` =  '$title' WHERE `id` = '$fee';";
			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The selected exam was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword']))
{

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
	         $clauses[] = "title LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "f.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM exams WHERE school_id = '$school_id' AND $filter";

 	$resultF = mysqli_query($server, $query) or die(mysqli_error($server));
	$numF = mysqli_num_rows($resultF);

	if($numF < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM exams WHERE school_id = '$school_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";

	$resultF = mysqli_query($server, $query);
	$numF = mysqli_num_rows($resultF);
	if($numF < "1")
		{
		$message = "There are no exams created for your school";
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

        <input type="search" name="keyword" placeholder="Search Exams" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/exam?new"><button type="button" class="submit">Add <hide>Exam</hide></button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="25%" scope="col">Title</th>
                <th width="19%" scope="col">Session</th>
                <th width="15%" scope="col">Term</th>
                <th width="20%" scope="col">Action</th>
              </tr>
               <?php

				while($row=mysqli_fetch_assoc($resultF)){
					$id = $row['id'];
					$session = $row['session_id'];
					$term = $row['term_id'];
					$title = $row['title'];


				?>
              <tr class="inner">
                <td width=""> <?php echo $title; ?></td>
                <td width=""> <?php echo sessionName($session); ?></td>
                <td width=""> <?php echo termName($term); ?></td>
                <td width="" valign="middle">
                <?php if(!examInUse($id)) { ?>
                <a href="admin/exam?mark_class&exam_id=<?php echo $id;?>"><button>Mark  </button></a>
                <?php } else { ?>
                <a href="admin/exam?mark_class&exam_id=<?php echo $id;?>"><button>Mark  </button></a>

                <a href="admin/reportcard?report=1&exam_id=<?php echo $id;?>&session_id=<?php echo $session;?>&term_id=<?php echo $term;?>"><button class="btn-success">View Report </button></a>
				<?php } ?>
				<?php if(userRole($userID)<3) { ?>
                <a href="admin/exam?edit=<?php echo $id;?>"><button class="btn-warning">Edit </button></a>
                <?php
				if(!examInUse($id)) { ?>
                <a href="admin/exam?delete=<?php echo $id;?>"><button class="btn-danger">Delete</button></a>
                <?php } } ?>
                </td>
              </tr>
              <?php
			} ?>
              </table>

<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
</div>
