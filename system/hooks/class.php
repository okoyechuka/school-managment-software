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

if(userRole($userID) > 2) {
header('location: adashboard');
}
if(isset($_REQUEST['delete'])){
	$book = filterinp($_REQUEST['delete']);
	if(!classHasStudent($book, getSetting('current_session'))) {
		$sql=$query = "DELETE FROM classes WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));

		$message = "The selected class was successfully deleted.";
		$class="green";
	} else {
		$message = "Sorry but you cannot delete this class as students are currently assigned to the selected class.";
		$class="yellow";
	}
}




if(isset($_REQUEST['edit']) || isset($_REQUEST['new']))
{
	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM classes WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['$title']; ?>
<?php
	} else {
		$book = '';

?>
<div id="add-new">
   <div id="add-new-head">Create New Class
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/class" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Class ID:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="id" id="id" readonly="readonly" placeholder="Auto Assign" value="<?php echo @$row['id']; ?>">
        </td>
       </tr>
      <tr>
        <td align="left" valign="middle">Class Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" id="name" required="required" placeholder="" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="class" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Class</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Class</button>
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

if(isset($_POST['add']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

		//create new prents
			$sql=$query ="INSERT INTO classes (`id`, `school_id`, `title`) VALUES (NULL, '$school_id', '$title');";
			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new class was succesfully created.';
	$class = 'green';

}


if(isset($_GET['teacher']))
{
if(userRole($userID) > 2) {
header('location: index.php');
}

	$class = $_GET['teacher'];
?>

<div id="add-new">
   <div id="add-new-head">Assign Teacher

            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/class" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">

      <tr>
        <td width="20%" align="left" valign="middle">Selected Class:</td>
      <td>
        <select name="sub" id="e4" style="width: 50%;" >
               <option  value="<?php echo $class; ?>"><?php echo className($class); ?></option>
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
               <option <?php if(getClassTeacher($class) == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php
										$i++;
								}   ?>
			</select>
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="assign" value="yes" />
        <input type="hidden" name="class" value="<?php echo $class; ?>" />
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


if(isset($_POST['assign']))
{
	$class = $_POST['class'];
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

		$sql=$query="SELECT * FROM teacher_class WHERE class_id = '$class'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$found = mysqli_num_rows($result);

		if($found > 0) {
			$sql=$query="UPDATE `teacher_class` SET `teacher_id` =  '$teacher' WHERE `class_id` = '$class';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		} else {
			$sql=$query="INSERT INTO teacher_class (`id`, `teacher_id`, `class_id`) VALUES (NULL, '$teacher', '$class');";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}


	$message = 'Your changes was successfully saved.';
	$class = 'green';
}


if(isset($_POST['save']))
{
$class = $_POST['class'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "id") && ($key != "save") && ($key != "photo") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
			$sql=$query="UPDATE `classes` SET `$key` =  '$value' WHERE `id` = '$class';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}



	$message = 'The selected class was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword']))
{
$category = filterinp($_GET['category']);

$school_id = $school_id;

$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', $term);
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)	{
	    $term = trim($look);
	    if (!empty($term))	    {
	         $clauses[] = "p.title LIKE '%$term%'";
	    }    else    {
	         $clauses[] = "p.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM classes p WHERE p.school_id = '$school_id' AND $filter LIMIT $pageLimit,$setLimit";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")		{
		$message = "There are currently no classes created for your school";
		$class="blue";
	}
}

if(isset($_REQUEST['msg'])) {
	$message = 'This seem to be your first time. You need to create classes for your school before you continue';
	$class = 'blue';
}

?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <input type="search" name="keyword" placeholder="Search Class" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/class?new"><button type="button" class="submit">Add <hide>New</hide></button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="5%" scope="col">ID</th>
                <th width="15%" scope="col">Class Name</th>
                <th width="20%" scope="col">Teacher</th>
                <th width="15%" scope="col">Students Count</th>
                <th width="40%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$teacher = teacherName(getClassTeacher($id));
					if(empty($teacher)||$teacher==" ") $teacher = 'Un-assigned';
					$available = countClass($id,$currentSession).' Student(s)';

				?>
              <tr class="inner">
                <td width="5%"> <?php echo $id; ?></td>
                <td width="15%"> <?php echo $title; ?></td>
                <td width="20%"> <?php echo $teacher; ?></td>
                <td width="15%"> <?php echo $available; ?></td>
                <td width="40%" valign="middle">
                <a href="admin/student?keyword&session=<?php getSetting('current_session');?>&class=<?php echo $id;?>"><button>View Students</button></a>
                <a href="admin/class?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/class?teacher=<?php echo $id;?>"><button class="success">Assign Teacher</button></a>
                <a href="admin/class?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
                <a href="admin/sms?class=<?php echo $user;?>"><button>SMS</button></a>
                <a href="admin/email?class=<?php echo $user;?>"><button>Email</button></a>
                </td>
              </tr>
              <?php
					$i++;
				} ?>
              </table>

<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
</div>