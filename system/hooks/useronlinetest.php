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

$session = getSetting('current_session');
$term = getSetting('current_term');

$student_id = userProfile($userID);
$student_class = getClass($student_id,$session);

if(userRole($userID) != 6) {
header('location: index.php');
}
if(!isset($_REQUEST['cbt'])){
	header('location: usercbt');
} else {
	$cbt_id = filterinp($_REQUEST['cbt']);
	$query="SELECT * FROM cbt WHERE id = '$cbt_id' AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$course = mysqli_fetch_assoc($result);
	if($course['id'] < 1 ) header('location: usercbt?0');
	if($course['exam_id'] > 0) {
		if(subjectClass($course['subject_id']) != $student_class) {
			header('location: usercbt?1');
		}
	}
	if($course['course_id'] > 0) {
		if(!classsOfferCourse($student_class,$course['course_id'])) {
			header('location: usercbt?2');
		}
	}
	if(strtotime($course['start_date'])>time()) {
		header('location: usercbt?3');
	}
    if(strtotime($course['expire_date'])>100&&strtotime($course['expire_date'])<=time()) {
		header('location: usercbt?4');		
	}
	$continue_test = false;
	if(cbtTaken($student_id,$cbt_id) >0 && $course['allow_repeat']=="0") { 
		if(cbtFinished($student_id,$cbt_id) ) {
			header('location: usercbt?4f');		
		}
		$quize_started = cbtStartTime($student_id,$cbt_id);
		if(cbtFinishTime($student_id,$cbt_id) != cbtStartTime($student_id,$cbt_id)) { 
			if((cbtStartTime($student_id,$cbt_id)+$course['time_duration']) > time()) {
				$continue_test = true;
			} else {
				$ths = time();
				mysqli_query($server,"UPDATE `cbt_answers` SET `competion_time` =  '$ths', `is_finished` = 1 WHERE `cbt_id` = '$cbt_id' AND student_id = '$student_id'");
				header('location: usercbt?5');
			}
		} else {
			$continue_test = true;
		}
	} else { 
		if(cbtFinishTime($student_id,$cbt_id) != cbtStartTime($student_id,$cbt_id)) {
			if((cbtStartTime($student_id,$cbt_id)+$course['time_duration']) > time()) {
				$continue_test = true;
			} else {
				$continue_test = false;
			}
		} else {
			$continue_test = true;
		}
	}
	if(cbtTaken($student_id,$cbt_id) <1) $continue_test = false;
}
//prepare questions
if($continue_test==false) {
	$query = "SELECT * FROM cbt_questions WHERE cbt_id = '$cbt_id' ORDER BY RAND() LIMIT $course[question_limit]";
	$result2 = mysqli_query($server,$query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result2);
	if($num >= $course['question_limit']) {
		mysqli_query($server,"DELETE FROM cbt_answers WHERE student_id = '$student_id' AND cbt_id = '$cbt_id'");  
		$answer_date = time();
		while($row2 = mysqli_fetch_assoc($result2)){
			$question_id = $row2['id'];
			$cbt_id = $row2['cbt_id'];
			mysqli_query($server,"INSERT INTO cbt_answers (`answer_date`, `question_id`, `cbt_id`, `student_id`, `competion_time`) VALUES ('$answer_date', '$question_id', '$cbt_id', '$student_id', '$answer_date');") or die(mysqli_error($server));
		}
	}
} else {
	if(cbtFinishTime($student_id,$cbt_id) != cbtStartTime($student_id,$cbt_id)) {
		define("STARTED",$course['id']);	
	}
}
$start = true;;
$query = "SELECT * FROM cbt_answers WHERE cbt_id = '$cbt_id' AND student_id = '$student_id' ORDER BY RAND()";
$result3 = mysqli_query($server, $query);
$num = mysqli_num_rows($result3);
if($num < "1")	{
	$start = false;
	$message = "We couldn't prepare your test questions at this time. Please try again later";
	$class="blue";
}
if(countCBTQuestions($cbt_id) < $course['question_limit']) {
	$start = false;
	$message = "We couldn't serve your test questions at this time. Please try again later";
	$class="blue";
}
?>
<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div class="panel" style="border-color: transparent;">
<?php if($start ==true) {?>   
<style>
body {overflow-x:hidden;}
p {
  font-size: 14px;
  font-weight: bold;
}

.container2 {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 400;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
.container2 input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 20px;
  width: 20px;
  background-color: #eee;
  border: 1px solid #036;
  border-radius: 50%;
}
.container2:hover input ~ .checkmark {
  background-color: #ccc;
}
.container2 input:checked ~ .checkmark {
  background-color: #2196F3;
}
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}
.container2 input:checked ~ .checkmark:after {
  display: block;
}
.container2 .checkmark:after {
 	top: 9px;
	left: 9px;
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: white;
}
</style>
	<div class="row">
		<div class="alert alert-info">
             <h4><strong>Test</strong>: <?=$course['title']?></h4>
             <h4><strong>Questions</strong>: <?=$course['question_limit']?></h4>
             <h4><strong>Time Left</strong>: <span id="cbttime"><?=gmdate("H:i:s",$course['time_duration'])?></span> seconds</h4>
             <strong>Instructions: </strong><?=filterinp($course['description'],false)?>
        </div>
     </div>   
     <div class="panel-body">
     	<button onclick="finishTest('<?=$course['id']?>');" class="btn btn-success"><i class="fa fa-check"></i> Finish Test</button>
      	<table width="100%" border="0" cellspacing="0" cellpadding="0">
           <?php $i=1; $bfas = '';
			while($row = mysqli_fetch_assoc($result3)){
				$id = $row['question_id']; 	$answers = "";
				$resultd = mysqli_query($server, "SELECT * FROM cbt_choices WHERE question_id = '$id'");
				while($rowd = mysqli_fetch_assoc($resultd)){ $answers .= '<div class="col-sm-12 col-md-4"><label class="container2">'.$rowd['answer'].' <input type="radio" name="answer'.$row['question_id'].'" value="'.$rowd['id'].'" onchange="submitAnswer(\''.$course['id'].'\',\''.$row['question_id'].'\',\''.$rowd['id'].'\')"> <span class="checkmark"></span></label></div>';}
			?>
          <tr class="inner">
            <td><p>Q<?=$i?>, &nbsp;<?=getQuestions($row['question_id'])?></p>
            <div class="row"><?=$answers?></div></td>
          </tr>
          <?php	$i++;} ?>
          </table>
          <button onclick="finishTest('<?=$course['id']?>');" class="btn btn-success"><i class="fa fa-check"></i> Finish Test</button>
    </div>
    <?php if($start==true) {
		if($continue_test==false || cbtFinishTime($student_id,$cbt_id)==cbtStartTime($student_id,$cbt_id)) {define("TIMER",$course['time_duration']);	?>
    <div id="starter" style="?=overflow-x: hidden; width: 99.99%; height: 99.99%; z-index: 1000; position: fixed; top: 0; left: 0; background: rgba(0,0,0,1);">
       	 <div class="row"><br><br>
           	<div class="col-md-1 col-sm-1 col-xs-1"> </div>
            <div class="col-md-10 col-sm-10 col-xs-10"> 
				<div class="alert alert-info">
                  <h3>Your online test questions are ready!</h3>
                  <h3>You will be given <strong><?=gmdate("H",$course['time_duration']).' hours '.gmdate("i",$course['time_duration']).' minutes '.gmdate("s",$course['time_duration'])?> seconds</strong> to answer all <strong><?=$course['question_limit']?></strong> questions on this test</h3>
                  <h3>Click the start button below when you are ready to start</h3><br>
                  <button onclick="startTest('<?=$course['id']?>');" class="btn btn-success btn-lg">Start Test</button>
                </div>
             </div>
             <div class="col-md-1 col-sm-1 col-xs-1"> </div>
        </div>
      </div> <?php } else { 
	  $TIMER = time()-cbtStartTime($student_id,$cbt_id);
	  $TIMER = $course['time_duration']-$TIMER;
	  define("TIMER",$TIMER);}}?>    
<?php } else { ?>
		<div class="panel-body">
        	<div class="alert alert-warning">
              <h3>Unable to start test!</h3> <p><?=$message?></p><a href="usercgt"><br><button class="btn btn-primary">Back to Tests</button></a>
            </div>
        </div>
<?php } ?>        
    </div>
</div>
