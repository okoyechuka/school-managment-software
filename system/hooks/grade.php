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

if(userRole($userID) > 2) {
	header('location: admin.php');
}

if(isset($_REQUEST['delete'])){
	$book = filterinp($_REQUEST['delete']);
	$sql=$query = "DELETE FROM grades WHERE id = '$book'  AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));

	$message = "The selected class was successfully deleted.";
	$class="green";
}

if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])){
	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
	$sql=$query="SELECT * FROM grades WHERE id = '$book'  AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update Grade
<?php
	} else {
		$book = '';
		$start_mark = '0.00';
		$end_mark = '0.00';
?>
<div id="add-new">
   <div id="add-new-head">Create Grade
<?php
	}
?>
   <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/grade" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Grade ID:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="id" id="id" readonly="readonly" placeholder="Auto Assign" value="<?php echo @$row['id']; ?>">
        </td>
       </tr>
	  
      <tr>
        <td align="left" valign="middle">Grade Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" id="title2" required="required" placeholder="Eg. Pass" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>
      
	   <tr>
        <td align="left" valign="middle">Assign to Class:</td>
      <td>
        <select name="class_id" id="e4" style="width: 90%;" class="grad_subjclass">
            <option <?php if(@$row['class_id'] == 0) { echo 'selected';} ?> value="0">Use for All Classes</option>
            <option <?php if($row['class_id'] != '0') { echo 'selected';} ?> value="<?=$row['class_id']?>">Use for Selected Classes</option>
		</select>
        </td>
      </tr>
      <?php
	  $dis = 'display:none;';
	  $classes_id = array();
	  if(isset($_REQUEST['edit'])) {
		  if($row['class_id'] != '0') {  $dis = ''; }
		  $classes_id = explode(',',$row['class_id']);
	  }
	  ?>
      <div id="grad_mulclass" style="">
      <tr>
        <td align="left" valign="middle">Select Classes:</td>
      <td>
        <select name="classes_id[]" multiple="multiple" id="e5" style="width: 90%;" class="grad_mulclass">
			<?php
                $sql=$query="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $result2 = mysqli_query($server, $query);
				while($rows = mysqli_fetch_assoc($result2)){
                $c_id = $rows['id'];
                $title = $rows['title'];
            ?>
               <option <?php if (in_array($c_id, $classes_id)) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  }   ?>
			</select>
        </td>
      </tr>
      </div>
      
      <tr>
        <td align="left" valign="middle">Code:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="code" id="code" required="required" placeholder="Eg. A" value="<?php echo @$row['code']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Minimum Mark:</td>
        <td  align="left" valign="middle">
        	<input type="number"  name="start_mark" id="start_mark" required="required" placeholder="" value="<?php echo @$row['exam_max']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Maximum Mark: <br /><blue>(Must be higher than the Min Mark)</blue></td>
        <td  align="left" valign="middle">
        	<input type="number"  name="end_mark" id="end_mark" required="required" placeholder="" value="<?php echo @$row['end_mark']; ?>">
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="subject" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Grade</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Grade</button>
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

	//create new prents
	if(gradeExist($start_mark,$end_mark,$school_id)) {
		$message = 'A grade has already been created with the specified Min Mark and Max Mark.';
		$class = 'yellow';
	} else if($end_mark <= $start_mark) {
		$message = 'Sorry but the Max mark muxt be higher than the Min mark.';
		$class = 'yellow';
	} else {
		if($class_id == 0) {
			$query = "INSERT INTO grades (`id`, `school_id`,`code`, `title`, `start_mark`, `end_mark`, `class_id`) VALUES (NULL, '$school_id','$code', '$title','$start_mark','$end_mark', '0');";
			mysqli_query($server, $query) or die(mysqli_error($server));		
		} else {
			$class_id = '';
			foreach($_POST['classes_id'] as $class_d) {
				$class_id .= ','.$class_d;
			}
			$class_id = trim($class_id,',');
			$query = "INSERT INTO grades (`id`, `school_id`,`code`, `title`, `start_mark`, `end_mark`, `class_id`) VALUES (NULL, '$school_id','$code', '$title','$start_mark','$end_mark', '$class_id');";
			mysqli_query($server, $query) or die(mysqli_error($server));		
		}
		$message = 'The new grade was succesfully saved.';
		$class = 'green';
	}
}

if(isset($_POST['save'])){
	$subject = $_POST['subject'];
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	if($end_mark <= $start_mark) {
		$message = 'Sorry but the Max mark muxt be higher than the Min mark.';
		$class = 'yellow';
	} else {
		foreach ($_POST as $key => $value ){
			$n = count($_POST);
			//update students fields
			if(($key != "subject") && $key != 'classes_id' && ($key != "save") && ($key != "id") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
				$sql=$query="UPDATE `grades` SET `$key` =  '$value' WHERE `id` = '$subject';";
				mysqli_query($server, $query) or die(mysqli_error($server));
			}
		}
	}
	if($class_id == 0) {
		$sql=$query="UPDATE `grades` SET `class_id` =  '0' WHERE `id` = '$subject';";
		mysqli_query($server, $query) or die(mysqli_error($server));
	} else {
		$class_id = '';
		foreach($_POST['classes_id'] as $class_d) {
			$class_id .= ','.$class_d;
		}
		$class_id = trim($class_id,',');
		$sql=$query="UPDATE `grades` SET `class_id` =  '$class_id' WHERE `id` = '$subject';";
		mysqli_query($server, $query) or die(mysqli_error($server));	
	}
		
	$message = 'The selected grade was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword']))
{
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
	         $clauses[] = "p.title LIKE '%$term%' OR p.code LIKE '%$term%'";
	    }   else {
	         $clauses[] = "p.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM grades p WHERE p.school_id = '$school_id' AND $filter ";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "SELECT * FROM grades WHERE school_id = '$school_id' ORDER BY start_mark ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")	{
		$message = "There are currently no grades created for your school";
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
        <input type="search" name="keyword" placeholder="Search Grades" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3 || userRole($userID) == 8) { ?>
        <a href="admin/grade?new"><button type="button" class="submit">Add <hide>New</hide></button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="20%" scope="col">Title </th>
                <th width="10%" scope="col">Code</th>
                <th width="10%" scope="col">Class</th>
                <th width="15%" scope="col">Start Mark</th>
                <th width="15%" scope="col">End Mark</th>
                <th width="20%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($rew = mysqli_fetch_assoc($result)){
					$id = $rew['id'];
					$title = $rew['title'];
					$code = $rew['code'];
					$start_mark = $rew['start_mark'];
					$end_mark = $rew['end_mark'];
					$cll = 'Not assigned';
					if($rew['class_id'] != 0){
						$cll = '';
						$classes_id = explode(',',$rew['class_id']);
						foreach($classes_id as $clss) {
							$cll .= ', '.className($clss);
						}
						$cll = trim($cll,',');
					}
				?>
              <tr class="inner">
                <td width="10%"> <?php echo $id; ?></td>
                <td width="20%"> <?php echo $title; ?></td>
                <td width="10%"> <?php echo $code; ?></td>
                <td width="10%"> <?php echo $cll; ?></td>
                <td width="15%"> <?php echo $start_mark; ?></td>
                <td width="15%"> <?php echo $end_mark; ?></td>
                <td width="20%" valign="middle">
                <a href="admin/grade?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/grade?delete=<?php echo $id;?>"><button class="danger">Remove</button></a>
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
