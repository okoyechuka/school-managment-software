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
	if(userRole($userID) < 3) {
		$sql=$query = "DELETE FROM e_courses WHERE id = '$book' AND school_id = '$school_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));

		$message = "The selected online course was successfully deleted.";
		$class="green";
	} else {
		$message = "Sorry but you need admin role to perform this action.";
		$class="yellow";
	}
}

if(isset($_REQUEST['view']) && is_numeric($_REQUEST['view'])){
	$book = filterinp($_REQUEST['view']);
	$sql=$query="SELECT * FROM e_courses WHERE id = '$book' AND school_id = '$school_id'";
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
        <td align="left" colspan="2" valign="middle"><strong><blue>Course Details</blue></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Course Title:</td>
        <td  align="left" valign="middle"><?php echo $row['title']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Start Date:</td>
        <td  align="left" valign="middle">	<?php echo empty($row['start_date'])?'Any Date':$row['start_date']; ?>  </td>
      </tr>
      <tr>
        <td align="left" valign="middle">End Date:</td>
        <td  align="left" valign="middle"><?php echo empty($row['expire_date'])?'Never':$row['expire_date']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assigned Teacher:</td>
        <td  align="left"><?=$row['teacher_id']>0?teacherName($row['teacher_id']):"Not Assigned" ?>     </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assigned Classes:</td>
        <td  align="left">
        <?php
            $result = mysqli_query($server,"SELECT * FROM class_course WHERE course_id = '$row[id]'");
			while($rows = mysqli_fetch_assoc($result)){ $titles = className($rows['class_id']);
			echo '<span class="badge badge-success">'.$titles.'</span>&nbsp;&nbsp;&nbsp;';
          	 } ?>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Cost:</td>
        <td  align="left"><?=$row['fee_id']>0?$userSymbul.feeTotal($row['fee_id']):"Free" ?>     </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Lessons:</td>
        <td  align="left"><?=countCourseLeason($row['id'])?> Lessons     </td>
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

if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])){
	if(userRole($userID) > 2) {
		header('location: courses');
	}
	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM e_courses WHERE id = '$book' AND school_id = '$school_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['title']; ?>
<?php	} else {
		$book = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Course
<?php
	}
?>
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
    <form method="post" action="admin/courses" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Course Title:</td>
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
        <td align="left" valign="middle">Assign Teacher:</td>
        <td>
        <select name="teacher_id" id="e2" style="width: 90%;" >
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
      <tr>
        <td align="left" valign="middle">Assign Fee:</td>
        <td>
        <select name="fee_id" id="e3" style="width: 90%;" >
        	<option value="0">Unassigned</option>
			<?php
                $query="SELECT * FROM fees WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
				while($rows = mysqli_fetch_assoc($result)){
                $g_id = $rows['id']; $title1 = $rows['title'];
            ?>
               <option data-amount="<?=$row['amount']?>" value="<?php echo $g_id; ?>" <?php if(@$row['fee_id']==$g_id) { echo 'selected'; } ?>><?php echo $title1; ?></option>
            <?php }   ?>
			</select>
        </td>
      </tr>
      
      <tr>
        <td align="left" colspan="2" valign="middle">What class can access this course?:</td>
      </tr>
      <tr>
        <td colspan="2" width="75%" align="left" valign="middle">
			<?php
                $query="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
				while($rows = mysqli_fetch_assoc($result)){
            ?>
            <?=$rows['title']?> <input name="class_id[]" type="checkbox" value="<?=$rows['id']?>" <?php if (classsOfferCourse($rows['id'],$row['id'])) {echo'checked="checked"';} ?> />&nbsp;&nbsp;&nbsp;&nbsp;        
        <?php } ?>
        </td>
      </tr>
      
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue>Introduction:</strong></td>
      </tr>
      <tr>
        <td align="left" valign="top" colspan="2">
        	<textarea placeholder="Type course overview here" id="textMessage" class="ckeditor" name="description"  style="height: 400px; width:98%;" required ><?php echo @$row['description']; ?>&nbsp;</textarea>
      </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="class" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Course</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Course</button>
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
	$description = filterinp($_POST['description'],true);
	//create new prents
	$query ="INSERT INTO e_courses (`description`, `start_date`, `expire_date`, `fee_id`, `teacher_id`, `school_id`, `title`) VALUES ('$description', '$start_date', '$expire_date', '$fee_id', '$teacher_id', '$school_id', '$title');";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$course_id = mysqli_insert_id($server);
	//insert class
	mysqli_query($server,"DELETE FROM class_course WHERE course_id = '$course_id'");
	foreach($_POST['class_id'] as $class_id) {
		$class_id = filterinp($class_id);
		mysqli_query($server,"INSERT INTO class_course (`course_id`, `class_id`) VALUES ('$course_id', '$class_id');") or die(mysqli_error($server));
	}
	$message = 'The new online course was succesfully created. <a href="admin/coursecontent?course='.$course_id.'">Click Here</a> to manage leasons now';
	$class = 'green';
}

if(isset($_POST['save'])){
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$description = filterinp($_POST['description'],true);	
	$query="UPDATE `e_courses` SET 
	`description` =  '$description' ,
	`start_date` =  '$start_date' ,
	`expire_date` =  '$expire_date' ,
	`fee_id` =  '$fee_id' ,
	`teacher_id` =  '$teacher_id' ,
	`title` =  '$title' 
	WHERE `id` = '$class';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	mysqli_query($server,"DELETE FROM class_course WHERE course_id = '$class'");
	foreach($_POST['class_id'] as $class_id) {
		$class_id = filterinp($class_id);
		mysqli_query($server,"INSERT INTO class_course (`course_id`, `class_id`) VALUES ('$class', '$class_id');") or die(mysqli_error($server));
	}
	$message = 'The selected online course was succesfully updated.';
	$class = 'green';
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
	$sql=$query = "select * FROM e_courses WHERE school_id = '$school_id' $addedT AND $filter ";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "SELECT * FROM e_courses WHERE school_id = '$school_id' $addedT ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")		{
		$message = "There are currently no online courses created for your school";
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
        <input type="search" name="keyword" placeholder="Search Courses" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/courses?new"><button type="button" class="submit">Add <hide>New</hide></button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="5%" scope="col">ID</th>
                <th width="30%" scope="col">Title</th>
                <th width="10%" scope="col">Start Date</th>
                <th width="10%" scope="col">End Date</th>
                <th width="10%" scope="col">Cost</th>
                <th width="10%" scope="col">Lessons</th>
                <th width="30%" scope="col">Action</th>
              </tr>
               <?php $i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];	$title = $row['title'];
				?>
              <tr class="inner">
                <td> <?php echo $id; ?></td>
                <td> <?php echo $title; ?></td>
                <td> <?php echo empty($row['start_date'])?'Any Date':$row['start_date']; ?></td>
                <td> <?php echo empty($row['expire_date'])?'Never':$row['expire_date']; ?></td>
                <td> <?php echo $row['fee_id']>0?$userSymbul.number_format(feeTotal($row['fee_id']),2):'Free'; ?></td>
                <td> <?php echo countCourseLeason($id); ?></td>
                <td valign="middle">
                <a href="admin/courses?view=<?php echo $id;?>"><button>View Details</button></a>
                <a href="admin/coursecontent?course=<?php echo $id;?>"><button class="success">Lessons</button></a>
                <?php if(userRole($userID) < 3) { ?>
                <a href="admin/courses?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/courses?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php	$i++; } ?>
              </table>
<?php displayPagination($setLimit,$page,$query) ?>
        </div>
    </div>
</div>
