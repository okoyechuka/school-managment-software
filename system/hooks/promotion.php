<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		promotion.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			4/10/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) > 3 && userRole($userID) != 4) {
header('location: index.php');
}

	$session = getSetting('current_session');
	$term = getSetting('current_term');
	$graduate_class = getSetting('graduate_class_id');
	$class="0";

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

//check if subject exist

if (getSetting('current_session') < 1) {
$message = 'You have not defined the current accademic session yet!. <br>You must fix this before you can manage fees. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/generalsetting" title="Difine Active Session">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
}


//start initiation


if(isset($_POST['promoted']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

	foreach($_POST['passed'] as $student_id => $passed){
		$sql=$query = "INSERT INTO student_class (`id`, `student_id`, `session_id`, `class_id`)
		VALUES (NULL, '$student_id', '$session_id', '$class_id');";
		mysqli_query($server, $query) or die(mysqli_error($server));
		if($class_id == 999999) {
			$sql=$query="UPDATE `students` SET `status` = '5' WHERE `id` = '$student_id';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
		global $hooks;
		$vars = array("student_id"=>$student_id );
		$_SESSION['EventVals'] = $vars;
		$hooks->do_action('OnStudentPromote'); 
	}
	$message = 'Selected students successfully promoted to new the class.';
	$class = 'green';
}

if(isset($_GET['promote_class']))
{

	$class = $_REQUEST['promote_class'];
	$school = $school_id;

?>
<div id="add-new">
   <div id="add-new-head"> Promote <?php echo className($class); ?> Students

       <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>

   <div class="inside" style="background-color: #fff;">

    <form method="get" action="admin/promotion" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" valign="middle">Current Session:</td>
        <td align="left" valign="middle">
        <select name="session_id" id="e1" style="width: 90%" >
       	 <option  value="<?php echo $currentSession; ?>"><?php echo sessionName($currentSession); ?></option>
			<?php
			     $sqlC=$queryC="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY id DESC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

                while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option  value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php
									$i++;
								}  ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Current Class:</td>
        <td align="left" valign="middle">
        <select name="class_id" id="e2" style="width: 90%" >
			<?php
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
			if(userRole($userID) == 4) {
				$class_id = getTeacherClass(userProfile($userID));
				$sqlC=$queryC="SELECT * FROM classes WHERE id = '$class_id' ORDER BY title ASC";
			}
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

                $i=0;
				while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option  value="<?php echo $c_id; ?>" <?php if($class == $c_id) { echo 'selected'; } ?>><?php echo $title; ?></option>
            <?php
									$i++;
								}  ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Next Session:</td>
        <td align="left" valign="middle">
        <select name="new_session_id" id="e3" style="width: 90%" >
			<?php
			     $sqlC=$queryC="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY id DESC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

                $i=0;
								while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option  value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php
									$i++;
								}  ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">New Class:</td>
        <td align="left" valign="middle">
        <select name="new_class_id" id="e4" style="width: 90%" >
       	 <option  value="<?php echo 999999; ?>"><?php echo 'Graduate Students'; ?></option>
			<?php
			     $sqlC=$queryC="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);

			while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option  value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php
								}  ?>
			</select>
        </td>
      </tr>
<?php if($_GET['t'] == 'all') { ?>
      <input type="hidden" name="use" value="all" />
<?php } ?>
<?php if($_GET['t'] == 'score') { ?>
      <input type="hidden" name="use" value="score" />
      <tr>
        <td  align="left" valign="middle">Exam to Use:</td>
        <td  align="left" valign="middle">
        <select name="exam" id="e5" style="width: 90%;" >
      		<option value="0" >Use Cumulative Scores from Selected Session</option>
			<?php
                $sql=$query="SELECT * FROM exams WHERE school_id = '$school_id' ";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title1 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" ><?php echo $title1; ?></option>
            <?php
								$i++;
								}   ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Set Pass Mark:<br /><small>In Percentage</small></td>
        <td  align="left" valign="middle">
        	<input type="number"  name="pass_mark" id="pass_mark" max="100" min="1" style="width: 90%;"  required="required"  value="50">
      </td>
      </tr>
<?php } ?>
<?php if($_GET['t'] == 'manual') { ?>
      <input type="hidden" name="use" value="manual" />
<?php } ?>
      <!-- Submit Buttons -->
      <tr>
        <td width="30%" align="left" valign="top">
        <input type="hidden" name="promot" value="yes" />
        <input type="hidden" name="class_id" value="<?php echo $class; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Get Students</button>
	</form>
        <td align="left" valign="top">&nbsp;</td>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Initiating...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}



if(isset($_GET['promot']))
{

	$new_class = $_REQUEST['new_class_id'];
	$class = $_REQUEST['class_id'];
	$new_session = $_REQUEST['new_session_id'];
	$session = $_REQUEST['session_id'];
	$using = $_REQUEST['use'];
	$exam = @$_REQUEST['exam'];
	$school = $school_id;


	if($new_class == $class) {
		$Emessage = 'Oops!<br>You cannot promote students to same class as their current class. Please choose a different class';
	}
	if($new_session == $session) {
		$Emessage = 'Oops!<br>You need to select two different sessions to promote students';
	}

	if(isset($Emessage)) {
		$message = $Emessage;
		$class = 'red';
	} else {

?>
<div id="add-new">
   <div id="add-new-head">Promote students in <?php echo className($class); ?> to <?php echo className($new_class); ?>

       <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>

   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/promotion" enctype="multipart/form-data">
    <div style="overflow-x:auto; width: 100%;">
    <table class="list" width="100%" border="0" cellspacing="1" cellpadding="0" style="width:100%;min-width:800px; border: 0">
      <tr>
        <th width="40%" align="left" valign="middle"> Student</th>
        <?php if($using == 'score') {  $exam = $_GET['exam'];
		$pass_mark = $_GET['pass_mark'];
		if($exam == 0) { ?>
        <th align="left" width="30" valign="middle"> Cumulative Average</th>
        <?php } else {?>
        <th align="left" width="30" valign="middle"> Average Score</th>
        <?php } ?>
        <?php } else { ?>
        <th align="left" width="30" valign="middle"> Average Score</th>
        <?php } ?>
        <th align="left" width="30" valign="middle"> Check to Promote</th>
      </tr>
   <?php
   $sql=$query="SELECT * FROM student_class WHERE class_id = '$class' AND session_id = '$session'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$numSt = 0;
   $i=0;
	 while($row = mysqli_fetch_assoc($result)){
   ?>

			<?php if($using == 'score') {
				$exam = $_GET['exam'];
				$pass_mark = $_GET['pass_mark'];
				if($exam == 0) {
					$percentage = @sessionPercentage($row['student_id'],$session);
				} else {
					$percentage = @examPercentage($row['student_id'],$exam);
				}
				if($percentage >= $pass_mark) {
					$numSt++;
			?>

   		<tr style="">
        <td align="left" valign="middle"><?php echo studentName($row['student_id']); ?></td>
        <td width="30%" align="left" valign="middle"><?php echo round($percentage, 2); ?>%</td>
        <td width="30%" align="left" valign="middle">
        <input name="passed[<?php echo $row['student_id']; ?>]" type="checkbox" value="1" checked="checked" /> Promote
        </td>
    	</tr>
<?php
	} else { 
		//skipp student
	}
} elseif($using == 'all') {   ?>
   		<tr>
        <td align="left" valign="middle"><?php echo studentName($row['student_id']); ?></td>
        <?php if($exam == 0) { ?>
        <td width="30%" align="left" valign="middle"><?php echo sessionPercentage($row['student_id'],$_GET['session_id']); ?>%</td>
        <?php } else { ?>
        <td width="30%" align="left" valign="middle"><?php echo examPercentage($row['student_id'],$_GET['exam']); ?>%</td>
        <?php } ?>
        <td width="30%" align="left" valign="middle">
        <input name="passed[<?php echo $row['student_id']; ?>]" type="checkbox" value="1" checked="checked" /> Promote
        </td>
    	</tr>
<?php } else {   ?>
   		<tr>
        <td align="left" valign="middle"><?php echo studentName($row['student_id']); ?></td>
        <?php if($exam == 0) { ?>
        <td width="30%" align="left" valign="middle"><?php echo sessionPercentage($row['student_id'],$_GET['session_id']); ?>%</td>
        <?php } else { ?>
        <td width="30%" align="left" valign="middle"><?php echo examPercentage($row['student_id'],$_GET['exam']); ?>%</td>
        <?php } ?>
        <td width="30%" align="left" valign="middle">
        <input name="passed[<?php echo $row['student_id']; ?>]" type="checkbox" value="1"  /> Promote
        </td>
    	</tr>
<?php } ?>
  <?php
			$i++;
		}
  if($num < 1 || ($using == 'score' && $numSt < 1)) {
		echo '<tr><td colspan="3">'.showMessage('Sorry but no students were fund using your selected criteria','yellow').'<td></tr>';
  } else {
  ?>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" colspan="3" valign="top">&nbsp;</td>
        <input type="hidden" name="promoted" value="yes" />
        <input type="hidden" name="school_id" value="<?php echo $school; ?>" />
        <input type="hidden" name="class_id" value="<?php echo $new_class; ?>" /
        <input type="hidden" name="session_id" value="<?php echo $new_session; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Promote Students</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Moving Students to New Class...</div>
        </td>
      </tr>
 <?php } ?>
    </table>
	</div>

	</div>
</div>
<?php
	}
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
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "p.title LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "p.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM classes p WHERE p.school_id = '$school_id' AND $filter ";
	if(userRole($userID) == 4) {
		$class_id = getTeacherClass(userProfile($userID));
		$sql=$query = "SELECT * FROM classes WHERE id = '$class_id' ORDER BY title ASC";
	}

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	if(userRole($userID) == 4) {
		$class_id = getTeacherClass(userProfile($userID));
		$sql=$query = "SELECT * FROM classes WHERE id = '$class_id' ORDER BY title ASC";
	}

	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no classes created for your school";
		if(userRole($userID) == 4) {
			$message = "There are currently no classes assigned to you";
		}
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
        <input type="search" name="keyword" placeholder="Search Class" style="width:200px"/>
        <button class="submit"><i class="fa fa-search"></i>Search</button>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="25%" scope="col">Class </th>
                <th width="20%" scope="col">Students Count</th>
                <th width="30%" scope="col">Promotion Criteria</th>
              </tr>
               <?php

				$i = 0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'].' <small>('.sessionName($session).')</small>';
					$available = countClass($id,$session).' Student(s)';
				?>
   
              <tr class="inner">
                <td width=""> <?php echo sprintf('%05d',$id); ?></td>
                <td width=""> <?php echo $title; ?></td>
                <td width=""> <?php echo $available; ?> </td>
                <td width="" valign="middle">
                <a href="admin/promotion?promote_class=<?php echo $id;?>&t=manual"><button>Select Students</button></a>
                <a href="admin/promotion?promote_class=<?php echo $id;?>&t=score"><button>Use Score</button></a>
                <a href="admin/promotion?promote_class=<?php echo $id;?>&t=all"><button>All Students</button></a>
                </td>
              </tr>
              <?php
						$i++;
					} ?>
              </table>

<?php displayPagination($setLimit,$page, $query) ?>

        </div>
    </div>
</div>
