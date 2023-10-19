<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		fee.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			28/02/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) > 3 && userRole($userID) != 4) {
header('location: admin.php');
}

	$session = getSetting('current_session');
	$term = getSetting('current_term');
	$class="0";


//start initiation
if(isset($_GET['id'])) {
	$view = $_GET['id'];
	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

	$sql=$query="SELECT * FROM assignments WHERE id = '$view' AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$assignment = $row['id'];
	$currentTerm = $row['term_id'];
	$class = $row['class_id'];
	$subject = $row['subject_id'];
	$duedate = $row['close_date'];
	$title = $row['title'];
	$text = $row['text'];
	if($assignment < 1) {
		header('location: Assignment.php');
	}
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
	         $clauses[] = "s.first_name LIKE '%$term%' OR s.last_name LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "s.first_name LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql = "select * FROM student_assignments f JOIN students s ON s.id = f.student_id WHERE f.id = '$assignment'AND $filter";

 	$resultF = mysqli_query($server, $sql) or die(mysqli_error($server));
	$numF = mysqli_num_rows($resultF);

	if($numF < "1")
		{
		$message = "No match found! Please try another search term.";
		$class="blue";
		}
} else {
	$sql = "SELECT * FROM student_assignments WHERE assignment_id = '$assignment' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$resultF = mysqli_query($server, $sql) or die(mysqli_error($server));
	$numF = mysqli_num_rows($resultF);
	if($numF < "1")
		{
		$message = "There are no answers for this assignment yet";
		$class="blue";
		}
}

?>
<div class="wrapper" style="padding: 10px; border: 1px solid #ddd;">
<blue>Assignment: </blue><?=$title?> (<?php echo countAssignment($assignment); ?> answers so far)
<br><blue>Class: </blue><?=className($class)?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<blue>Subject: </blue><?php echo subjectName($subject); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<blue>Due Date: </blue><?php echo realDate($duedate); ?>
<hr><div style="font-weight: normal;"><?=$text?> </div>
</div>
<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <input type="search" name="keyword" placeholder="Search" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <a href="admin/assignment"><button type="button" class="submit">Back</button></a>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="40%" scope="col">Student</th>
                <th width="15%" scope="col">Class</th>
                <th width="15%" scope="col">Answered On</th>
                <th width="15%" scope="col">Action</th>
              </tr>
             </table>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$student = $row['student_id'];
					$date = $row['date'];
					$url = $row['file'];
					$est = $array = end(explode('.', $url));
					?>
             <div class="virtualpage hidepeice">
             <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner">
                <td width="40%"> <?php echo studentName($student); ?></td>
                <td width="15%"> <?php echo className(getClass($row['student_id'],$currentSession)); ?></td>
                <td width="15%"> <?php echo realDate($date); ?></td>
                <td width="15%" valign="middle">
                <a href="<?php echo 'media/uploads/'.$url;?>" download><button class="success"> Download Answer (<?=strtoupper($est)?>)</button></a>
                </td>
              </tr>
              </table>
              </div>
              <?php
					$i++;
				} ?>

<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
</div>
