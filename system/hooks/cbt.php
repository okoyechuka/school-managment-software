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

$currentSession = getSetting('current_session');
$currentTerm = getSetting('current_term');

if(userRole($userID) > 3 && userRole($userID) != 4) {
header('location: adashboard');
}
$addedT = "";
if(userRole($userID) == 4) {
	$teachers_id = userProfile($userID);
	$addedT .= " AND teacher_id = '$teachers_id'";
}
if(isset($_REQUEST['delete'])){
	$book = filterinp($_REQUEST['delete']);
	$sql=$query = "DELETE FROM cbt WHERE id = '$book' $addedT AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));

	$message = "The selected test was successfully deleted.";
	$class="green";
}

if(isset($_REQUEST['view']) && is_numeric($_REQUEST['view'])){
	$book = filterinp($_REQUEST['view']);
	$sql=$query="SELECT * FROM cbt WHERE id = '$book' $addedT AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head"><?php echo shorten($row['title'],65); ?>
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle"><?php echo @$row['title']; ?>     </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Start Date:</td>
        <td  align="left" valign="middle">  <?php echo @$row['start_date']; ?>     </td>
      </tr>
      <tr>
        <td align="left" valign="middle">End Date:</td>
        <td  align="left" valign="middle">     	<?php echo @$row['expire_date']; ?>     </td>
      </tr>

      <tr class="show_exam">
        <td align="left" valign="middle">Assigned Exam:</td>
        <td  align="left"><?=$row['exam_id']>0?sessionName(examSession($row['exam_id']))." ".termName(examTerm($row['exam_id'])):"Not Assigned" ?>     </td>
      </tr>
      
      <tr class="show_exam">
        <td align="left" valign="middle">Assigned Subject:</td>
        <td  align="left"><?=$row['subject_id']>0?subjectName($row['subject_id']).ucfirst($row['mark_type']):"Not Assigned" ?>     </td>
      </tr>
      
      <tr class="show_course">
        <td align="left" valign="middle">Assigned Course:</td>
        <td  align="left"><?=$row['course_id']>0?courseName($row['course_id']):"Not Assigned" ?>     </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Assigned Teacher:</td>
        <td  align="left"><?=$row['teacher_id']>0?teacherName($row['teacher_id']):"Not Assigned" ?>     </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">No. of Questions per Test:</td>
        <td  align="left" valign="middle"><?php echo @$row['question_limit']; ?> Questions per test       </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">Pass Mark (%):</td>
        <td  align="left" valign="middle">      	<?php echo @$row['pass_mark']; ?> Percent      </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">Test Duration:</td>
        <td  align="left" valign="middle">     	<?php echo gmdate('H:i:s',$row['time_duration']); ?> (HH:MM:SS)     </td>
      </tr>
            
      <tr>
        <td align="left" valign="middle">Should students be allowed to retake this test?:</td>
        <td><?=@$row['allow_repeat']=="0"?"No":'Yes'?>  </td>
      </tr>
      
      <tr>
        <td align="left" colspan="2" valign="top"><strong style="font-size:18px">Introduction</strong>:<br>
        <holdd><?php echo @$row['description']; ?></holdd></td>
      </tr>
      </table>
    </div>
</div>    
<?php 
} 

if(isset($_REQUEST['result']) && is_numeric($_REQUEST['result'])){
	$book = filterinp($_REQUEST['result']);
	$sql=$query="SELECT * FROM cbt WHERE id = '$book' $addedT AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head"><?php echo shorten($row['title'],65); ?>
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle"><?php echo @$row['title']; ?>     </td>
      </tr>
      <tr class="show_exam">
        <td align="left" valign="middle">Assigned Exam:</td>
        <td  align="left"><?=$row['exam_id']>0?sessionName(examSession($row['exam_id']))." ".termName(examTerm($row['exam_id'])):"Not Assigned" ?>     </td>
      </tr>      
      <tr class="show_course">
        <td align="left" valign="middle">Assigned Course:</td>
        <td  align="left"><?=$row['course_id']>0?courseName($row['course_id']):"Not Assigned" ?>     </td>
      </tr>      
      <tr>
        <td align="left" valign="middle">No. of Questions per Test:</td>
        <td  align="left" valign="middle"><?php echo @$row['question_limit']; ?> Questions per test       </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">Pass Mark (%):</td>
        <td  align="left" valign="middle"><?php echo @$row['pass_mark']; ?> Percent      </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">Test Duration:</td>
        <td  align="left" valign="middle"> <?php echo gmdate('H:i:s',$row['time_duration']); ?> (HH:MM:SS)      </td>
      </tr>
                  
      <tr>
      <?php
		$result3T = mysqli_query($server,"SELECT DISTINCT student_id FROM cbt_answers WHERE cbt_id = '$book' ORDER BY id desc");	$num = mysqli_num_rows($result3T);
	?>	
    <?php if($num < 1) { ?>
    	<div class="alert alert-warning">No student has completed this test yet. Please check back some other time</div
    ><?php } ?>		
        <td align="left" colspan="2" valign="top"><strong style="font-size:18px">Students Report</strong>:<br>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left" valign="middle"><strong>Student</strong></td>
                <td align="left" valign="middle"><strong>Finish Time</strong></td>
                <td align="left" valign="middle"><strong>Score</strong></td>
              </tr>
              <?php
				while($rowRest = mysqli_fetch_assoc($result3T)){
				$student = $rowRest['student_id'];
			  	$ratio = round(countCorrectAnswered($student,$book)/$row['question_limit'],2);
				$scorePerc = ($ratio*100);
				$passed = "<red>Failed</red>";if($scorePerc >=$row['pass_mark']) $passed = "<green>Passed</gree>";
			  ?>
              <tr class="inner">
              	<td align="left" valign="middle"><?=studentName($student)?></td>
                <td align="left" valign="middle"><?=gmdate('H:i:s',cbtStudentTiming($student,$book))?>:</td>
                <td align="left" valign="middle"><?=$scorePerc?>% (<?=$passed?>)</td>
              </tr>
              <?php } ?>
            </table>  
        </td>
      </tr>
      </table>
    </div>
</div>    
<?php 
} 

if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])){
	$row = array();
	if(isset($_POST['add'])){
		$row = $_POST;
	}
	if(isset($_REQUEST['edit'])) {
		$book = filterinp($_REQUEST['edit']);
		$query="SELECT * FROM cbt WHERE id = '$book' $addedT AND school_id = '$school_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['title']; ?>
<?php	} else {
		$book = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Test
<?php
	}
?>
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
    <form method="post" action="admin/cbt" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" required="required" placeholder="" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Start Date:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="start_date" placeholder="YYYY-MM-DD" value="<?php echo @$row['start_date']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">End Date:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="expire_date" placeholder="YYYY-MM-DD" value="<?php echo @$row['expire_date']; ?>">
        </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">Link Text To:</td>
        <td>
        	<select name="linkto" id="linkto" style="width: 90%;" >
        		<option <?=@$row['exam_id']>0?"selected":''?> value="exam">Exam</option>
				<option <?=@$row['course_id']>0?"selected":''?> value="course">Online Course</option>
			</select>
        </td>
      </tr>
      
      <tr class="show_exam">
        <td align="left" valign="middle">Assign To Exam:</td>
        <td>
        <select name="exam_id" id="e3" class="required_on_exam" style="width: 90%;" >
			<?php
                $query="SELECT * FROM exams WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
				while($rows = mysqli_fetch_assoc($result)){
                $g_id = $rows['id']; $title1 = $rows['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$row['exam_id']==$g_id) { echo 'selected'; } ?>><?php echo $title1; ?></option>
            <?php }   ?>
			</select>
        </td>
      </tr>
      
      <tr class="show_exam">
        <td align="left" valign="middle">Assign To Subject:</td>
        <td>
        <select name="subject_id" id="e2" class="required_on_exam" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM subject WHERE school_id = '$school_id' $addedT ORDER BY title ASC";
                $result = mysqli_query($server, $query);
				while($rows = mysqli_fetch_assoc($result)){
                $c_id = $rows['id'];
                $class = className($rows['class_id']);
				$title = $class.' - '.$rows['title'];
            ?>
               <option <?php if($row['subject_id'] == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
               <?php } ?>
			</select>
        </td>
      </tr>
      
      <tr class="show_exam">
        <td align="left" valign="middle">Assign Score To:</td>
        <td>
        	<select name="mark_type" id="exORass" class="required_on_exam" style="width: 90%;" >
				<option <?=@$row['mark_type']=="exam"?"selected":''?> value="exam">Main Exam Score</option>
					<option <?=@$row['mark_type']=="assessment_1"?"selected":''?> value="assessment_1"><?=getSetting('assessOneName')?></option>
                    <?php if(getSetting('num_assignment')>1) { ?>
                    <option <?=@$row['mark_type']=="assessment_2"?"selected":''?> value="assessment_2"><?=getSetting('assessTwoName')?></option>
                    <?php } if(getSetting('num_assignment')>2) { ?>
                    <option <?=@$row['mark_type']=="assessment_3"?"selected":''?> value="assessment_3"><?=getSetting('assessThreeName')?></option>
                    <?php } if(getSetting('num_assignment')>3) { ?>
                    <option <?=@$row['mark_type']=="assessment_4"?"selected":''?> value="assessment_4"><?=getSetting('assessFourName')?></option>
                    <?php } if(getSetting('num_assignment')>4) { ?>
                    <option <?=@$row['mark_type']=="assessment_5"?"selected":''?> value="assessment_5"><?=getSetting('assessFiveName')?></option>
                    <?php } ?>                
			</select>
        </td>
      </tr>
      
      <tr class="show_exam">
        <td align="left" valign="middle">Exam Score Contribution (%):</td>
        <td  align="left" valign="middle">
        	<input type="number" class="required_on_exam"  name="accessment_id" placeholder="What percentage of exam score will this test contribute?" value="<?php echo @$row['accessment_id']; ?>">
        </td>
      </tr>
      
      <tr class="show_course">
        <td align="left" valign="middle">Assign To Course:</td>
        <td>
        <select name="course_id" id="e3" class="required_on_course" style="width: 90%;" >
			<?php
                $query="SELECT * FROM e_courses WHERE school_id = '$school_id' $addedT ORDER BY title ASC";
                $result = mysqli_query($server, $query);
				while($rows = mysqli_fetch_assoc($result)){
                $g_id = $rows['id']; $title1 = $rows['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$row['course_id']==$g_id) { echo 'selected'; } ?>><?php echo $title1; ?></option>
            <?php }   ?>
			</select>
        </td>
      </tr>
      
      <?php if(userRole($userID) == 4) {
	$teachers_id = userProfile($userID);?>
    <input type="hidden" name="teacher_id" value="<?=$teachers_id?>" >
    <?php } else {?>
      <tr>
        <td align="left" valign="middle">Assign Teacher:</td>
        <td>
        <select name="teacher_id" id="e2r" style="width: 90%;" >
        	<option value="0">Unassigned</option>
			<?php
                $query="SELECT * FROM teachers WHERE school_id = '$school_id' ORDER BY first_name ASC";
                $result = mysqli_query($server, $query);
				while($rows = mysqli_fetch_assoc($result)){
                	$c_id = $rows['id']; $titles = teacherName($rows['id']);
            ?>
               <option <?php if($row['teacher_id']==$c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $titles; ?></option>
            <?php }   ?>
			</select>
        </td>
      </tr>
      <?php } ?>
      
      <tr>
        <td align="left" valign="middle">No. of Questions per Test:</td>
        <td  align="left" valign="middle">
        	<input type="number" required name="question_limit" min="1" placeholder="Questions will be served in random order" value="<?php echo @$row['question_limit']; ?>">
        </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">Pass Mark (%):</td>
        <td  align="left" valign="middle">
        	<input type="number" required max="100" min="1" name="pass_mark" placeholder="Pass mark in percentatge of test questions" value="<?php echo @$row['pass_mark']; ?>">
        </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">Test Duration (in seconds):</td>
        <td  align="left" valign="middle">
        	<input type="number" required name="time_duration" min="30" placeholder="In Seconds" value="<?php echo @$row['time_duration']; ?>">
        </td>
      </tr>
            
      <tr>
        <td align="left" valign="middle">Should students be allowed to retake this test?:</td>
      <td>
        	<select name="allow_repeat" id="e1r" style="width: 90%;" >
        		<option <?=@$row['allow_repeat']=="0"?"selected":''?> value="0">No</option>
				<option <?=@$row['allow_repeat']=="1"?"selected":''?> value="1">Yes</option>
			</select>
        </td>
      </tr>
      
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue>Introduction Text:</td>
      </tr>
      <tr>
        <td align="left" valign="top" colspan="2">
        	<textarea placeholder="Type an some opeing instructions here here" id="textMessage" class="ckeditor" name="description"  style="height: 200px; width:98%;" required ><?php echo @$row['description']; ?>&nbsp;</textarea>
      </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="class" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Test</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Test</button>
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
	$error = 0;
	$description = filterinp($_POST['description'],true);
	$exam = $subject = $mark = $course = "";
	if($linkto =="exam") {
		$exam = $exam_id ;
		$subject = $subject_id;
		$mark = $mark_type;
		if($exam < 1) {
			$message = 'Please select an Exam to assign';$class = 'red';$error = 1;
		}
		if($subject < 1) {
			$message = 'Please select a Subject to assign';$class = 'red';$error = 1;
		}
	} else {
		$course = $course_id;
		if($course < 1) {
			$message = 'Please select an Online Course to assign';$class = 'red';$error = 1;
		}
	}
	if($error < 1) {
		//create new prents
		$datetime = date('Y-m-d H:i:s');
		$query ="INSERT INTO cbt (`title`, `datetime`, `description`, `question_limit`, `pass_mark`, `time_duration`, `allow_repeat`, `teacher_id`, `exam_id`, `subject_id`, `course_id`, `start_date`, `expire_date`, `school_id`, `mark_type`, `accessment_id`) VALUES ('$title', '$datetime', '$description', '$question_limit', '$pass_mark', '$time_duration', '$allow_repeat', '$teacher_id', '$exam', '$subject', '$course', '$start_date', '$expire_date', '$school_id', '$mark', '$accessment_id');";
		mysqli_query($server, $query) or die(mysqli_error($server));
		$cbt_id = mysqli_insert_id($server);
		//insert class
		$message = 'The new online test was succesfully created. <a href="admin/cptquestion?cbt='.$cbt_id.'">Click Here</a> to manage leasons now';
		$class = 'green';
	}
}

if(isset($_POST['save'])){
	$error = 0;
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$description = filterinp($_POST['description'],true);	
	$exam = $subject = $mark = $course = "";
	if($linkto =="exam") {
		$exam = $exam_id ;
		$subject = $subject_id;
		$mark = $mark_type;
		if($exam < 1) {
			$message = 'Please select an Exam to assign';$class = 'red'; $error = 1;
		}
		if($subject < 1) {
			$message = 'Please select a Subject to assign';$class = 'red';$error = 1;
		}
		if($accessment_id < 1) {
			$message = 'Please enter a value for Exam Score Contribution (%)';$class = 'red';$error = 1;
		}
	} else {
		$course = $course_id;
		if($course < 1) {
			$message = 'Please select an Online Course to assign';$class = 'red';$error = 1;
		}
	}
	if($error < 1) {
		$query="UPDATE `cbt` SET 
		`title` =  '$title' ,
		`description` =  '$description' ,
		`question_limit` =  '$question_limit' ,
		`pass_mark` =  '$pass_mark' ,
		`time_duration` =  '$time_duration' ,
		`allow_repeat` =  '$allow_repeat' ,
		`teacher_id` =  '$teacher_id' ,
		`exam_id` =  '$exam_id' ,
		`subject_id` =  '$subject_id' ,
		`course_id` =  '$course_id' ,
		`start_date` =  '$start_date' ,
		`expire_date` =  '$expire_date' ,
		`mark_type` =  '$mark_type' ,
		`accessment_id` = '$accessment_id'
		WHERE `id` = '$class';";
		mysqli_query($server, $query) or die(mysqli_error($server));	
		$message = 'The selected online test was succesfully updated.';
		$class = 'green';
	}
}

if(isset($_GET['keyword'])){
	$category = filterinp($_GET['category']);
	$school_id = $school_id;
	$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', $_GET['keyword']);
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))	    {
	         $clauses[] = "title LIKE '%$term%' OR description LIKE '%$term%'";
	    }	    else	    {
	         $clauses[] = "title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM cbt WHERE school_id = '$school_id' $addedT AND $filter ";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "SELECT * FROM cbt WHERE school_id = '$school_id' $addedT ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")		{
		$message = "There are currently no online tests created for your school";
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
        <input type="search" name="keyword" placeholder="Search Test" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <a href="admin/cbt?new"><button type="button" class="submit">Add <hide>New</hide></button></a>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="5%" scope="col">ID</th>
                <th width="25%" scope="col">Title</th>
                <th width="10%" scope="col">Created On</th>
                <th width="25%" scope="col">Assigned To</th>
                <th width="10%" scope="col">Questions</th>
                <th width="30%" scope="col">Action</th>
              </tr>
               <?php $i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$assigned = "Not Available";
					if($row['exam_id'] > 0) {
						$assigned = sessionName(examSession($row['exam_id']))." ".termName(examTerm($row['exam_id'])).' '.subjectName($row['subject_id']).' '.ucfirst($row['mark_type']);
					}
					if($row['course_id'] > 0) {
						$assigned = "<strong>Course</strong>: ".courseName($row['course_id']);
					}
				?>
              <tr class="inner">
                <td> <?php echo $id; ?></td>
                <td> <?php echo $row['title']; ?></td>
                <td> <?php echo $row['datetime']?></td>
                <td> <?php echo $assigned; ?></td>
                <td> <?php echo countCBTQuestions($row['id'])?></td>
                <td valign="middle">
                <a href="admin/cbt?view=<?php echo $id;?>"><button>View Details</button></a>
                <a href="admin/cbtquestions?cbt=<?php echo $id;?>"><button class="success">Questions</button></a>
                <a href="admin/cbt?result=<?php echo $id;?>"><button class="info">Report</button></a>
                <a href="admin/cbt?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/cbt?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
                </td>
              </tr>
              <?php	$i++; } ?>
              </table>
<?php displayPagination($setLimit,$page,$query) ?>
        </div>
    </div>
</div>
