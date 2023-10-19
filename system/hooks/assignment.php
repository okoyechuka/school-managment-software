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

if (getSetting('current_session') < 1) {
$message = 'You have not defined the current accademic session yet!. <br>You must fix this before you can manage fees. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/generalsetting" title="Difine Active Session">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
}

if(isset($_REQUEST['delete']))
{
	$delete = filterinp($_REQUEST['delete']);
	if(isset($_REQUEST['yes'])) {
		$sql=$query= "DELETE FROM assignments WHERE id = '$delete' AND school_id = '$school_id' ";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));

		$message = "The assignment was successfully deleted.";
		$class="green";
	} else {
		$message = "Are you sure you want to delete the selected assignment? <br><a href='admin/assignment?delete=".$id."&yes=1'>Yes I'm sure</a> <a href='admin/assignment'>Cancel</a>";
		$class="yellow";
	}
}

//start initiation
if(isset($_GET['new']) || isset($_REQUEST['edit'])){
	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

	if(isset($_REQUEST['edit'])) {
	$edit = filterinp($_REQUEST['edit']);

		$sql=$query="SELECT * FROM assignments WHERE id = '$edit' AND school_id = '$school_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);

		$currentSession = $row['session_id'];
		$currentTerm = $row['term_id'];
		$class = $row['class_id'];
		$subject = $row['subject_id'];
		$close_date = $row['close_date'];
		$title = $row['title'];
		$text = $row['text'];
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['title']; ?>
<?php
	} else {
	$title = '';
	$text = 'Instructions:';
	$subject = '';
	$close_date = addDays(date('Y-m-d'), 1);
	$class = '';
?>
<div id="add-new">
   <div id="add-new-head">Create Assignment
<?php } ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
    <form method="post" action="admin/assignment" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
 	  <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" id="titles" required="required"  value="<?php echo $title;?>">
      </td>
      </tr>

       <tr>
        <td  align="left" valign="middle">Session:</td>
        <td  align="left" valign="middle">
        <select name="session_id" readonly id="e1" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY start_date DESC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

					while($row =mysqli_fetch_assoc($result)){
		                $g_id = $row['id'];
		                $title0 = $row['title'];
		            ?>
		               <option value="<?php echo $g_id; ?>" <?php if($currentSession == $g_id) {echo 'selected';} ?>><?php echo $title0; ?></option>
            <?php
			}   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Term:</td>
        <td  align="left" valign="middle">
        <select name="term_id" readonly id="e2" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                while($row = mysqli_fetch_assoc($result)){
		                $g_id = $row['id'];
				                $title1 = $row['title'];
				            ?>
				               <option value="<?php echo $g_id; ?>" <?php if($currentTerm == $g_id) {echo 'selected';} ?>><?php echo $title1; ?></option>
				            <?php
								}   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td  align="left" valign="middle">Select Class:</td>
        <td  align="left" valign="middle">
        <select name="class_id" id="e3" style="width: 90%" >
       		<option value="0" <?php if($class == 0) {echo 'selected';} ?>>All Classes</option>
			<?php
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
				if(userRole($userID) == 4) {
						$class_id = getTeacherClass(userProfile($userID));
						$sqlC=$queryC="SELECT * FROM classes WHERE id = '$class_id' ORDER BY title ASC";
				}
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

					while($row=mysqli_fetch_assoc($resultC)){
		                $c_id = $row['id'];
		                $title2 = $row['title'];
				            ?>
				               <option  value="<?php echo $c_id; ?>" <?php if(@$class == $c_id) { echo 'selected'; } ?>><?php echo $title2; ?></option>
				            <?php
							}  ?>
			</select>
        </td>
      </tr>
      <tr>
        <td  align="left" valign="middle">Select Subject:</td>
        <td  align="left" valign="middle">
        <select name="subject_id" id="e4" style="width: 90%" >
			<?php
                $sql=$query="SELECT * FROM subject WHERE school_id = '$school_id' ORDER BY title ASC";
				if(userRole($userID) == 4) {
						$class_id = getTeacherClass(userProfile($userID));
						$sqlC=$queryC="SELECT * FROM subject WHERE class_id = '$class_id' OR class_id = '0' ORDER BY title ASC";
				}
                $result = mysqli_query($server, $query) or die(mysqli_error($server));
                $num = mysqli_num_rows($result);

					while($row = mysqli_fetch_assoc($result)){
		                $g_id = $row['id'];
		                $title0 = $row['title'].' ('.className($row['class_id']).')';
		            ?>
		               <option value="<?php echo $g_id; ?>" <?php if(@$subject == $g_id) { echo 'selected'; } ?>><?php echo $title0; ?></option>
            <?php
				}   ?>
			</select>
        </td>
      </tr>

       <tr>
        <td align="left" valign="top">Assignment Contents:</td>
        <td  align="left" valign="middle">
        	<textarea placeholder="Type detailed assignment instructions here" id="textMessage" class="ckeditor" name="text"  style="height: 400px; width:98%;" required ><?php echo @$text; ?>&nbsp;</textarea>
      </td>
      </tr>

	<tr>
        <td align="left" valign="middle">Due Date:</td>
        <td  align="left" valign="middle">
        	<input type="date" name="close_date" id="close_date" required="required"  value="<?php echo $close_date;?>">
      </td>
      </tr>

      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;

        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="assignment" value="<?php echo $edit; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Assignment</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Create Assignment</button>
        <?php } ?>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving...</div>
	</form>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}

if(isset($_POST['add'])){
	foreach ($_POST as $key => $value ){
		$$key = mysqli_real_escape_string($server,$value);
	}
	//create new prents
	$sql=$query="INSERT INTO assignments (`id`, `school_id`, `session_id`, `class_id`, `term_id`, `subject_id`, `title`, `text`, `close_date`)
	VALUES (NULL, '$school_id', '$session_id', '$class_id', '$term_id', '$subject_id', '$title', '$text','$close_date');";
	mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new assignment was succesfully created.';
	$class = 'green';
}

if(isset($_POST['save'])){
	foreach ($_POST as $key => $value ){
		$$key = mysqli_real_escape_string($server,$value);
	}

	//create new prents
	$sql=$query="UPDATE `assignments` SET `session_id` =  '$session_id' WHERE `id` = '$assignment';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `assignments` SET `term_id` =  '$term_id' WHERE `id` = '$assignment';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `assignments` SET `class_id` =  '$class_id' WHERE `id` = '$assignment';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `assignments` SET `subject_id` =  '$subject_id' WHERE `id` = '$assignment';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `assignments` SET `title` =  '$title' WHERE `id` = '$assignment';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `assignments` SET `text` =  '$text' WHERE `id` = '$assignment';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `assignments` SET `close_date` =  '$close_date' WHERE `id` = '$assignment';";
	mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The selected assignment was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword'])){
$class = filterinp($_GET['class_id']);
$term = filterinp($_GET['term_id']);
$session = filterinp($_GET['session_id']);

$school_id = $school_id;

$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', $term);
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "f.title LIKE '%$term%' OR f.text LIKE '%$term%' OR c.title LIKE '%$term%' OR t.title LIKE '%$term%' OR s.title LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "f.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query= "select * FROM assignments f JOIN session s ON s.id = f.session_id JOIN classes c ON c.id = f.class_id JOIN terms t ON t.id = f.term_id WHERE a.school_id = '$school_id' AND $filter AND f.class_id = '$class' AND f.session_id = '$session' AND f.term_id = '$term'";
	if(userRole($userID) == 4) {
		$class_id = getTeacherClass(userProfile($userID));
	}

 	$resultF = mysqli_query($server, $query) or die(mysqli_error($server));
	$numF = mysqli_num_rows($resultF);

	if($numF < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM assignments WHERE school_id = '$school_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	if(userRole($userID) == 4) {
		$class_id = getTeacherClass(userProfile($userID));
		$sql=$query = "SELECT * FROM assignments WHERE school_id = '$school_id' AND class_id = '$class_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	}
	$resultF = mysqli_query($server, $query);
	$numF = mysqli_num_rows($resultF);
	if($numF < "1")
		{
		$message = "There are no assignments created for your school";
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
           <select name="session_id" id="e1" style="" >
        	<option value="0" <?php if($currentSession == 0) {echo 'selected';} ?>>All Sessions</option>
			<?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY start_date DESC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								while($row = mysqli_fetch_assoc($result)){
		                $g_id = $row['id'];
		                $title = $row['title'];
				            ?>
				               <option value="<?php echo $g_id; ?>" <?php if($session == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
				            <?php
								}   ?>
            <option selected value="" disabled>Select Session</option>
			</select>
		&nbsp;
        <select name="term_id" id="e2" style="" >
       		 <option value="0" <?php if($term == 0) {echo 'selected';} ?>>All Terms</option>
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)){
                		$g_id = $row['id'];
		                $title = $row['title'];
		            ?>
		               <option value="<?php echo $g_id; ?>" <?php if($tern == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php
				}   ?>
            <option selected value="" disabled>Select Term</option>
			</select>
        &nbsp;
        <select name="class_id" id="e3" style="" >
       		<option value="0" <?php if($class == 0) {echo 'selected';} ?>>All Classes</option>
			<?php
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
				 if(userRole($userID) == 4) {
				$class_id = getTeacherClass(userProfile($userID));
				$sqlC=$queryC="SELECT * FROM classes WHERE id = '$class_id' ORDER BY title ASC";
				}
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

					while($row = mysqli_fetch_assoc($resultC)){
		                $c_id = $row['id'];
		                $title = $row['title'];
		            		?>
		               	<option  value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
		            		<?php
								}  ?>
            <option selected value="" disabled>Select Class</option>
			</select>
            &nbsp;
        <input type="search" name="keyword" placeholder="Search" />
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<5) { ?>
            <a href="admin/assignment?new"><button type="button" class="submit">Add <hide>Assignment</hide></button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="30%" scope="col">Assignment</th>
                <th width="13%" scope="col">Term</th>
                <th width="13%" scope="col">Class</th>
                <th width="15%" scope="col">Subject</th>
                <th width="10%" scope="col">Answers</th>
                <th width="16%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($resultF)){
					$id = $row['id'];
					$term = $row['term_id'];
					$class = $row['class_id'];
					$subject = $row['subject_id'];
					$title = $row['title'];

				?>
              <tr class="inner">
                <td width="30%"> <?php echo $title; ?></td>
                <td width="13%"> <?php echo termName($term); ?></td>
                <td width="13%"> <?php echo className($class); ?></td>
                <td width="15%"> <?php echo subjectName($subject); ?></td>
                <td width="10%"> <?php echo countAssignment($id); ?></td>
                <td width="16%" valign="middle">
                <a href="admin/answers?id=<?php echo $id;?>"><button class="success"> Answers </button></a>
                 <a href="admin/assignment?edit=<?php echo $id;?>"><button>Edit </button></a>
                 <a href="admin/assignment?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
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
