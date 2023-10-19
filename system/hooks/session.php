<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		admin/session
Description:	This is the session page
Developer: 		Ynet Interactive
Date: 			26/02/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());



if(userRole($userID) > 2) {
header('location: admin.php');
}

if(isset($_REQUEST['delete']))
{
	$book = filterinp($_REQUEST['delete']);
	if(getSetting('current_session') != $book) {
		$sql = "DELETE FROM sessions WHERE id = '$book'";
		$result = mysqli_query($server, $sql) or die(mysqli_error($server));

		$message = "The selected sessions was successfully deleted.";
		$class="green";
	} else {
		$message = "Sorry but you cannot delete an active session. Please change the active session from the settings page and try again.";
		$class="yellow";
	}
}


	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

if(isset($_REQUEST['current']))
{
	$book = filterinp($_REQUEST['current']);
	saveSettings('current_session',$book,0) ;
	$sql=$query="UPDATE `schools` SET `current_session` =  '$book' WHERE `id` = '$school_id';";
	mysqli_query($server, $query) or die(mysqli_error($server));

	$message = "The new active school session has been successfully set.";
	$class="green";
}



if(isset($_REQUEST['edit']) || isset($_REQUEST['new']))
{
	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM sessions WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['$title']; ?>
<?php
	} else {
		$parent = '';
		$row['title'] = date('Y').'/'.(date('Y')+1).' Academic Session';
		$row['start_date'] = date('Y-m-d');
		$row['end_date'] = date('Y-m-d');
?>
<div id="add-new">
   <div id="add-new-head">Create New Session
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/session" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Session ID:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="id" id="id" readonly="readonly" placeholder="Auto Assign" value="<?php echo @$row['id']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Session Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" id="title2" required="required" placeholder="" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Start Date:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="start_date" id="start" required="required" placeholder="" value="<?php echo @$row['start_date']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">End Date:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="end_date" id="end" required="required" placeholder="" value="<?php echo @$row['end_date']; ?>">
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="class" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Session</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Session</button>
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
			$sql=$query = "INSERT INTO sessions (`id`, `school_id`, `title`, `start_date`, `end_date`)
			VALUES (NULL, '$school_id', '$title', '$start_date', '$end_date');";
			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new session was succesfully created.';
	$class = 'green';

}

if(isset($_POST['save']))
{
$class = $_POST['class'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "id") && ($key != "save") && ($key != "photo") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
			$sql=$query="UPDATE `sessions` SET `$key` =  '$value' WHERE `id` = '$class';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}



	$message = 'The selected session was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword']))
{
$category = filterinp($_GET['category']);

$school_id = $school_id;

$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', mysqli_real_escape_string($server, $_GET['keyword']));
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
	$sql = "select * FROM sessions p WHERE p.school_id = '$school_id' AND $filter ";

 	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql = "SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no sessions created for your school";
		$class="blue";
		}
}
if(isset($_REQUEST['msg'])) {
	$message = 'This seem to be your first time. You need to create a new session and set it as the current active session for your school before you continue';
	$class = 'blue';
}
?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <input type="search" name="keyword" placeholder="Search Session"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3 || userRole($userID) == 8) { ?>
        <a href="admin/session?new"><button type="button" class="submit">Add <hide>New</hide></button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="25%" scope="col">Session Name</th>
                <th width="30%" scope="col">Session Duration</th>
                <th width="20%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$start = date('d M, Y', strtotime($row['start_date']));
					$end = date('d M, Y', strtotime($row['end_date']));
					$available = $start.' - '.$end;

				?>
              <tr class="inner">
                <td width="10%"> <?php echo sprintf('%05d',$id); ?></td>
                <td width="25%"> <?php echo $title; ?></td>
                <td width="30%"> <?php echo $available; ?></td>
                <td width="20%" valign="middle">
                <a href="admin/session?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/session?delete=<?php echo $id;?>"><button class="danger">Remove</button></a>
                <?php if($id != $currentSession) { ?>
                <a href="admin/session?current=<?php echo $id;?>"><button class="success">Set Active</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php
						$i++;
					} ?>
              </table>

<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
</div>
