<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		fee.php
Description:	This is the parent page
Date: 			28/02/2017
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

$session = getSetting('current_session');
$term = getSetting('current_term');

if( (!isset($_REQUEST['student_id']) || $_REQUEST['student_id'] < 1 )) {
if(userRole($userID) == 5) {
	$parent = userProfile($userID);
} else {
	$student = userProfile($userID);
	$parent = $student;
	header('location: userassignment?student_id='.$student);
}

?>
<div id="add-new">
   <div id="add-new-head">Choose a Student's Profile to View Assignments
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="get" action="userassignment" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>

       <tr>
        <td  align="left" valign="middle">Select Student:</td>
        <td  align="left" valign="middle">
        <select name="student_id" id="e1" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM students s JOIN student_parent sp ON sp.student_id = s.id WHERE s.school_id = '$school_id' AND (sp.parent_id = '$parent' OR sp.student_id = '$student') ORDER BY s.first_name ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);
				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title0 = $row['first_name']. ' '.$row['last_name']. ' '.$row['other_name'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(@$student_id == $g_id) { echo 'selected'; } ?>><?php echo $title0; ?></option>
            <?php  	}   ?>
			</select>
        </td>
      </tr>
      <tr>
      <td  align="left" valign="middle"></td>
        <td  align="left" valign="middle">
      <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" type="submit">Get Assignments</button>
      </td>
      </tr>
      </table>
      </form>
      </div>
      </div>
<?php
} else {
if(userRole($userID) == 5) {
	$parent = userProfile($userID);
} else {
	$student_id = $student = userProfile($userID);
	$parent = $student;
}
	$_SESSION['student'] = $student_id = filterinp($_REQUEST['student_id']);
	$student = $student_id;


//start initiation
if(isset($_GET['answer'])) {
	$student_id = $_SESSION['student'];
	$assignment = mysqli_real_escape_string($server, $_GET['answer']);
?>
<div id="add-new">
   <div id="add-new-head">Answer Assignment
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="POST" action="userassignment" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>

 	  <tr>
        <td align="left" valign="middle">Upload Answer:</td>
        <td  align="left" valign="middle">
        	<input type="file" name="answer" id="answer" required="required" style="width: 70%;" >
      </td>
      </tr>

      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;
        <input type="hidden" name="assignment_id" value="<?=$assignment?>" />
        <input type="hidden" name="student_id" value="<?=$student_id?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Submit Answer</button>

     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving ...</div>
        </td>
      </tr>
      </form>
    </table>

	</div>
</div>
<?php
}

if(isset($_GET['view']))
{
	$student_id = $_SESSION['student'];
	$assignment = filterinp($_GET['view']);
	$sql=$query="SELECT * FROM assignments WHERE id = '$assignment' AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$assignment = $row['id'];
	$session_id = $row['session_id'];
	$term_id = $row['term_id'];
	$class = $row['class_id'];
	$subject = $row['subject_id'];
	$duedate = $row['close_date'];
	$title = $row['title'];
	$text = $row['text'];
	if($assignment < 1) {
		header('location: userassignmebt');
	}
?>
<div id="add-new">
   <div id="add-new-head">View Assignment
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="GET" action="userassignment?answer=<?=$assignment?>&student_id=<?=$student_id?>" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
 	  <tr>
        <td style="width:30%" align="left" valign="middle">Assignment:</td>
        <td  align="left" valign="middle"><?=$title?></td>
      </tr>
 	  <tr>
        <td align="left" valign="middle">Session:</td>
        <td  align="left" valign="middle"><?=sessionName($session_id)?></td>
      </tr>
 	  <tr>
        <td align="left" valign="middle">Term:</td>
        <td  align="left" valign="middle"><?=termName($term_id)?></td>
      </tr>
 	  <tr>
        <td align="left" valign="top">Details:</td>
        <td  align="left" style="font-weight: normal;" valign="middle"><?=$text?></td>
      </tr>

      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;
        <?php
		if(strtotime(date('Y-m-d')) < strtotime($row['close_date'])) { ?>
        <input type="hidden" name="answer" value="<?=$assignment?>"/>
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="student_id" value="<?=$student_id?>" type="submit">Answer</button>
        <?php } ?>
        </form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Wait...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}


if(isset($_POST['assignment_id']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$upload_path = 'media/uploads/';
	$answer = $_FILES['answer']['name'];
	$filename = time().$_FILES['answer']['name'];
	$ext = strtolower(end(explode(".", $_FILES['answer']['name'])));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","","mpeg","flv","wav","mp4","mp3","exe","sldm","pub","accdb","dot","totx","docm","xlst","xlsm","pot","pps","doc","docx","pdf","zip","ppt","pptx","pptm","xps","xls","xlsx","csv");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
	} else {
		$date = date('Y-m-d');
		if(move_uploaded_file($_FILES['answer']['tmp_name'],$upload_path . $filename)) 	{
			mysqli_query($server,"DELETE FROM student_assignments WHERE assignment_id = '$assignment_id' AND student_id = '$student_id'");
			$sql=$query = "INSERT INTO student_assignments (`id`, `assignment_id`, `student_id`, `date`, `file`)
			VALUES (NULL, '$assignment_id', '$student_id', '$date', '$filename');";
			mysqli_query($server, $query) or die(mysqli_error($server));
		//inserted
			$message = 'Good Job!. Your answer has been successfully submitted to the subject teacher.';
			$class = 'green';
		} else {
			//upload error
			$message = 'Unable to submit your answer. An error was encountered while uploading your file. Please try again.';
			$class = 'red';
		}
	}
}

$class = getClass($student_id,$session);
if(isset($_GET['keyword']))
{
$term = filterinp($_GET['term_id']);
$session = filterinp($_GET['session_id']);

$school_id = $school_id;

$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', mysqli_real_escape_string($server, $_GET['keyword']));
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "f.title LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "f.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM assignments f JOIN classes c ON c.id = f.class_id JOIN terms t ON t.id = f.term_id JOIN student_class scl ON scl_class_id = c.class_id JOIN students st ON st.id = scl.student_id JOIN student_parent spe ON spe.student_id = st.id WHERE a.school_id = '$school_id' AND (spe.student_id = '$student' OR spe.parent_id = '$parent') AND $filter AND f.class_id = '$class' AND f.session_id = '$session' AND f.term_id = '$term'";

 	$resultF = mysqli_query($server, $sql) or die(mysqli_error($server));
	$numF = mysqli_num_rows($resultF);

	if($numF < "1")
		{
		$message = "No match found! Please try another search term.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM assignments f JOIN student_class sc ON (f.class_id = sc.class_id OR f.class_id = 0) JOIN student_parent sp ON sp.student_id = sc.student_id WHERE f.school_id = '$school_id' AND (sc.student_id = '$student' OR sp.parent_id = '$parent' OR f.class_id = 0) GROUP BY f.id ORDER BY f.title ASC";

	$resultF = mysqli_query($server, $sql) or die(mysqli_error($server));
	$numF = mysqli_num_rows($resultF);
	if($numF < "1")
		{
		$message = "There are no assignments for you at this time";
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
        &nbsp;
           <select name="session_id" id="e1" style="" >
        	<option value="0" <?php if($session == 0) {echo 'selected';} ?>>All Sessions</option>
			<?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY start_date DESC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($session == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php  $i++;
								}   ?>
			</select>
		&nbsp;
        <select name="term_id" id="e2" style="" >
       		 <option value="0" <?php if($term == 0) {echo 'selected';} ?>>All Terms</option>
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($tern == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php  $i++;
								}   ?>
			</select>
            &nbsp;
        <input type="search" name="keyword" placeholder="Search Assignments" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="40%" scope="col">Assignment</th>
                <th width="20%" scope="col">Due Date</th>
                <th width="15%" scope="col">Status</th>
                <th width="15%" scope="col"></th>
              </tr>
               <?php

				 $i=0;
 				while($row = mysqli_fetch_assoc($resultF)){
					$id = $row['id'];
					$title = $row['title'];
					$close_date = $row['close_date'];
					$status = assignmentAnswered($student,$id);
					$actions = '<a href="userassignment?view='.$id.'&student_id='.$_SESSION['student'].'"><button>View</button></a> ';
					$actions .= '<a href="userassignment?answer='.$id.'&student_id='.$_SESSION['student'].'"><button class="btn-success">Answer</button></a> ';
					$tag1 = ''; $tag2 = '';
					if($status == 'Answered') {
						$actions = '<a href="userassignment?view='.$id.'"><button>View</button></a> ';
						$actions .= '<a href="userassignment?answer='.$id.'&student_id='.$_SESSION['student'].'" onclick="alert(\'Note that submitting a new answer will replace your current answer for this assignment\')"><button class="btn-success">Answer</button></a> ';
					}
					if(strtotime(date('Y-m-d')) > strtotime($close_date)) {
						$tag1 = '<red>'; $tag2 = '</red>';
						$actions = '<a href="userassignment?view='.$id.'&student_id='.$_SESSION['student'].'"><button>View</button></a> ';
						$actions .= '<a href="#" onclick="alert(\'Sorry but submission of answers for this assignment has closed as of '.realDate($close_date).'. Please contact the subject teacher\')"><button class="btn-success">Answer</button></a> ';
					}

				?>
              <tr class="inner">
                <td width=""> <?php echo $title; ?></td>
                <td width=""> <?php echo $tag1.realDate($close_date).$tag2; ?></td>
                <td width=""> <?php echo $status; ?></td>
                <td width="" valign="middle"><?=$actions?></td>
              </tr>
              <?php $i++;	} ?>
              </table>

<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
</div>
<?php } ?>
