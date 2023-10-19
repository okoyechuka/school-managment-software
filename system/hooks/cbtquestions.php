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
if(!isset($_REQUEST['cbt'])){
	header('location: cbt');
} else {
	$cbt_id = filterinp($_REQUEST['cbt']);
	$query="SELECT * FROM cbt WHERE id = '$cbt_id' AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$course = mysqli_fetch_assoc($result);
	if($course['id'] < 1 ) header('location: cbt');
	if(userRole($userID) == 4) {
		if($course['teacher_id'] != userProfile($userID)) {
			header('location: cbt');
		}
	}
}
if(isset($_REQUEST['delete'])){
	$book = filterinp($_REQUEST['delete']);
	$sql=$query = "DELETE FROM cbt_questions WHERE id = '$book' AND cbt_id = '$cbt_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	mysqli_query($server,"DELETE FROM cbt_choices WHERE question_id = '$book'");
	$message = "The selected question was successfully deleted.";
	$class="green";
}

if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])){
	$row = $answers=array();
	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
	$query="SELECT * FROM cbt_questions WHERE id = '$book' AND cbt_id = '$cbt_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	//answers
	$query="SELECT * FROM cbt_choices WHERE question_id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	while($rowd = mysqli_fetch_assoc($result)){
		$answers[] = $rowd['answer'];
	}
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['title']; ?>
<?php	} else {
		$book = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Question
<?php
	}
?>
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
    <form method="post" action="admin/cbtquestions?cbt=<?=$cbt_id?>" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle"><strong>Test Question</strong>:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="question" required="required" placeholder="" value="<?php echo @$row['question']; ?>">
        </td>
      </tr>
      
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue><h4>Answer Choices:</h4></td>
      </tr>
      <tr>
        <td align="left" valign="top" colspan="2">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left" valign="middle"><strong>Choice</strong></td>
                <td align="left" valign="middle"><strong>Answer</strong></td>
                <td align="left" valign="middle"><strong>Correct Choice</strong></td>
              </tr>
              <tr>
                <td align="left" valign="middle">A</td>
                <td align="left" valign="middle"><input type="text" name="answers[]" required placeholder="" value="<?=@$answers[0]?>"></td>
                <td align="left" valign="middle"><input type="radio" name="correct_answer" value="0" <?php if(@$row['correct_answer']==@$answers[0]) { ?> checked="checked" <?php } ?>></td>
              </tr>
              <tr>
                <td align="left" valign="middle">B</td>
                <td align="left" valign="middle"><input type="text" name="answers[]" required placeholder="" value="<?=@$answers[1]?>"></td>
                <td align="left" valign="middle"><input type="radio" name="correct_answer" value="1" <?php if(@$row['correct_answer']==@$answers[1]) { ?> checked="checked" <?php } ?>></td>
              </tr>
              <tr>
                <td align="left" valign="middle">C</td>
                <td align="left" valign="middle"><input type="text" name="answers[]" placeholder="Optional" value="<?=@$answers[2]?>"></td>
                <td align="left" valign="middle"><input type="radio" name="correct_answer" value="2" <?php if(@$row['correct_answer']==@$answers[2]) { ?> checked="checked" <?php } ?>></td>
              </tr>
              <tr>
                <td align="left" valign="middle">D</td>
                <td align="left" valign="middle"><input type="text" name="answers[]" placeholder="Optional" value="<?=@$answers[3]?>"></td>
                <td align="left" valign="middle"><input type="radio" name="correct_answer" value="3" <?php if(@$row['correct_answer']==@$answers[3]) { ?> checked="checked" <?php } ?>></td>
              </tr>
              <tr>
                <td align="left" valign="middle">E</td>
                <td align="left" valign="middle"><input type="text" name="answers[]" placeholder="Optioanl" value="<?=@$answers[4]?>"></td>
                <td align="left" valign="middle"><input type="radio" name="correct_answer" value="4" <?php if(@$row['correct_answer']==@$answers[4]) { ?> checked="checked" <?php } ?>></td>
              </tr>
            </table>  
      </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="class" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Question</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Save Question</button>
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
         
if(isset($_GET['import'])){
?>
<div id="add-new">
   <div id="add-new-head">Import Questions from Excel
       <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
   	<div id="messageBox" style="max-width: 850px;" class="message blue">
    <strong>Important Notice</strong><br>
    Use this tool to import multiple questions from an Excel file. Please download the Sample Excel file to see the correct arrangement for your Excel sheet. <br><br>
     <a class="btn btn-info" href="sample3.xls" class="btn btn-primary">Download Sample</a></div>
	</div>
    <form method="post" action="admin/cbtquestions?cbt=<?=$cbt_id?>" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Selected Test</td>
        <td  align="left" valign="middle">
        	<select name="cbt_id" style="min-width: 200px;">
			<?php
			    $sqlC=$queryC="SELECT * FROM cbt WHERE id = '$cbt_id'";
                $resultC = mysqli_query($server, $queryC);
				while($row = mysqli_fetch_assoc($resultC)){
                $c_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  }   ?>
        	</select>
        </td>
       </tr>

       <tr>
        <td align="left" valign="middle">Upload Excel File:</td>
        <td  align="left" valign="middle">
        	<input type="file" name="sdata" accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required="required" value="Choose File">
        </td>
       </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="import_csv" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Import Records</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Importing Data...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php
}     

if(isset($_POST['import_csv'])) {
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	if($cbt_id < 1) {
		$message = 'You have not selected any test';$class = 'red';
	} else {
		$upload_path = 'media/uploads/';
		$file1 = $_FILES['sdata']['name'];
		$ext = end(explode(".", $_FILES['sdata']['name']));
		$allowed = array("xls","xlsx");
		if(!in_array(strtolower($ext), $allowed)) {
			$message = 'Please upload a valid Excel file';
			$class = 'red';
		} else {
			if(move_uploaded_file($_FILES['sdata']['tmp_name'],$upload_path.$file1)) {
				$success = importQuestions($file1,$cbt_id,$school_id);
				if($success!=='ok') {
					$message = $success;
					$class = 'red';
				} else {
					$message = 'The uploaded records have been successfully imported.';
					$class = 'green';
				}
			} else {
				$message = 'Sorry but your file could not be uploaded. Please try again or use a different file';
				$class = 'red';
			}
		}
	}
}
     
if(isset($_POST['add'])){
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$texts = $video = $audio = $document = "";
	$error = 0;
	$correct_answer = $_POST['answers'][$correct_answer];
	if(empty($correct_answer)) {
		$message = 'You have not set any correct answer for the test.';
		$class = 'red'; $error = 1;
	}
	if($error < 1) { 
	//create new prents
	$datetime = date('Y-m-d H:i:s');
	mysqli_query($server,"DELETE FROM cbt_questions WHERE question = '$question' AND cbt_id = '$cbt_id'");
	$query ="INSERT INTO cbt_questions (`question`, `cbt_id`, `correct_answer`, `course_id`, `school_id`) VALUES ('$question', '$cbt_id', '$correct_answer', '0', '$school_id');";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$question_id = mysqli_insert_id($server);
	mysqli_query($server,"DELETE FROM cbt_choices WHERE question_id = '$question_id'");
	foreach($_POST['answers'] as $answers) {
		$answers = filterinp($answers);
		if(!empty($answers)) {
			mysqli_query($server,"INSERT INTO cbt_choices (`question_id`, `answer`) VALUES ('$question_id', '$answers');") or die(mysqli_error($server));
		}
	}
	$message = 'The new question was succesfully added.';
	$class = 'green';
	}
}

if(isset($_POST['save'])){
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$error = 0;
	$correct_answer = $_POST['answers'][$correct_answer];
	if(empty($correct_answer)) {
		$message = 'You have not set any correct answer for the test.';
		$class = 'red'; $error = 1;
	}
	if($error < 1) { 
	mysqli_query($server,"UPDATE `cbt_questions` SET 
	`question` =  '$question',
	`correct_answer` =  '$correct_answer'
	WHERE `id` = '$class' AND cbt_id = '$cbt_id'") or die(mysqli_error($server));
	$question_id = $class;
	mysqli_query($server,"DELETE FROM cbt_choices WHERE question_id = '$question_id'");
	foreach($_POST['answers'] as $answers) {
		$answers = filterinp($answers);
		if(!empty($answers)) {
			mysqli_query($server,"INSERT INTO cbt_choices (`question_id`, `answer`) VALUES ('$question_id', '$answers');") or die(mysqli_error($server));
		}
	}
	$message = 'The selected question was succesfully updated.';
	$class = 'green';
	}
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
	         $clauses[] = "p.question LIKE '%$term%' OR p.id LIKE '%$term%'";
	    }	    else	    {
	         $clauses[] = "p.question LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM cbt_questions p WHERE p.cbt_id = '$cbt_id' AND $filter ";
 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "SELECT * FROM cbt_questions WHERE cbt_id = '$cbt_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")		{
		$message = "There are currently no question created for this test";
		$class="blue";
	}
}
?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="admin/cbtquestions?cbt=<?=$cbt_id?>" method="get">
        <input type="search" name="keyword" placeholder="Search" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <a href="admin/cbtquestions?new&cbt=<?=$cbt_id?>"><button type="button" class="submit">Add <hide>Question</hide></button></a>
        <a href="admin/cbtquestions?import&cbt=<?=$cbt_id?>"><button type="button" class="submit success">Import Questions</button></a>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="30%" scope="col">Question</th>
                <th width="50%" scope="col">Choices</th>
                <th width="20%" scope="col">Action</th>
              </tr>
               <?php $i=0; $bfas = '<div class="col-sm-12 col-md-6"><i class="fa fa-circle"></i>&nbsp;&nbsp;';
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id']; 	$answers = "";
					$result2 = mysqli_query($server, "SELECT * FROM cbt_choices WHERE question_id = '$id'");
					while($rowd = mysqli_fetch_assoc($result2)){ $answers .= $bfas.$rowd['answer'].'</div>';}
				?>
              <tr class="inner">
                <td> <?php echo $row['question']; ?></td>
                <td> <div class="row"><?php echo $answers; ?></div></td>
                <td valign="middle">
                <a href="admin/cbtquestions?edit=<?php echo $id;?>&cbt=<?=$cbt_id?>"><button>Edit</button></a>
                <a href="admin/cbtquestions?delete=<?php echo $id;?>&cbt=<?=$cbt_id?>"><button class="danger">Delete</button></a>
                </td>
              </tr>
              <?php	$i++; } ?>
              </table>
<?php displayPagination($setLimit,$page,$query) ?>
        </div>
    </div>
</div>
