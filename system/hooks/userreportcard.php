<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		reportcard.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			10/03/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

?>
<?php
$session = getSetting('current_session');
$term = getSetting('current_term');
$student = 0;

if(isset($_POST['exam_id'])){
$exam = mysqli_real_escape_string($server,$_POST['exam_id']);
$session = mysqli_real_escape_string($server,$_POST['session_id']);
$student = mysqli_real_escape_string($server,$_POST['student_id']);

	$sql=$query = "select * FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.exam_id = '$exam' AND e.student_id = '$student' ORDER BY e.id ASC";
	$report_title = studentName($student).' - '.examName($exam).' - Report Card';
	if($exam < 1) {
		$sql=$query = "select * FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.session_id = '$session' AND e.student_id = '$student' AND ex.is_cumulative = 1 ORDER BY e.id ASC";
		$report_title = studentName($student).' - '.sessionName($session).' - Cumulative Report';
	}
 	$resultP = mysqli_query($server, $sql) or die(mysqli_error($server));
	$numP = mysqli_num_rows($resultP);


	if($numP < "1")
		{
		$message = "No exam record found for your selections! Please try another search.";
		$class="blue";
		}
} else {

		$message = "Select an exam to view report";
		$class="blue";
}

?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="POST">
        <select name="student_id" id="e3">
			<?php
			if(userRole($userID) == 5) {
				$parent = userProfile($userID);

			     $sqlC=$queryC="SELECT * FROM students s JOIN student_parent p ON p.student_id = s.id WHERE p.parent_id = '$parent' ORDER BY s.first_name ASC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

                $iC=0;
								while($rowC=mysqli_fetch_assoc($resultC)){
                $c_id = $rowC['id'];
                $title = studentName($rowC['id']);
            ?>
               <option  value="<?php echo $c_id; ?>"  <?php if($student == $c_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php  $iC++; }
			} else { $student = userProfile($userID); ?>
            	<option  value="<?php echo $student; ?>"><?php echo studentName($student); ?></option>
            <?php } ?>
		</select>
        &nbsp;
        <select name="session_id" id="e1">
       		<option value="0">Select Session</option>
			<?php
			     $sqlC=$queryC="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY id DESC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

                $iC=0;
								while($rowC=mysqli_fetch_assoc($resultC)){
                $c_id = $rowC['id'];
                $titleF = $rowC['title'];
            ?>
               <option value="<?php echo $c_id; ?>" <?php if($session == $c_id) {echo 'selected';} ?>><?php echo $titleF; ?></option>
            <?php  $iC++; }  ?>
			</select>
         &nbsp;
           <select name="exam_id" required id="e2" >
			<?php
            $sql=$query="SELECT * FROM exams WHERE school_id = '$school_id' AND session_id = '$session' ORDER BY id DESC";
            $result = mysqli_query($server, $query);
            $num = mysqli_num_rows($result);

						$i=0;
						while($row = mysqli_fetch_assoc($result)){
            $g_id = $row['id'];
            $title = $row['title'];
            ?>
            <option value="<?php echo $g_id; ?>"><?php echo $title; ?></option>
            <?php  $i++; }   ?>
			</select>
		&nbsp;
        <button class="submit btn-success"><i class="fa fa-search"></i> View</button>
        <a href="" onclick="javascript:printDiv('print-this1')"><button class="submit">Print</button></a>
        </form>
    </div>

    <?php if(@$numP > 0) { ?>
    <?php global $hooks;$hooks->do_action('ReportCardHeader'); ?>			
	<div class="panel" id="print-this1" style="/* [disabled]border-color: transparent; */">
    	<div class="panel-head"> &nbsp;<?php echo $report_title;?></div>
         <div id="print-this1" class="panel-body">
        <br /><div style="text-align: center">
        <img src="media/uploads/<?php echo getSetting('logo'); ?>" style="width: 40px;" /><br />
		<?php echo '<strong class="capitalize">'.getSetting('name').'</strong></left><br>';
		echo '<small>'.getSetting('address').', ';
		echo ''.getSetting('city').', '.getSetting('state').'</small><br>';
		echo '<small>Email: '.getSetting('email').'</small> &nbsp;';
		echo '<small>Tel: '.getSetting('phone1').' '.getSetting('phone2').'</small><br>';   ?>
        <hr /><h2 class="result"><?php echo $report_title;?></h2>
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
       </table><br>
        <?php } ?>
 <?php
 //check if cumulative
 if($exam > 0 ) {
 ?>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="25%" scope="col">Subject</th>
                    <th width="" class="rotate" scope="col"><?=getSetting('assessOneName')?></th>
                    <?php if(getSetting('num_assignment')>1) { ?>
                    <th width="" class="rotate" scope="col"><?=getSetting('assessTwoName')?></th>
                    <?php } if(getSetting('num_assignment')>2) { ?>
                    <th width="" class="rotate" scope="col"><?=getSetting('assessThreeName')?></th>
                    <?php } if(getSetting('num_assignment')>3) { ?>
                    <th width="" class="rotate" scope="col"><?=getSetting('assessFourName')?></th>
                    <?php } if(getSetting('num_assignment')>4) { ?>
                    <th width="" class="rotate" scope="col"><?=getSetting('assessFiveName')?></th>
                    <?php } ?>                
                <th width="" scope="col">Exam Score</th>
                <th width="" scope="col">Total Score</th>
                <th width="" scope="col">Class Average</th>
                <th width="" scope="col">Grade</th>
                <th width="" scope="col">Class Rank</th>
              </tr>
               <?php
				$iP=0;
				while($rowP=mysqli_fetch_assoc($resultP)){
					$id = $row['id'];
					$subject = subjectName($rowP['subject_id']);
					$assessment = $rowP['assessment_score'];
					$assessment1 = $rowP['assessment_1'];
					$assessment2 = $rowP['assessment_2'];
					$assessment3 = $rowP['assessment_3'];
					$assessment4 = $rowP['assessment_4'];
					$assessment5 = $rowP['assessment_5'];
					$assessment = $assessment1+$assessment2+$assessment3+$assessment4+$assessment5;
					$exam_sc = $rowP['exam_score'];
					$total = $assessment+$exam_sc;
					$grade = gradeCode(getGrade($total,$school_id));
					$subjectTeacher = teacherName(getSubjectTeacher($rowP['subject_id']));
					$claAvr = subjectAvrg($rowP['subject_id'],filterinp($_REQUEST['exam_id']));
					$sclassma = formatPosition(subClassRank($student,$rowP['subject_id'],$_REQUEST['exam_id']));
				?>
             <div class="virtualpage hidepeice">
              <tr class="inner">
                <td width=""> <?php echo $subject; ?></td>
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
                <td width=""> <?php echo $exam_sc; ?></td>
                <td width=""> <?php echo colorScore($total); ?></td>
                <td width=""> <?php echo number_format($claAvr,2); ?></td>
                <td width=""> <?php echo $grade; ?></td>
                
                <td width=""><?=$sclassma?></td>
              </tr>
              </div>
              <?php $iP++;} ?>
              </table>
<?php } else { ?>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="20%" scope="col">Subject</th>
                <th width="21%" scope="col">Teacher</th>
                <?php
				$sqlEX=$queryEX= "SELECT * FROM exams WHERE school_id = '$school_id' AND is_cumulative = 1 AND session_id = '$session' ORDER BY id ASC";
				$resultEX = mysqli_query($server, $sqlEX);
				$numEX = mysqli_num_rows($resultEX);
				$ie=0;
				while($row_ie=mysqli_fetch_assoc($resultEX)){
				?>
                <th width="10%" scope="col"><?=$row_ie['title'];?> (%)</th>
                <?php $ie++;} ?>
                <th width="10%" scope="col">Cum. Average (%)</th>
                <th width="10%" scope="col">Grade</th>
              </tr>
               <?php
				 $iP=0;
 				while($rowP=mysqli_fetch_assoc($resultP)){
					$id = $rowP['id'];
					$subject = subjectName($rowP['subject_id']);
					$total = $assessment+$exam_sc;
					$total = sessionSubPercentage($rowP['student_id'],$session,$subject);
					$grade = gradeCode(getGrade($total,$school_id));
					$subjectTeacher = teacherName(getSubjectTeacher($rowP['subject_id']));
				?>
             <div class="virtualpage hidepeice">
              <tr class="inner">
                <td > <?php echo $subject; ?></td>
                <td > <?php echo $subjectTeacher; ?></td>
                <?php
				$sqlEX=$queryEX= "SELECT * FROM exams WHERE school_id = '$school_id' AND is_cumulative = 1 AND session_id = '$session' ORDER BY id ASC";
				$resultEX = mysqli_query($server, $sqlEX);
				$numEX = mysqli_num_rows($resultEX);
				$ie=0;
				while($row_ie = mysqli_fetch_assoc($resultEX)){
				?>
                <td > <?php echo round(totalSubAssessment($rowP['student_id'],$row_ie['id'],$rowP['subject_id'])+totalSubExam($rowP['student_id'],$row_ie['id'],$rowP['subject_id']), 2); ?></td>
                <?php $ie++; } ?>
                <td > <?php echo colorScore($total); ?></td>
                <td > <?php echo $grade; ?></td>
              </tr>
              </div>
              <?php
						$iP++;
					} ?>
              </table>
<?php } //end of cumulative check ?>
<?php displayPagination($setLimit,$page,$sql) ?>

<?php 
$exam = mysqli_real_escape_string($server,$_POST['exam_id']);
$session = mysqli_real_escape_string($server,$_POST['session_id']);
$student = mysqli_real_escape_string($server,$_POST['student_id']);
$vars = array( "exam_id"=>$exam,
			 "student_id"=>$student, 
			 "session_id"=>$session
		);
$_SESSION['EventVals'] = $vars;
global $hooks;$hooks->do_action('ReportCardFooter'); 
?>			
				<br /><br />
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th scope="col"> <strong>Grade Scale</strong></th>
              </tr>
              <tr class="inner">
                <td >
				<?php
	$sql = "SELECT * FROM grades WHERE school_id = '$school_id' ORDER BY start_mark DESC";
	$classnow = getClass($student,$session);
	if(count_assigned_grade_class($classnow)>0) {
		$sql = "SELECT * FROM grades WHERE school_id = '$school_id' AND FIND_IN_SET('$classnow', class_id) ORDER BY start_mark DESC";
	}
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);

	$i=0;
	while($row = mysqli_fetch_assoc($result)){
		$min = $row['start_mark'];
		$max = $row['end_mark'];
		$code = $row['code'];

	echo $min.' - '.$max.' = '.$code.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($i == 5 || $i == 11) echo '<hr>';
	$i++;
	}
				 ?>
                </td>
              </tr>
              </table>

    <br /><br />
        <?php if($student > 0) { ?>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th scope="col"> <strong>Comments</strong></th>
              </tr>
              <tr class="inner">
                <td >
				<?php
$sql2=$query2="SELECT * FROM exam_note WHERE student_id = '$student' AND exam_id = '$exam' AND seasion_id = '$session'";
	$result2 = mysqli_query($server, $query2) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result2);
	$notes = $row['notes'];
	if(!empty($notes)) { echo nl2br($notes); } else { echo "No comments attached to this report" ; 	}
				 ?>
                </td>
              </tr>
              </table>
	<?php } ?>
        </div>
        </div>
    <?php } ?>
</div>
