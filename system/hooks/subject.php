<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		subject.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			22/09/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

$currentSession = getSetting('current_session');
$currentTerm = getSetting('current_term');

if(userRole($userID) > 4) {
	header('location: adashboard');
}

if(isset($_REQUEST['delete'])){
	$book = filterinp($_REQUEST['delete']);

	$sql=$query = "DELETE FROM subject WHERE id = '$book'  AND school_id = '$school_id'";
	$result = mysqli_query($server, $sql) or die(mysqli_error($server));

	$message = "The selected subject was successfully deleted.";
	$class="green";
}

function student_offerssubject2($subject,$student) {
	global $server;
	$result = mysqli_query($server, "SELECT * FROM subject_student WHERE subject_id = '$subject' AND student_id = '$student'") or die(mysqli_error($server));
	$row = mysqli_num_rows($result);
	if($row > 0) return true; 
	return false;
}


if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])){
	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM subject WHERE id = '$book'  AND school_id = '$school_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['$title']; ?>
<?php
	} else {
		$book = '';
		$exam_max = '70';
		$assessment_max = '30';
?>
<div id="add-new">
   <div id="add-new-head">Create New Subject
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/subject" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Subject ID:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="id" id="id" readonly="readonly" placeholder="Auto Assign" value="<?php echo @$row['id']; ?>">
        </td>
       </tr>

      <tr>
        <td align="left" valign="middle">Subject Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" id="title2" required="required" placeholder="" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assign to Class:</td>
      <td>
        <select name="class_id" id="e4" style="width: 90%;" class="subjclass">
            <option <?php if(@$row['class_id'] == 0) { echo 'selected';} ?> value="0">Assign to All Classes</option>
			<?php
                $sql=$query="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $result2 = mysqli_query($server, $query);
				while($rows = mysqli_fetch_assoc($result2)){
                $c_id = $rows['id'];
                $title = $rows['title'];
            ?>
               <option <?php if(@$row['class_id'] == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  }   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Upload Syllabus: <br /><small>Upload PDF file</small></td>
        <td  align="left" valign="middle">
        	<input type="file" accept="application/pdf" style="width: 70%;" name="content" id="content" placeholder="Upload course contents (PDF) " >
        </td>
      </tr>

        	<input type="hidden"  name="exam_max" value="<?php echo @$row['exam_max']; ?>">

        	<input type="hidden"  name="assessment_max" value="<?php echo @$row['assessment_max']; ?>">
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" id="c_subject" name="subject" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Subject</button>
        <?php } else { ?>
        <input type="hidden" id="c_subject" value="" />
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Subject</button>
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

	$sontent = '';
	if(!empty($_FILES['content']['name'])) {
		$upload_path = 'media/uploads/';
		$file2 = $_FILES['content']['name'];
		$content = date("d-m-Y").$_FILES['content']['name'];
		$ext = end(explode(".", $_FILES['content']['name']));
		$allowed = array("jpg","jpeg","gif","png","swf","bmp","","mpeg","flv","wav","mp4","mp3","exe","sldm","pub","accdb","dot","totx","docm","xlst","xlsm","pot","pps","doc","docx","pdf","zip","ppt","pptx","pptm","xps","xls","xlsx","csv");
		if(!in_array(strtolower($ext), $allowed)) {
			//This file format is not allowed
			$message = "The uploaded file format is not allowed"; $class = "red";
		} else {
			if(move_uploaded_file($_FILES['content']['tmp_name'],$upload_path . $filename) || ($file2 =="")){
				$content =  $filename;
			}
		}
	}

	//create new subect
	$sql=$query="INSERT INTO subject (`id`, `school_id`,`class_id`, `title`, `exam_max`, `assessment_max`, `content`) VALUES (NULL, '$school_id','$class_id', '$title','$exam_max','$assessment_max', '$content');";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$subject_id = mysqli_insert_id($server);
	//check specific students 

	$message = 'The new subject was succesfully created.';
	$class = 'green';

}

if(isset($_POST['save'])) {
	$subject = $_POST['subject'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "subject") && ($key != "save") && ($key != "id") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
			$sql=$query="UPDATE `subject` SET `$key` =  '$value' WHERE `id` = '$subject';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}

	if(!empty($_FILES['content']['name'])) {
		$upload_path = 'media/uploads/';
		$file2 = $_FILES['content']['name'];
		$filename = date("d-m-Y").$_FILES['content']['name'];
		$ext = end(explode(".", $_FILES['content']['name']));
		$allowed = array("jpg","jpeg","gif","png","swf","bmp","","mpeg","flv","wav","mp4","mp3","exe","sldm","pub","accdb","dot","totx","docm","xlst","xlsm","pot","pps","doc","docx","pdf","zip","ppt","pptx","pptm","xps","xls","xlsx","csv");
		if(!in_array(strtolower($ext), $allowed)) {
			//This file format is not allowed
			$message = "The uploaded file format is not allowed"; $class = "red";
		} else {
			if(move_uploaded_file($_FILES['content']['tmp_name'],$upload_path . $filename) || ($file2 =="")) {
				$sql=$query="UPDATE `subject` SET `content` =  '$filename' WHERE `id` = 'subject';";
				mysqli_query($server, $query);
			}
		}
	}

	$message = 'The selected subject was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['teacher'])){
if(userRole($userID) > 2) {
header('location: admin.php');
}

	$subject = $_GET['teacher'];
?>

<div id="add-new">
   <div id="add-new-head">Assign Teacher

            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/subject" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">

      <tr>
        <td align="left" valign="middle">Selected Subject:</td>
      <td>
        <select name="sub" id="e4" style="width: 90%;" >
               <option  value="<?php echo $subject; ?>"><?php echo subjectName($subject); ?></option>
		</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assign Teacher:</td>
        <td>
        <select name="teacher" id="e2" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM teachers WHERE school_id = '$school_id' ORDER BY first_name ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $c_id = $row['id'];
				$title = teacherName($row['id']);
            ?>
               <option <?php if(getSubjectTeacher($subject) == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  $i++;
								}   ?>
			</select>
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="assign" value="yes" />
        <input type="hidden" name="subject" value="<?php echo $subject; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Save Changes</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Changes...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}


if(isset($_POST['assign'])){
	$subject = $_POST['subject'];
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

		$sql=$query="SELECT * FROM teacher_subject WHERE subject_id = '$subject'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$found = mysqli_num_rows($result);

		if($found > 0) {
			$sql=$query="UPDATE `teacher_subject` SET `teacher_id` =  '$teacher' WHERE `subject_id` = '$subject';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		} else {
			$sql=$query="INSERT INTO teacher_subject (`id`, `teacher_id`, `subject_id`) VALUES (NULL, '$teacher', '$subject');";
			$query = mysqli_query($server, $query) or die(mysqli_error($server));
		}


	$message = 'Your changes was successfully saved.';
	$class = 'green';
}

if(isset($_GET['keyword'])){
$category = filterinp($_GET['category']);
$school_id = $school_id;
$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', $term);
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look){
	    $term = trim($look);
	    if (!empty($term))    {
	         $clauses[] = "p.title LIKE '%$term%' ";
	    }    else    {
	         $clauses[] = "p.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM subject p JOIN classes c ON c.id = p.class_id WHERE p.school_id = '$school_id' AND $filter LIMIT $pageLimit,$setLimit";

	if(userRole($userID) > 2) {
	$class_id = getTeacherClass(userProfile($userID));
	$sql=$query = "select * FROM subject p JOIN classes c ON c.id = p.class_id WHERE p.school_id = '$school_id' AND p.class_id = '$class_id' AND $filter  LIMIT $pageLimit,$setLimit";
	}

 	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "SELECT * FROM subject WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";

	if(userRole($userID) > 2) {
	$class_id = getTeacherClass(userProfile($userID));
	$sql=$query = "SELECT * FROM subject WHERE school_id = '$school_id' AND class_id = '$class_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	}

	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no subjects created for your school";
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
        <input type="search" name="keyword" placeholder="Search Subject" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/subject?new"><button type="button" class="submit">Add <hide>New</hide></button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
<?php
if(userRole($userID) > 2) {
?>
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="25%" scope="col">Title </th>
                <th width="15%" scope="col">Class</th>
                <th width="20%" scope="col">Students</th>
                <th width="30%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$content = $row['content'];
					$class = className($row['class_id']);
					$teacher = $available = countClass($id,$currentSession).' Student(s)';

				?>
              <tr class="inner">
                <td width="10%"> <?php echo sprintf('%05d',$id); ?></td>
                <td width="25%"> <?php echo $title; ?> (<?=className($row['class_id'])?>)</td>
                <td width="15%"> <?php echo $class; ?></td>
                <td width="20%"> <?php echo $teacher; ?></td>
                <td width="30%" valign="middle">
                <?php if(!empty($content)) { ?>
                <a href="media/uploads/<?php echo $content;?>"><button>Download Syllabus</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php
						$i++;
					} ?>
              </table>
<?php 	} else { ?>
        	<table ="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="20%" scope="col">Title </th>
                <th width="15%" scope="col">Class</th>
                <th width="20%" scope="col">Teacher</th>
                <th width="35%" scope="col">Action</th>
              </tr>
               <?php

			 	$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$content = $row['content'];
					$class = className($row['class_id']);
					if($class<0) {
						$class = 'Not assigned';
					}
					$teacher = teacherName(getSubjectTeacher($id));
					if(empty($teacher)||$teacher==" ") $teacher = 'Un-assigned';

				?>
              <tr class="inner">
                <td width="10%"> <?php echo $id; ?></td>
                <td width="20%"> <?php echo $title; ?></td>
                <td width="15%"> <?php echo $class; ?></td>
                <td width="20%"> <?php echo $teacher; ?></td>
                <td width="35%" valign="middle">
                <?php if(!empty($content)) { ?>
                <a href="media/uploads/<?php echo $content;?>"><button class="success">Syllabus</button></a>
                <?php } if(userRole($userID) < 3) {  ?>
                <a href="admin/subject?edit=<?php echo $id;?>"><button class="warning">Edit</button></a>
                <a href="admin/subject?teacher=<?php echo $id;?>"><button>Assign Teacher</button></a>
                <a href="admin/subject?delete=<?php echo $id;?>"><button class="danger">Remove</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php
						$i++;
					} ?>
              </table>
<?php } ?>

<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
</div>
