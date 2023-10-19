<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		reportcard.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			3/02/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

?>

<?php
$numP = 0;
$student = 0;
if(userRole($userID) > 3 && userRole($userID) != 4) {
header('location: admin.php');
}

	$session = getSetting('current_session');
	$term = getSetting('current_term');
	$class="0";

if (getSetting('current_session') < 1) {
$message = 'You have not defined the current accademic session yet!. <br>You must fix this before you can manage fees. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/generalsetting" title="Difine Active Session">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
}

if(isset($_GET['addnote'])) {
	$session=@mysqli_real_escape_string($server,$_GET['session_id']);
	$exam=@mysqli_real_escape_string($server,$_GET['exam_id']);
	$student=@mysqli_real_escape_string($server,$_GET['student_id']);
	if($session<1) $session = getSetting('current_session');
	if($_GET['upnot'] > 0) {
		$sql=$query="SELECT * FROM exam_note WHERE student_id = '$student' AND exam_id = '$exam' AND seasion_id = '$session'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$notes = $row['notes'];
	}
?>
<div id="add-new">
   <div id="add-new-head">Add Comments to Report Card
      <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="" enctype="multipart/form-data">
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
                $sql=$query="SELECT * FROM students WHERE school_id = '$school_id'";
				if(userRole($userID) == 4) {
					$class_id = getTeacherClass(userProfile($userID));
					$sql=$query="SELECT s.* FROM students s JOIN student_class sc ON s.id = sc.student_id WHERE sc.class_id = '$class_id' AND sc.session_id = '$session' ORDER BY s.first_name ASC";
				}
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title0 = $row['first_name']. ' '.$row['last_name']. ' '.$row['other_name'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($_GET['student_id']==$g_id) { echo 'selected'; } ?>><?php echo $title0; ?></option>
            <?php }   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Exam:</td>
        <td  align="left" valign="middle">
        <select name="exam_id" id="e2" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM exams WHERE school_id = '$school_id'";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);
				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title1 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($exam==$g_id) { echo 'selected'; } ?>><?php echo $title1; ?></option>
            <?php
								}   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Session:</td>
        <td  align="left" valign="middle">
        <select name="session_id" id="e2" style="width: 90%;" >
			<?php
		     $sqlC=$queryC="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY id DESC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

				while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $titleF = $row['title'];
            ?>
               <option value="<?php echo $c_id; ?>" <?php  if($session==$c_id) {echo 'selected';} ?>><?php echo $titleF; ?></option>
            <?php	}  ?>
			</select>
        </td>
      </tr>

 	  <tr>
        <td align="left" valign="middle">Comments:</td>
        <td  align="left" valign="middle">
        	<textarea id="message"  name="note"  style="height: 300px;" required ><?=@$notes?></textarea>
      </td>
      </tr>

      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="add_note" value="1" type="submit">Add Comments</button>
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

if(isset($_POST['add_note'])){
	foreach ($_POST as $key => $value ){
		$$key = mysqli_real_escape_string($server,$value);
	}
	$notes = nl2br($notes);
	$sql=$query="DELETE FROM exam_note WHERE student_id = '$student_id' AND exam_id = '$exam_id' AND seasion_id = '$session_id'";
	mysqli_query($server, $query);
	$sql=$query="INSERT INTO exam_note (`student_id`, `seasion_id`, `exam_id`, `notes`) VALUES ('$student_id', '$session_id', '$exam_id', '$note')";
	mysqli_query($server, $query) or die (mysqli_error($server));

	$_SESSION['message'] = 'Your comments was successfully added to the selected student report.';
	$_SESSION['color'] = 'green';
	$stuClass = getClass($student_id,$session_id);
	header("location: reportcard?done&report=3&exam_id=$exam_id&session_id=$session_id&class_id=$stuClass&student_id=$student_id");
}
if(isset($_GET['exam_id']) | isset($_GET['view']))
{
$exam = filterinp($_GET['exam_id']);
$session = filterinp($_GET['session_id']);
$class = filterinp($_GET['class_id']);
$subject = filterinp(@$_GET['subject_id']);
$student = filterinp($_GET['student_id']);

if(isset($_GET['view'])) {
$exam = lastExam(mysqli_real_escape_string($server,$_GET['view']));
$session = $currentSession;
$class = getClass($student,$currentSession);
$subject = 0;
$student = filterinp($_GET['view']);
} else {
		$not_started = 1;	
}

if(!isset($_GET['view']) && (@$_GET['report'] < 3 || !isset($_GET['report']))) {
	$student = 0;
}
if(@$student > 0) {
	$sql=$query = "select distinct e.* FROM exam_student_score e WHERE e.exam_id = '$exam' AND e.student_id = '$student' GROUP BY e.student_id, e.subject_id ORDER BY e.subject_id ASC";
	$report_title = studentName($student).' - '.examName($exam).' Report';
	if($exam < 1) {
		$sql=$query = "select distinct e.* FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id  WHERE e.session_id = '$session' AND e.student_id = '$student' AND ex.is_cumulative = 1  GROUP BY e.student_id, e.subject_id ORDER BY e.subject_id ASC";
	$report_title = studentName($student).' - Cumulative Report';
	}
} else {
	$sql=$query = "select distinct e.* FROM exam_student_score e  WHERE e.school_id = '$school_id' AND (e.class_id = '$class' OR e.class_id = 0) AND e.session_id = '$session' AND e.exam_id = '$exam' GROUP BY e.student_id ORDER BY e.id ASC";
	$report_title = className($class).' - '.examName($exam).' Report';
	if($exam < 1) {
		$sql=$query = "select distinct e.* FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.school_id = '$school_id' AND (e.class_id = '$class' OR e.class_id = 0) AND e.session_id = '$session' AND ex.is_cumulative = 1 GROUP BY e.student_id ORDER BY e.id ASC";
		$report_title = className($class).' - Cumulative Report';
	}
	if($subject > 0) {
		$sql=$query = "select distinct e.* FROM exam_student_score e WHERE e.school_id = '$school_id' AND (e.class_id = '$class' OR e.class_id = 0) AND e.subject_id = '$subject' AND e.session_id = '$session' AND e.exam_id = '$exam' GROUP BY e.student_id ";
		$report_title = className($class).' '.subjectName($subject).' Report for '.examName($exam);
		if($exam < 1) {
			$sql=$query = "select distinct e.* FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.school_id = '$school_id' AND (e.class_id = '$class' OR e.class_id = 0) AND e.subject_id = '$subject' AND e.session_id = '$session' AND ex.is_cumulative = 1 GROUP BY e.student_id ORDER BY e.subject_id ASC";
			$report_title = className($class).' - '.subjectName($subject).' - Cumulative Report';
		}
	}
}
 	$resultP = mysqli_query($server, $query) or die(mysqli_error($server));
	$numP = mysqli_num_rows($resultP);


	if($numP < 1){
		$message = "No exam records found for your selections! Please try another search.";
		$class="blue";
		}
} else {
		$message = "Select desired report type to view exam report";
		$class="blue";
}
if(isset($_GET['done'])) {
	$message = 	$_SESSION['message'];
	$class = $_SESSION['color'];

}
?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
           <select name="report" required id="report" >
				<option value="" disabled selected>Report Type</option>
                <option value="1">Class-wise Report</option>
	       		<option value="2">Subject-wise Report</option>
                <option <?php if(isset($_GET['view']) || $student >0) echo 'selected ';?>value="3">Student-wise Report</option>
			</select>
        &nbsp;
        <select name="session_id" id="session_se"  >
       		<option value="0">Select Session</option>
			<?php
			    $sqlC=$queryC="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY id DESC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);
				while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $titleF = $row['title'];
            ?>
               <option value="<?php echo $c_id; ?>"><?php echo $titleF; ?></option>
            <?php
			}  ?>
			</select>
        &nbsp;
           <select name="exam_id" required id="exam_sel"  >
				<option value="0">Select an Exam</option>
			</select>
        &nbsp;
        <select name="class_id" id="class_sel" >
			<?php
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

			while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option  value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php
								}  ?>
            <option value="" disabled selected>Select Class</option>
		</select>
		<span id="subspan" style="display: none;">
        &nbsp;
        <select name="subject_id" id="sel_sub" >
       		 <option value="0">All Subjects</option>
		</select>
        </span>
       <span id="stuspan" style="display: none;">
       &nbsp;
        <select name="student_id" id="sel_stu">
        <?php if(isset($_GET['view'])){ ?>
       		 <option value="<?php echo $_GET['view']; ?>"><?php echo studentName($_GET['view']); ?></option>
         <?php } ?>
             <option value="0">Select a Student</option>
		</select>
        </span>
		&nbsp;
        <button class="submit"><i class="fa fa-search"></i> </button>
        <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print</button></a>
        </form>
    </div>
<?php if($numP > 0 ) { ?>
	 <?php global $hooks;$hooks->do_action('ReportCardHeader'); ?>			
    <?php if(isset($_GET['exam_id'])) { ?>
	<div class="panel" style="/* [disabled]border-color: transparent; */">
    	<div class="panel-head"> &nbsp;<?php echo $report_title;?></div>
        <div id="print-this1" class="panel-body">
        <br /><div style="text-align: center">
        <img src="media/uploads/<?php echo getSetting('logo'); ?>" style="width: 40px;" /><br />
		<?php echo '<strong class="capitalize">'.getSetting('name').'</strong></left><br>';
		echo '<small>'.getSetting('address').', ';
		echo ''.getSetting('city').', '.getSetting('state').'</small><br>';
		echo '<small>Email: '.getSetting('email').'</small> &nbsp;';
		echo '<small>Tel: '.getSetting('phone1').' '.getSetting('phone2').'</small><br>';   ?>
        <hr /><h2 class="result"><?php echo sessionName($session).' - '.$report_title;?></h2>
        </div>

        <?php if($student > 0) {
		$term_id = examTerm($exam);

		$sql=$query="SELECT * FROM students WHERE id = '$student'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}
		$firstName = $row['first_name'];
		$lastName = $row['last_name'];
		$year = $row['year'];
		$id = $row['admission_number'];

		$perc = examPercentage($student,$exam);
		if($exam<1)
		$perc = sessionPercentage($student,$session);
		$tag1 = '<green>'; $tag2 = '</green>';
		if($perc < 40) {
			$tag1 = '<red>'; $tag2 = '</red>';
		}
		?>
        <!-- display students report card header -->
        <table width="100%" style="border-color: transparent; "border="0" cellspacing="0" cellpadding="0">
              <tr style="border-color: transparent; ">
              	<td style="border-color: transparent; " width="20%" valign="middle"><img src="media/uploads/<?php echo $picture; ?>" style="width: 100px; height: auto; border: 2px solid #999;"/><br /></td>
                <td width="35%" valign="top" style="font-size:15px; text-shadow:none; border-color: transparent;">
                Student's Name: <blue><?php echo $firstName.' '.$lastName; ?></blue><br />
                Admission Year: <blue><?php echo $year; ?></blue><br />
                Admission No.: <blue><?php echo $id; ?></blue><br />
                Class: <blue><?php echo className(getClass($student,$session)); ?></blue><br />
                Class Teacher: <blue><?php echo teacherName(getClassTeacher(getClass($student,$session))); ?></blue><br />
                </td>
                <td width="35%" valign="top" style="font-size:15px; text-shadow:none; border-color: transparent;">
                Session: <blue><?php echo sessionName($session); ?></blue><br />
                Term: <blue><?php echo termName($term); ?></blue><br />

               <?php if($exam>0) { ?>
                Average Score: <?php echo $tag1.round(examPercentage($student,$exam), 2).'%'.$tag2; ?><br />
                Class Rank: <blue><?php echo formatPosition(classRank($student,$exam)); ?></blue><br />
                <?php } else { ?>
                Cumulative Average: <?php echo $tag1.round(sessionPercentage($student,$session), 2).'%'.$tag2; ?><br />
                Class Rank: <blue><?php echo formatPosition(sessionClassRank($student,$session)); ?></blue><br />
                <?php } ?>

                </td>
              </tr>
              <tr><td colspan="3"></td></tr>

        </table>
        <?php } else { ?>
        	<?php if($_GET['subject_id'] > 0) { ?>
            <table width="100%" style="border-color: transparent; "border="0" cellspacing="0" cellpadding="0">
              <tr style="border-color: transparent; ">
                <td width="35%" valign="top" style="font-size:15px; text-shadow:none; border-color: transparent;">
                Teacher: <blue><?php echo teacherName(getSubjectTeacher($subject)); ?></blue><br />
                </td>
                <td width="35%" valign="top" style="font-size:15px; text-shadow:none; border-color: transparent;">
                <?php if($exam>0) { ?>
                Total Candidates: <blue><?php echo examSubjectCount($exam,$subject,$class); ?></blue><br />
                <?php } else { ?>
                Total Candidates: <blue><?php echo examSessionSubjectCount($session,$subject); ?></blue><br />
                <?php } ?>
                </td>
              </tr>
              <tr><td colspan="3"></td></tr>
        </table>
            <?php } else { ?>
            <table width="100%" style="border-color: transparent; "border="0" cellspacing="0" cellpadding="0">
              <tr style="border-color: transparent; ">
                <td width="35%" valign="top" style="font-size:15px; text-shadow:none; border-color: transparent;">
                Teacher: <blue><?php echo teacherName(getClassTeacher($class)); ?></blue><br />
                </td>

                <td width="35%" valign="top" style="font-size:15px; text-shadow:none; border-color: transparent;">
                <?php if($exam>0) { ?>
                Total Candidates: <blue><?php echo examClassCount($exam,$class); ?></blue><br />
                <?php } else { ?>
                Total Candidates: <blue><?php echo examSessionClassCount($session,$class); ?></blue><br />
                <?php } ?>
                </td>
              </tr>
              <tr><td colspan="3"></td></tr>
        </table>
            <?php } ?>

        <?php } ?>
 <?php
 //check if cumulative
 if($exam > 0 ) {
	 $not_started=0;
 ?>

        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="resultsh">
                <?php if($student > 0) { ?>
                <th width="25%" scope="col">Subject</th>
                    <th width="" class="rotate" scope="col"><?=getSetting('assessOneName')?></th>
                    <?php if(getSetting('num_assignment')>1) { ?>
                    <th width="" class="rotate" scope="col"><?=getSetting('assessTwoName')?></th>
                    <?php } if(getSetting('num_assignment')>2) { ?>
                    <th width="" class="rotate" scope="col"><?=getSetting('assessThreeName')?></th>
                    <?php }  if(getSetting('num_assignment')>3) { ?>
                    <th width="" class="rotate" scope="col"><?=getSetting('assessFourName')?></th>
                    <?php } if(getSetting('num_assignment')>4) { ?>
                    <th width="" class="rotate" scope="col"><?=getSetting('assessFiveName')?></th>
                    <?php } ?>                                
                <th width="" scope="col">Exam Score</th>
                <th width="" scope="col">Total Score</th>
                <th width="" scope="col">Class Average</th>
                <th width="" scope="col">Grade</th>
                <th width="" scope="col">Class Rank</th>
                <?php } else { ?>
                <th width="7%" scope="col">S/N</th>
                <th width="30%" scope="col">Student</th>
					<?php if($_GET['subject_id'] > 0) { ?>
                        <th width="" scope="col"><?=getSetting('assessOneName')?></th>
                        <?php if(getSetting('num_assignment')>1) { ?>
                        <th width="" scope="col"><?=getSetting('assessTwoName')?></th>
                        <?php } if(getSetting('num_assignment')>2) { ?>
                        <th width="" scope="col"><?=getSetting('assessThreeName')?></th>
                        <?php }  if(getSetting('num_assignment')>3) { ?>
                        <th width="" scope="col"><?=getSetting('assessFourName')?></th>
                        <?php } if(getSetting('num_assignment')>4) { ?>
                        <th width="" scope="col"><?=getSetting('assessFiveName')?></th>
                        <?php } ?>                
                    <th width="" scope="col">Exam Score</th>
                    <th width="" scope="col">Total Score</th>
                    <th width="" scope="col">Class Average</th>
                    <th width="" scope="col">Grade</th>
                    <th width="" scope="col">Class Rank</th>
                    <?php } else { ?>
                    <th width="" scope="col">Admission No.</th>
                    <th width="" scope="col">Total Score</th>
                    <th width="" scope="col">Average Score</th>
                    <?php if($_GET['subject_id'] > 0) { ?>
                    <th width="" scope="col">Class Average</th>
                    <?php } ?>
                    <th width="" scope="col">Class Rank</th>
                    <?php } ?>
                <?php } ?>
              </tr>

               <?php
			   $i=0;
				while($row = mysqli_fetch_assoc($resultP)){
					if($student > 0) {
						$id = $row['id'];
						$subject = subjectName($row['subject_id']);						
						$assessment = $row['assessment_score'];
						$assessment1 = $row['assessment_1'];
						$assessment2 = $row['assessment_2'];
						$assessment3 = $row['assessment_3'];
						$assessment4 = $row['assessment_4'];
						$assessment5 = $row['assessment_5'];
						$assessment = $assessment1+$assessment2+$assessment3+$assessment4+$assessment5;
						$multi_assign='yes';
						
						$exam_sc = $row['exam_score'];
						$total = $assessment+$exam_sc;
						$grade = gradeCode(getGrade($total,$school_id));
						$subjectTeacher = teacherName(getSubjectTeacher($row['subject_id']));
						$claAvr = subjectAvrg($row['subject_id'],filterinp($_GET['exam_id']));
						$sclassma = formatPosition(subClassRank($student,$row['subject_id'],$_GET['exam_id']));
					} else {
						$id = $row['id'];
						$subject = studentName($row['student_id']);

						$assessment = $row['assessment_score'];
						
						$assessment1 = $row['assessment_1'];
						$assessment2 = $row['assessment_2'];
						$assessment3 = $row['assessment_3'];
						$assessment4 = $row['assessment_4'];
						$assessment5 = $row['assessment_5'];
						$assessment = $assessment1+$assessment2+$assessment3+$assessment4+$assessment5;
						$multi_assign='yes';
						
						$exam_sc = $row['exam_score'];
						$total = $assessment+$exam_sc;
						$grade = gradeCode(getGrade($total,$school_id));
						
						$claAvr=round(examPercentage($row['student_id'],$exam), 2);
						$sclassma = formatPosition(subClassRank($row['student_id'],$_GET['subject_id'],$_GET['exam_id']));
						if($_GET['subject_id'] < 1) {
							$assessment = studentAdmissionNumber($row['student_id']);
							$exam_sc = totalAssessment($row['student_id'],filterinp($_GET['exam_id']))+totalExam($row['student_id'],$_GET['exam_id']);
							$multi_assign = 'no';
							$total = number_format(examPercentage($row['student_id'],filterinp($_GET['exam_id'])), 2);
							$grade = formatPosition(classRank($row['student_id'],filterinp($_GET['exam_id'])));
							$claAvr = subjectAvrg($_GET['subject_id'],filterinp($_GET['exam_id']));
						}
					}
				?>
             <div class="virtualpage hidepeice">
              <tr class="inner">
              <?php if($student > 0) { ?>
                <td width=""> <?php echo $subject; ?></td>
              <?php } else { ?>
                <td width=""> <?php echo $i+1; ?></td>
                <td width=""> <?php echo $subject; ?></td>
              <?php } ?>
                <?php if($multi_assign=='yes') {?>
               		 <td width=""><?=number_format($assessment1,2)?></td>
                     <?php if(getSetting('num_assignment')>1) { ?>
                     <td width=""><?=number_format($assessment2,2)?></td>
                     <?php } if(getSetting('num_assignment')>2) { ?>
                     <td width=""><?=number_format($assessment3,2)?></td>
                     <?php } if(getSetting('num_assignment')>3) { ?>
                     <td width=""><?=number_format($assessment4,2)?></td>
                     <?php } if(getSetting('num_assignment')>4) { ?>
                     <td width=""><?=number_format($assessment5,2)?></td>
                     <?php } ?>
                <?php } else {?>
                <td width=""> <?php echo $assessment; ?></td>
               <?php } ?>
                <td width=""> <?php echo number_format($exam_sc,2); ?></td>
                <td width=""> <?php echo colorScore($total); ?></td>
                <?php if($claAvr>0) {?>
                <td width=""> <?php echo number_format($claAvr,2); ?></td>
                <?php } ?>
                <td width=""> <?php echo $grade; ?></td>
                <?php if($multi_assign=='yes') {?>
               		 <td width=""><?=$sclassma?></td>
                <?php }?>
              </tr>
              </div>
         <?php
				$i++;	} ?>
	     </table>
<?php
	} else {
		$not_started=0;
?>
       	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <?php if($student > 0) { //apply cum view ?>
                <th width="20%" scope="col">Subject</th>
                <?php
				$sqlEX=$queryEX = "SELECT * FROM exams WHERE school_id = '$school_id' AND is_cumulative = 1 AND session_id = '".filterinp($_GET['session_id'])."' ORDER BY id ASC";
				$resultEX = mysqli_query($server, $queryEX);
				$numEX = mysqli_num_rows($resultEX);
				$i=0;
				while($row = mysqli_fetch_assoc($resultEX)){
				?>
                <th width="10%" scope="col"><?=$row['title'];?></th>
                <?php
				$i++;
				} ?>
                <th width="10%" scope="col">Cum. Average</th>
                <th width="10%" scope="col">Grade</th>
                <?php } else { ?>
                <th width="10%" scope="col">S/N</th>
                <th width="31%" scope="col">Student</th>
					<?php if($_GET['subject_id'] > 0) { ?>
                    <th width="13%" scope="col">Cum. Assessment</th>
                    <th width="13%" scope="col">Cum. Exam</th>
                    <th width="13%" scope="col">Cum. Average</th>
                    <th width="10%" scope="col">Grade</th>
                    <?php } else { ?>
                    <th width="13%" scope="col">Adm. No.</th>
                    <th width="13%" scope="col">Cum. Marks</th>
                    <th width="13%" scope="col">Cum Average</th>
                    <th width="10%" scope="col">Class Rank</th>
                    <?php } ?>
                <?php } ?>
              </tr>

               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($resultP)){
					if($student > 0) {
						//apply cum view
						$id = $row['id'];
						$subject = subjectName($row['subject_id']);
						$total = sessionSubPercentage($row['student_id'],filterinp($_GET['session_id']),$row['subject_id']);
						$grade = gradeCode(getGrade($total,$school_id));
						$subjectTeacher = teacherName(getSubjectTeacher($row['subject_id']));
					} else {
						$id = $row['id'];
						$subject = studentName($row['student_id']);
						$assessment = totalSessionAssessment($row['student_id'],filterinp($_GET['session_id']))/totalSessionSubjectsTaken($row['student_id'],filterinp($_GET['session_id']));
						$exam_sc = totalSessionExam($row['student_id'],filterinp($_GET['session_id']))/totalSessionSubjectsTaken($row['student_id'],$_GET['session_id']);
						$total = $assessment+$exam_sc;
						$grade = gradeCode(getGrade($total,$school_id));

						if($_GET['subject_id'] < 1) {
							$assessment = studentAdmissionNumber($row['student_id']);
							$exam_sc = (totalSessionAssessment($row['student_id'],filterinp($_GET['session_id']))+totalSessionExam($row['student_id'],filterinp($_GET['session_id'])));
							$total = round(sessionPercentage($row['student_id'],filterinp($_GET['session_id'])), 2);
							$grade = formatPosition(sessionClassRank($row['student_id'],filterinp($_GET['session_id'])));
						}
					}
				?>
             <div class="virtualpage hidepeice">
              <tr class="inner">
              <?php if($student > 0) { ?>
                <td > <?php echo $subject; ?></td>
                <?php
				$sqlEX=$queryEX = "SELECT * FROM exams WHERE school_id = '$school_id' AND is_cumulative = 1 AND session_id = '".filterinp($_GET['session_id'])."' ORDER BY id ASC";
				$resultEX = mysqli_query($server, $queryEX);
				$numEX = mysqli_num_rows($resultEX);
				$i=0;
				while($rowe = mysqli_fetch_assoc($resultEX)){
				?>
                <td > <?php echo round(totalSubAssessment($row['student_id'],$rowe['id'],$row['subject_id'])+totalSubExam($row['student_id'],$rowe['id'],$row['subject_id']), 2); ?></td>
                <?php
							$i++;
				} ?>
                <td > <?php echo colorScore($total); ?></td>
                <td > <?php echo $grade; ?></td>
              <?php } else { ?>
                <td width="10%"> <?php echo $i+1; ?></td>
                <td width="31%"> <?php echo $subject; ?></td>
                <td width="13%"> <?php echo $assessment; ?></td>
                <td width="13%"> <?php echo $exam_sc; ?></td>
                <td width="13%"> <?php echo colorScore($total); ?></td>
                <td width="10%"> <?php echo $grade; ?></td>
              <?php } ?>
              </tr>
              </div>
         <?php
			 				$i++;
					} ?>
	     </table>

<?php } //end of cumulative check ?>
    <?php } ?>
<?php displayPagination($setLimit,$page,$query) ?>
<?php 
$exam = mysqli_real_escape_string($server,$_GET['exam_id']);
$session = mysqli_real_escape_string($server,$_GET['session_id']);
$student = mysqli_real_escape_string($server,$_GET['student_id']);
$class = filterinp($_GET['class_id']);
$subject = filterinp(@$_GET['subject_id']);
$vars = array( "exam_id"=>$exam,
			 "student_id"=>$student, 
			 "session_id"=>$session,
			 "class_id"=>$class,
			 "subject_id"=>$subject
		);
$_SESSION['EventVals'] = $vars;
global $hooks;$hooks->do_action('ReportCardFooter'); ?>			
				<br /><br />
        <?php if(@$_GET['subject_id'] > 0 || $student > 0) { ?>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th scope="col"> <strong>Grade Scale</strong></th>
              </tr>
              <tr class="inner">
                <td >
				<?php
	$sql = "SELECT * FROM grades WHERE school_id = '$school_id' ORDER BY start_mark DESC";
	if($student > 0) {
		$classnow = getClass($student,$session);
		if(count_assigned_grade_class($classnow)>0) {
			$sql = "SELECT * FROM grades WHERE school_id = '$school_id' AND FIND_IN_SET('$classnow', class_id) ORDER BY start_mark DESC";
		}
	}
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);

	$i=0;
	while($row = mysqli_fetch_assoc($result)){
		$min = $row['start_mark'];
		$max = $row['end_mark'];
		$code = $row['code'];

	echo $min.' - '.$max.' = '.$code.', &nbsp;&nbsp;';
	if($i == 5 || $i == 11) echo '<hr>';
			$i++;

	}
				 ?>
                </td>
              </tr>
              </table>
	<?php } ?>

    <br /><br />
        <?php if($student > 0) { ?>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th scope="col"> <strong>Comments/Notes</strong></th>
              </tr>
              <tr class="inner">
                <td >
				<?php
				$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$sql2=$query2="SELECT * FROM exam_note WHERE student_id = '$student' AND exam_id = '$exam' AND seasion_id = '".filterinp($_GET['session_id'])."'";
	$result2 = mysqli_query($server, $query2) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result2);
	$notes = $row['notes'];
	if(!empty($notes)) {
		echo nl2br($notes); 
		echo "<br><p class='no-print'><a href='admin/reportcard.php?addnote&upnot&student_id=$student&exam_id=$exam&session_id=$session'><button class='btn btn-info'>Edit Comments</button></a></p>";
	} else {
		echo "No notes attached to this student's report" ;
		echo "<p class='no-print'><a href='$actual_link&addnote&student_id=$student&exam_id=$exam&session_id=$session'><button  class='btn btn-info'>Add Comments</button></a></p>";
}
				 ?>
                </td>
              </tr>
              </table>
	<?php } ?>

        </div>
    </div>
<?php } ?>
<?php if (@$not_started>0) { ?>
	<div class="alert alert-info" style="">We nned some more information to generate an Exam Report for you!<br>Please select a <strong>Report Type</strong>, <strong>Session</strong>, <strong>Exam</strong> and other desired report criteria to view a report</div>
<?php } ?>
</div>