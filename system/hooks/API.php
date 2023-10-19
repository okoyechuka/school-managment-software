<?php header('Access-Control-Allow-Origin: *');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;
error_reporting(0);
global $LANG;
global $configverssion_id;
global $configapp_version;
global $server;
$userID = getUser();
$resellerID = userData($userID,'reseller');
global $userID;
global $resellerID;
global $URL;
define('TIMEOUT_MINUTES', 120);	
$timeout = (TIMEOUT_MINUTES == 0 ? 0 : time() + TIMEOUT_MINUTES * 1200);
global $timeout;	

if($userID < 1) {
	if(!adminLogedIn()) 	echo 'access denied!';die();	
}
function student_offerssubject2($subject,$student) {
	global $server;
	$result = mysqli_query($server, "SELECT * FROM subject_student WHERE subject_id = '$subject' AND student_id = '$student'") or die(mysqli_error($server));
	$row = mysqli_num_rows($result);
	if($row > 0) return true; 
	return false;
}
function chatTimes($ptime,$endtime=''){
	if(empty($endtime)) $endtime = time();
	if(!is_numeric($ptime))
	$ptime = strtotime($ptime);
	if(!is_numeric($endtime))
    $endtime = strtotime($endtime);
	$etime = $endtime - $ptime;
	if ($etime < 1) {
        return 'Just now';
    }
    $a = array( 365 * 24 * 60 * 60  =>  'year',
                 30 * 24 * 60 * 60  =>  'month',
			      7 * 24 * 60 * 60  =>  'week',
                      24 * 60 * 60  =>  'day',
                           60 * 60  =>  'hour',
                                60  =>  'minute',
                                 1  =>  'second'
                );
    $a_plural = array( 'year'   => 'years',
                       'month'  => 'months',
					   'week'  => 'weeks',
                       'day'    => 'days',
                       'hour'   => 'hours',
                       'minute' => 'minutes',
                       'second' => 'seconds'
                );

    foreach ($a as $secs => $str)   {
        $d = $etime / $secs;
        if ($d >= 1)  {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
        }
    }
}
function roomAvatName($id) {
	if($id<1) return 'Teacher';
	if(userRole($id)==4) return 'Teacher';
	return userData('name',$id); 
}
function chshorten($intro, $len) { 
	$string = $intro;
	if (strlen($string) > $len) 	{
    	$string = $stringCut = substr($string, 0, $len);
       $string = substr($stringCut, 0, strrpos($stringCut, ' ')); 
	} 
	return $string;
}
function chfilterInputValues($input,$html=false){
	global $server;
	if($html==false) {
		$strip = strip_tags($input);
		$strip = str_replace('">','',$strip);
		$strip = str_replace('/>','',$strip);
	}
	$previn = mysqli_real_escape_string($server, $strip);
	return $previn;
}

if(isset($_REQUEST['count_new_messchat'])) {
	if(!isset($_SESSION['myctRoomID'])) { echo '0'; die(); }
	$room = chfilterInputValues($_REQUEST['church']);	
	$lasttime = chfilterInputValues($_REQUEST['count_new_messchat']);	
	$result = mysqli_query($server,"SELECT * FROM chats WHERE `room` = '$room' AND `datetime` > '$lasttime'");
	$num = mysqli_num_rows($result);	
	echo $num;	die();
}
if(isset($_REQUEST['send_chat'])) {
	if(!isset($_SESSION['myctRoomID'])) { echo 'empty'; die(); }
	$room = chfilterInputValues($_REQUEST['room']);
	$member = getUser();
	//if(userRole(getUser())<5) $member = 0;
	$text = chfilterInputValues($_REQUEST['text']);	
	if(userRole(getUser())>4  && $member <1) { echo 'empty'; die(); }
	if(!empty($text)) {
		$datetime = time();
		mysqli_query($server, "INSERT INTO chats (`room`, `member_id`, `message`, `datetime`) VALUES ('$room', '$member', '$text', '$datetime');");echo 'success';
	} else {
		echo 'empty';	
	}
	die();
}
if(isset($_REQUEST['LaunchChats'])) {
	if(!isset($_SESSION['myctRoomID'])) { echo 'empty'; die(); }
	$room = chfilterInputValues($_REQUEST['LaunchChats']);	
	$datetime = chfilterInputValues($_REQUEST['last']);	
	$ftdays = time()-(86400*14);
    $result = mysqli_query($server,"select * from chats WHERE room = '$room' AND datetime > '$datetime' AND datetime > '$ftdays' order by id asc limit 300");
	$counts = mysqli_num_rows($result);
	$rows = array();
	while ($r = mysqli_fetch_assoc($result)) {	
			$r['member_name'] = chshorten(roomAvatName($r['member_id']),40);
			$r['agos'] = chatTimes($r['datetime']);
			$r['lasttime'] = $r['datetime'];
			$rows[] = $r;
	}
	echo json_encode($rows);	die();
}
if(isset($_REQUEST['start_test'])) {
	foreach ($_REQUEST as $key => $value ){
		${$key} = $value = mysqli_real_escape_string($server,$value);
	}
	$student_id = userProfile($userID);
	if($cbt_id < 1) { echo "Something doesnt seem right! Please reload the page and try again";die(); }
	
	$query = "SELECT * FROM cbt_answers WHERE cbt_id = '$cbt_id' AND student_id = '$student_id'";
	$result3 = mysqli_query($server, $query);
	$num = mysqli_num_rows($result3);
	if($num < "1")	{ echo "Something doesnt seem right! Please reload the page and try again";die(); }
	if(cbtFinished($student_id,$cbt_id) ) {
		echo "You have already completed this test";die();
	}
	if(cbtFinishTime($student_id,$cbt_id) != cbtStartTime($student_id,$cbt_id)) {
		echo cbtStartTime($student_id,$cbt_id); die();
	} else {
		$start_time = time()+10; //the time seconds is to take care of server loading time
		$query="UPDATE `cbt_answers` SET `answer_date` =  '$start_time' WHERE `cbt_id` = '$cbt_id' AND student_id = '$student_id'";
		mysqli_query($server, $query) or die(mysqli_error($server));
		echo $start_time; die();
	}
	echo "Unknown error has occured";die();
}

if(isset($_REQUEST['finish_test'])) {
	foreach ($_REQUEST as $key => $value ){
		${$key} = $value = mysqli_real_escape_string($server,$value);
	}
	$student_id = userProfile($userID);
	if($cbt_id < 1) { echo "Something doesnt seem right! Please reload the page and try again";die(); }
	$query = "SELECT * FROM cbt_answers WHERE cbt_id = '$cbt_id' AND student_id = '$student_id'";
	$result3 = mysqli_query($server, $query);
	$num = mysqli_num_rows($result3);
	if($num < "1")	{ echo "Something doesnt seem right! Please reload the page and try again";die(); }
	$competion_time = time();
	$query="UPDATE `cbt_answers` SET `competion_time` =  '$competion_time', `is_finished` = 1 WHERE `cbt_id` = '$cbt_id' AND student_id = '$student_id'";
	mysqli_query($server, $query) or die(mysqli_error($server));
	echo "OK"; die();
}

if(isset($_REQUEST['end_test'])) {
	foreach ($_REQUEST as $key => $value ){
		${$key} = $value = mysqli_real_escape_string($server,$value);
	}
	$student_id = userProfile($userID);
	if($cbt_id < 1) { echo "Something doesnt seem right! Please reload the page and try again";die(); }
	$query = "SELECT * FROM cbt_answers WHERE cbt_id = '$cbt_id' AND student_id = '$student_id'";
	$result3 = mysqli_query($server, $query);
	$num = mysqli_num_rows($result3);
	if($num < "1")	{ echo "Something doesnt seem right! Please reload the page and try again";die(); }
	$competion_time = time();
	mysqli_query($server, "UPDATE `cbt_answers` SET `competion_time` =  '$competion_time', `is_finished` = 1 WHERE `cbt_id` = '$cbt_id' AND student_id = '$student_id'") or die(mysqli_error($server));
	//compute scores if exam
	if(cbtData('exam_id',$cbt_id) >0) {
		$subject_id = cbtData('subject_id',$cbt_id);
		$exam_id = cbtData('exam_id',$cbt_id);
		$mark_type = cbtData('mark_type',$cbt_id);
		$mark_contribute = cbtData('accessment_id',$cbt_id)>0?cbtData('accessment_id',$cbt_id):100;
		$ratio = round(countCorrectAnswered($student_id,$cbt_id)/cbtData('question_limit',$cbt_id),2);
		$scorePerc = ($ratio*100);
		$contributedScore = round(($scorePerc/1100)*$mark_contribute,2);
		if(scoreExist($school_id,$subject_id,$exam_id,$student_id)) {
			if($mark_type == "exam") {
				mysqli_query($server, "UPDATE `exam_student_score` SET `exam_score` =  '$contributedScore' WHERE school_id = '$school_id' AND subject_id = '$subject_id' AND exam_id = '$exam_id' AND student_id = '$student_id'");
			} else {
				mysqli_query($server, "UPDATE `exam_student_score` SET 
				`$mark_type` =  '$contributedScore'
				WHERE school_id = '$school_id' AND subject_id = '$subject_id' AND exam_id = '$exam_id' AND student_id = '$student_id'");
			}
		} else {
			$session_id = examSession($exam_id);
			$class = getClass($student_id,$session_id);
			if($mark_type == "exam") {
				$assessment_score = $assessment_1 = $assessment_2 = $assessment_3 = $assessment_4 = $assessment_5 =  0;
				mysqli_query($server, "INSERT INTO exam_student_score (`id`, `school_id`, `subject_id`, `exam_id`, `student_id`, `assessment_score`, `exam_score`,`session_id`, `class_id`, `assessment_1`, `assessment_2`, `assessment_3`, `assessment_4`, `assessment_5`)
		VALUES (NULL, '$school_id', '$subject_id', '$exam_id', '$student_id', '$assessment_score', '$contributedScore', '$session_id', '$class', '$assessment_1', '$assessment_2', '$assessment_3',  '$assessment_4',  '$assessment_5');");
			} else {
				$exam_score = $assessment_1 = $assessment_2 = $assessment_3 = $assessment_5 = $assessment_4 = 0;
				$assessment_score =  $contributedScore;
				if($mark_type =="assessment_1") {
					$assessment_1 =  $contributedScore;
				}
				if($mark_type =="assessment_2") {
					$assessment_2 =  $contributedScore;
				}
				if($mark_type =="assessment_3") {
					$assessment_3 =  $contributedScore;
				}
				if($mark_type =="assessment_4") {
					$assessment_4 =  $contributedScore;
				}
				if($mark_type =="assessment_5") {
					$assessment_5 =  $contributedScore;
				}
				mysqli_query($server, "INSERT INTO exam_student_score (`id`, `school_id`, `subject_id`, `exam_id`, `student_id`, `assessment_score`, `exam_score`,`session_id`, `class_id`, `assessment_1`, `assessment_2`, `assessment_3`, `assessment_4`, `assessment_5`)
		VALUES (NULL, '$school_id', '$subject_id', '$exam_id', '$student_id', '$assessment_score', '$exam_score', '$session_id', '$class', '$assessment_1', '$assessment_2', '$assessment_3', '$assessment_4', '$assessment_5');");
			}
		}
	}
	echo "OK";die();
}

if(isset($_REQUEST['submit_answer'])) {
	foreach ($_REQUEST as $key => $value ){
		${$key} = $value = mysqli_real_escape_string($server,$value);
	}
	$student_id = userProfile($userID);
	if(cbtFinished($student_id,$cbt_id) ) {	echo "You have already completed this test";die();	}
	if($cbt_id < 1) { echo "Something doesnt seem right with your test! Please reload the page and try again";die(); }
	if($answer_id < 1) { echo "Your choice is empty! Please reload the page and try again";die(); }
	$query = "SELECT * FROM cbt_answers WHERE question_id = '$question_id' AND student_id = '$student_id'";
	$result3 = mysqli_query($server, $query);
	$num = mysqli_num_rows($result3);
	if($num < "1")	{ echo "Something doesnt seem right your choices! Please reload the page and try again";die(); }
	$answer = mysqli_real_escape_string($server,getAnswerValue($answer_id));
	if(empty($answer))	{ echo "Something doesnt seem right with your answer! Please reload the page and try again";die(); }
	$query="UPDATE `cbt_answers` SET `answer` =  '$answer' WHERE `question_id` = '$question_id' AND student_id = '$student_id'";
	mysqli_query($server, $query) or die(mysqli_error($server));
	echo "OK";die();
}

if(isset($_REQUEST['sese'])) {
//Seasion Exam
    $query="SELECT * FROM exams WHERE school_id = '$school_id' AND session_id = ".filterinp($_REQUEST['id'])." ORDER BY id DESC";
    $resultC = mysqli_query($server, $query);
	while($row = mysqli_fetch_assoc($resultC)){
    $g_id = $row['id'];
    $title = $row['title'];
    ?> 			
    <option value="<?php echo $g_id; ?>"><?php echo $title; ?></option>
    <?php  }   ?>
    <option value="0"><?=sessionName(filterinp($_REQUEST['id'])) ?> Cumulative Result</option>
    
<?php
die();
}

if(isset($_REQUEST['clasus'])) {
//   Class Subject
    $query="SELECT * FROM subject WHERE school_id = '$school_id' AND class_id = ".filterinp($_REQUEST['id'])." OR class_id = 0 ORDER BY title ASC";
	if($_REQUEST['id']<1) {
		$query="SELECT * FROM subject WHERE school_id = '$school_id' ORDER BY title ASC";
	}
    $resultC = mysqli_query($server, $query);
    ?> <option value="0">All Subjects</option><?php          
	while($row = mysqli_fetch_assoc($resultC)){	
    $g_id = $row['id'];
    $title = $row['title'].' ('.className($row['class_id']).')';
    ?> 			
    <option value="<?php echo $g_id; ?>"><?php echo $title; ?></option>
    <?php  }   ?>
<?php
die();
}
if(isset($_REQUEST['classtu'])) {
//Class Students
    $query="SELECT s.* FROM students s JOIN student_class c ON c.student_id = s.id WHERE s.school_id = '$school_id' AND c.class_id = ".filterinp($_REQUEST['id'])." ORDER BY s.first_name ASC";
	if($_REQUEST['id']<1) {
		$query="SELECT s.* FROM students s WHERE s.school_id = '$school_id' ORDER BY s.first_name ASC";
	}
    $resultC = mysqli_query($server, $query);
	while($row = mysqli_fetch_assoc($resultC)){
    $g_id = $row['id'];
    $title = studentName($g_id);
    ?> 			
    <option value="<?php echo $g_id; ?>"><?php echo $title; ?></option>
    <?php  }   ?>
<?php
die();
}

if(isset($_REQUEST['sub_classtu'])) {
	//Class Students for subject module
	$classs = filterinp($_REQUEST['id']);
	$subj_classs = filterinp($_REQUEST['sid']);
    $query="SELECT s.* FROM students s JOIN student_class c ON c.student_id = s.id WHERE s.school_id = '$school_id' AND c.class_id = ".$classs." ORDER BY s.first_name ASC";
	if($classs<1) {
		$query="SELECT s.* FROM students s WHERE s.school_id = '$school_id' ORDER BY s.first_name ASC";
	}
    $resultC = mysqli_query($server, $query);
	?>
    <option value="">Do not assign</option>
    <?php
	while($row = mysqli_fetch_assoc($resultC)){
    $g_id = $row['id'];
    $title = studentName($g_id);
    ?> 			
    <option <?php if(student_offerssubject2($subj_classs,$g_id)) { echo 'selected';} ?> value="<?=$g_id; ?>"><?php echo $title; ?></option>
    <?php  }   ?>
<?php
die();
}

if(isset($_REQUEST['sub_stud'])) {
	//Students for subject in exam module
	$subject_id = filterinp($_REQUEST['id']);
	$exam_class_id = filterinp($_REQUEST['sid']);
	$exam_session_id = filterinp($_REQUEST['esid']);
    $query="SELECT * FROM student_class WHERE class_id = '$exam_class_id' AND session_id = '$exam_session_id'";
	if(assigned_subect_students($subject_id)>0) {
		$query="SELECT * FROM subject_student WHERE subject_id = '$subject_id' ORDER BY id ASC";
	}
    $resultC = mysqli_query($server, $query);
	while($row = mysqli_fetch_assoc($resultC)){
    ?> 			
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
        <?php } ?>
        <td align="left" valign="middle">
        <input type="number" name="exam_score[<?php echo $row['student_id']; ?>]"  required="required"  value="0">
        </td>
    </tr>
    <?php  }   ?>
<?php
die();
}


if(isset($_REQUEST['usrex'])) {
//User session Exam
	$id = filterinp($_REQUEST['id']);
    $query="SELECT * FROM exams WHERE school_id = '$school_id' AND session_id = '$id' ORDER BY id DESC";
    $resultC = mysqli_query($server, $query);
	while($row = mysqli_fetch_assoc($resultC)){
    $g_id = $row['id'];
    $title = $row['title'];
    ?> 			
    <option value="<?php echo $g_id; ?>"><?php echo $title; ?></option>
    <?php  }   ?>
	<option value="0"><?=sessionName($id) ?> Cumulative Result</option> 
<?php }    

//load gateway templates
if(isset($_REQUEST['load_pay_gateway_temp'])) {
	foreach ($_REQUEST as $key => $value ){
		${$key} = $value = mysqli_real_escape_string($server,$value);
	}
	$query="SELECT * FROM paymentgateway_templates WHERE alias = '$load_pay_gateway_temp'"; 
	$result = mysqli_query($server,$query) or die(mysqli_error($server));  
	$row = mysqli_fetch_assoc($result); 
	echo $row['param1_label'].':'.$row['param2_label'].':'.$row['param3_label'];	die();
}
//do upgrades
if(isset($_REQUEST['do_upgrade'])) {
	 echo upgradeToLatest(); die();
}

if(isset($_REQUEST['do_update'])) {
	echo updateToLatest(); die();
}
global $hooks;$hooks->do_action('CustomAjaxAPIEvent');
exit;