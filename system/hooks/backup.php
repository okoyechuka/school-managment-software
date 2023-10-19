<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		backup.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			16/10/2015
*/

global $server;

if(userRole($userID) > 2) {
header('location: admin.php');
}

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

if(isset($_REQUEST['done'])){
	$message = "Your school database was successfully backed-up. <br>We recommemnd you download and keep a local copy of your database backup.";
	$class="green";
}

if(isset($_REQUEST['done2'])){
	$message = "The selected database was successfully restored.";
	$class="green";
}


if(isset($_REQUEST['backup']))
{
	$host = $sconfig['host'];
	$database = $sconfig['database'];
	$username = $sconfig['user'];
	$password = $sconfig['password'];
	$type = 'User Backup';
	$file = backup_tables($host,$username,$password,$database,$type);
	header('location: backup?done');
}

if(isset($_REQUEST['restore'])){
	$book = $_REQUEST['restore'];
	$sql=$query="SELECT * FROM backups WHERE id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$filename = 'backups/'.$row['file'];
	$host = $sconfig['host'];
	$database = $sconfig['database'];
	$username = $sconfig['user'];
	$password = $sconfig['password'];

	restore_tables($filename,$host,$username,$password,$database);
	header('location: backup?done2');
}




if(isset($_GET['keyword'])){

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
	         $clauses[] = "p.date LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "p.date LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql = "select * FROM backups p WHERE $filter ";

 	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search term.";
		$class="blue";
		}
} else {
	$sql = "SELECT * FROM backups ORDER BY id DESC LIMIT 100";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are no previous database backup records found";
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
        <input type="search" name="keyword" placeholder="Search Backups" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/backup?backup"><button type="button" class="submit">Backup</button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="30%" scope="col">Date & Time</th>
                <th width="20%" scope="col">Type</th>
                <th width="30%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$date = date('d F Y h:i A', strtotime($row['date']));
					$type = $row['type'];
					$filename = $row['file'];

				?>
              <tr class="inner">
                <td width=""> <?php echo $id; ?></td>
                <td width=""> <?php echo $date; ?></td>
                <td width=""> <?php echo $type; ?></td>
                <td width="" valign="middle">
                <a download href="backups/<?php echo $filename;?>"><button class="btn-success">Download</button></a>
                <a onclick="confirm('Are you sure you want to restore the selected database?');" href="admin/backup?restore=<?php echo $id;?>"><button class="btn-warning">Restore</button></a>
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
