<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		ticket.php
Description:	This is the ticket page
Developer: 		Ynet Interactive
Date: 			9/3/2015
*/
global $server;

$role = userRole($userID);
if(isset($_REQUEST['view'])) {
		$id = filterinp($_REQUEST['ID']);



		$sql=$query = "SELECT * FROM notice WHERE id = '$id' AND school_id = '$school_id' AND (role_id = '$role' OR role_id = '0' OR user_id = '$userID' OR class_id = '0' OR user_id = '0')";
		$result = mysqli_query($server, $sql);
		$num = mysqli_num_rows($result);
		if($num <1) {
			//header('location: Message.php');
		}
		$row = mysqli_fetch_assoc($result);
		$subject = $row['title'];
		$body = $row['text'];
		$date = date('F d, Y', strtotime($row['date']));

		$sql=$query="INSERT INTO notice_read (`id`, `notice_id`, `user_id`)
			VALUES (NULL, '$id', '$userID');";
		mysqli_query($server, $query) or die (mysqli_error($server));
	//display form
?>

<div id="add-new">
	<div id="add-new-head">Viewing Message #<?php echo $id; ?> recieved on: <?php echo date('D M d, Y', strtotime($row['date'])); ?>
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
     <div class="inside">
    <form method="post" action="admin/ticket" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Subject:</td>
        <td  align="left" valign="middle">
        	<strong><?php echo $subject; ?></strong>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Date:</td>
        <td  align="left" valign="middle">
        	<strong><?php echo $date; ?></strong>
        </td>
      </tr>
      <tr>
        <td align="left" valign="top">Message:</td>
        <td  align="left" valign="top">
            <?php echo $body; ?>
        </td>
      </tr>
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td  align="left" valign="top">
	</form>
        </td>
      </tr>
   </table>
   </div>
 </div>
<?php
}
if(isset($_REQUEST['new'])) {
	if(isset($_REQUEST['ID'])) {
		$id = filterinp($_REQUEST['ID']);

		$sql=$query="INSERT INTO notice_read (`id`, `notice_id`, `user_id`)
			VALUES (NULL, '$id', '$userID');";
		mysqli_query($server, $query) or die (mysqli_error($server));

		$sql=$query = "SELECT * FROM notice WHERE id = '$id' AND school_id = '$school' AND (role_id = '$role' OR role_id = '0' OR user_id = '$userID' OR class_id = '0' OR user_id = '0')";
		$result = mysqli_query($server, $sql);
		$num = mysqli_num_rows($result);
		if($num <1) {
			header('location: Message.php');
		}

		$row = mysqli_fetch_assoc($result);
		$subject = 'RE: '.$row['title'];
	} else {
		$subject = '';
	}
	//display form
?>

<div id="add-new">
<?php if(isset($_REQUEST['ID'])) { ?>
	<div id="add-new-head">Reply Message
<?php } else { ?>
	<div id="add-new-head">New Message
<?php } ?>
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
     <div class="inside">
    <form method="post" action="admin/ticket" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Subject:</td>
        <td  align="left" valign="middle">
        	<input type="test" name="subject" id="name" value="<?php echo $subject; ?>" maxlength="200" required="required" placeholder="Give your ticket a discriptive subject">
        </td>
      </tr>
      <tr>
        <td align="left" valign="top">Message:</td>
        <td width="69%" align="left" valign="top">
            <textarea  id="message" required name="message" ></textarea>
        </td>
      </tr>
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="save" value="1"/>
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="save" value="1" type="submit">Send Message</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Processing...</div>
        </td>
      </tr>
   </table>
   </div>
 </div>
<?php
}

if(isset($_GET['keyword']))
{
$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', $_GET['keyword']);
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "title LIKE '%$term%' OR text LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "title LIKE '%%' OR text LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM notice WHERE $filter AND school_id = '$school_id' AND (role_id = '$role' OR role_id = '0' OR user_id = '$userID' OR class_id = '0' OR user_id = '0')";

	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM notice WHERE school_id = '$school_id' AND (role_id = '$role' OR role_id = '0' OR user_id = '$userID' OR class_id = '0' OR user_id = '0') ORDER BY id DESC";
	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
}

?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        Search Messages: <input type="search" name="keyword" placeholder="Search message"/>
        <button type="submit" class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">Message ID</th>
                <th width="20%" scope="col">Date </th>
                <th width="40%" scope="col">Subject</th>
                <th width="30%" scope="col">Action</th>
              </tr>
            </table>
               <?php
				if($num < 1) echo 'No messages found';
				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$date = date('F d, Y', strtotime($row['date']));
				?>
              <div class="virtualpage hidepeice">
             <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner" <?php if(!noticeRead($id, $userID)) { echo 'style="font-weight: bold; color: blue;"'; } ?>>
                <td width="10%" <?php if(!noticeRead($id, $userID)) { echo 'style="font-weight: bold; color: blue;"'; } ?>> <?php echo sprintf('%07d',$id); ?></td>
                <td width="20%" <?php if(!noticeRead($id, $userID)) { echo 'style="font-weight: bold; color: blue;"'; } ?>> <?php echo $date; ?></td>
                <td width="40%" <?php if(!noticeRead($id, $userID)) { echo 'style="font-weight: bold; color: blue;"'; } ?>> <?php echo $title; ?></td>
                <td width="30%" valign="middle">
                <a href="admin/ticket?view&ID=<?php echo $id;?>"><button class="btn-success">Read Message</button></a>
				</td>
              </tr>
              </table>
              </div>
              <?php
						$i++;
					} ?>
<!-- Pagination start -->
<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
<?php
if(isset($_REQUEST['csv']))
	{
		$file_name = "Phonebook_CSV".date('Y-m-d-h-m-s');
		$value = mysqli_query($server, $sql);
		$row = mysqli_fetch_assoc($value);
		$csv_output=implode(",",array_keys($row))."\n";
			do
			{
			$csv_output.=implode(",",$row)."\n";
			}
			while($row = mysqli_fetch_assoc($value));

		$filename = $file_name . "_" . date("Y-d-m");
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv". date("Y-m-d") .".csv");
		header("Content-disposition: filename=".$filename.".csv");
		print $csv_output;
		exit;
}
?>
</div>