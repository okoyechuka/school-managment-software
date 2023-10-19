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

if(isset($_REQUEST['result'])&& is_numeric($_REQUEST['result'])){
	$book = filterinp($_REQUEST['result']);
	$sql=$query="SELECT * FROM cbt WHERE id = '$book' AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$ratio = round(countCorrectAnswered($student_id,$book)/$row['question_limit'],2);
	$scorePerc = ($ratio*100);
	$passed = "<red>Failed</red>";
	if($scorePerc >=$row['pass_mark']) $passed = "<green>Passed</gree>";
?>
<div id="add-new">
   <div id="add-new-head"><?php echo shorten($row['title'],65); ?> Result
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
		<?php if(cbtFinished($student_id,$book) ) { ?>
             <div class="row">
                <div class="alert alert-info">
                     <h4><strong>Test</strong>: &nbsp;&nbsp;&nbsp;<?=$row['title']?></h4>
                     <h4><strong>Total Questions</strong>: &nbsp;<?=$row['question_limit']?></h4>
                     <h4><strong>Completion Time</strong>: &nbsp;<?=gmdate('H:i:s',cbtStudentTiming($student_id,$book))?></h4>
                     <h4><strong>Total Answered</strong>: &nbsp;<?=countAnsweredTaken($student_id,$book)?></h4>
                     <h4><strong>Correct Answers</strong>: &nbsp;<?=countCorrectAnswered($student_id,$book)?></h4>
                     <h4><strong>Score</strong>: &nbsp;<?=$scorePerc?>% (<?=$passed?>)</h4>
                </div>
             </div>
        <?php } else { ?>
        	<div class="row">
                <div class="alert alert-warning">
                     <h4>Oops!</h4>
                     <h4>Looks like you havn't completed this test yet</h4>
                </div>
             </div>
        <?php } ?>     
    </div>
</div>    
<?php 
} 


if(isset($_REQUEST['view']) && is_numeric($_REQUEST['view'])){
	$book = filterinp($_REQUEST['view']);
	$sql=$query="SELECT * FROM cbt WHERE id = '$book' AND school_id = '$school_id'";
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
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Assigned Teacher:</td>
        <td  align="left"><?=$row['teacher_id']>0?teacherName($row['teacher_id']):"Not Assigned" ?>     </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">No. of Questions:</td>
        <td  align="left" valign="middle"><?php echo @$row['question_limit']; ?> Questions per test       </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">Pass Mark (%):</td>
        <td  align="left" valign="middle"><?php echo @$row['pass_mark']; ?> Percent      </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">Test Duration:</td>
        <td  align="left" valign="middle"><?php echo gmdate('H:i:s',$row['time_duration']); ?> HH:MM:SS      </td>
      </tr>
            
      <tr>
        <td align="left" valign="middle">Repeat Allowed?:</td>
        <td><?=@$row['allow_repeat']=="0"?"No":'Yes'?>  </td>
      </tr>

      </table>
    </div>
</div>    
<?php 
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
	$sql=$query = "select a.* FROM cbt a LEFT JOIN class_course b ON a.course_id = b.course_id WHERE (a.course_id > 0 AND b.class_id = '$student_class') OR (a.course_id = 0) AND a.school_id = '$school_id' AND $filter ";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "select a.* FROM cbt a LEFT JOIN class_course b ON a.course_id = b.course_id WHERE (a.course_id > 0 AND b.class_id = '$student_class') OR (a.course_id = 0) AND a.school_id = '$school_id' ORDER BY a.id DESC LIMIT $pageLimit,$setLimit";	
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
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
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="35%" scope="col">Title</th>
                <th width="35%" scope="col">For</th>
                <th width="30%" scope="col">Action</th>
              </tr>
               <?php $i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$assigned = "Not Available";
					$show = true;
					if($row['exam_id'] > 0) {
						$assigned = "<strong>Exam</strong>: ".examName($row['exam_id'])." ".termName(examTerm($row['exam_id'])).' '.subjectName($row['subject_id']).' '.ucfirst($row['mark_type']);
						if(subjectClass($row['subject_id']) != $student_class) { $show = false; }
					}
					if($row['course_id'] > 0) {
						$assigned = "<strong>Course</strong>: ".courseName($row['course_id']);
					}
				?>
           <?php if($show==true) { ?>
              <tr class="inner">
                <td> <?php echo $row['title']; ?></td>
                <td> <?php echo $assigned; ?></td>
                <td valign="middle">
                <a href="usercbt?view=<?php echo $id;?>"><button>View Details</button></a>
                <?php if(strtotime($row['start_date'])<=time()) {?>
                <?php if(strtotime($row['expire_date'])<100||strtotime($row['expire_date'])>time()) {?>
					<?php if(cbtTaken($student_id,$id) < 1 || $row['allow_repeat']=="1") {?>
                    <a href="useronlinetest?cbt=<?php echo $id;?>"><button class="success">Start Test</button></a>
                    <?php } else { ?>
                        <?php if(cbtTaken($student_id,$id)>0 && (cbtStartTime($student_id,$id)+$row['time_duration'] > time()) && !cbtFinished($student_id,$id)) {?>
                        <a href="useronlinetest?cbt=<?php echo $id;?>"><button class="success">Resume Test</button></a>
                        <?php } ?>
                    <?php } ?>
                    <?php if(cbtFinished($student_id,$id) ) { ?>
                    <a href="usercbt?result=<?php echo $id;?>"><button>View Score</button></a>
                    <?php } ?>
                <?php } } ?>
                </td>
              </tr>
              <?php	} } ?>
              </table>
<?php displayPagination($setLimit,$page,$query) ?>
        </div>
    </div>
</div>
