<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		document.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			22/02/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(isset($_REQUEST['delete']))
{

if(userRole($userID) > 2 && !userDocument($userID, $_REQUEST['delete'])) {
header('location: index.php');
}

	$library = filterinp($_REQUEST['delete']);
	$sql=$query = "DELETE FROM library WHERE id = '$library'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));

	$message = "The selected document was successfully deleted.";
	$class="green";
}


if(isset($_REQUEST['view']))
{
	$library = mysqli_real_escape_string($server, $_REQUEST['view']);
		//get students profile
		$sql=$query="SELECT * FROM library WHERE id = '$library'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);


?>
<div id="add-new">
   <div id="add-new-head"><?php echo $row['title']; ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
        	<div class="action-box">
           <a href="<?php echo 'media/uploads/'.$row['url']; ?>" target="_blank"><button class="submit">View/Download</button></a>
           <?php if(userRole($userID) < 3 && userDocument($userID, $_REQUEST['view'])) {  ?>
            <a href="admin/document?edit=<?php echo $library; ?>&done"><button class="submit">Edit</button></a>
            <?php } ?>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle"></td>
        <td  align="left" valign="middle"><br /><br /></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Description: </td>
        <td  align="left" valign="middle"><?php echo $row['description']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Date Created:</td>
        <td  align="left" valign="middle"><?php echo date('d M, Y', strtotime($row['date_created'])); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Created By:</td>
        <td  align="left" valign="middle"><?php echo adminData('name', $row['user_id']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Type:</td>
        <td  align="left" valign="middle" style=" text-transform:uppercase"><?php echo end(explode('.',$row['url'])); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">File Size:</td>
        <td  align="left" valign="middle"><?php echo format_size(filesize('media/uploads/'.$row['url'])); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      </table>
  </div>

</div>
</div>


 <?php
}

if(isset($_REQUEST['edit']) || isset($_REQUEST['new']))
{
	if(isset($_REQUEST['edit'])) {
	$library = filterinp($_REQUEST['edit']);

	if(userRole($userID) > 2 && !userDocument($userID, $_REQUEST['edit'])) {
	header('location: index.php');
	}

		$sql=$query="SELECT * FROM library WHERE id = '$library'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Edit <?php echo $row['title']; ?>
<?php
	} else {
		$library = '';
?>
<div id="add-new">
   <div id="add-new-head">Upload Document
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/document" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
        <td align="left" valign="middle">Upload File:</td>
        <td  align="left" valign="middle">
        	<input type="file" <?php if(!isset($_REQUEST['edit'])) { echo 'required="required"';} ?> style="width: 70%" name="photo" id="photo" >
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" required="required" placeholder="" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Description:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="description" id="description" placeholder="" value="<?php echo @$row['description']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue>Who can access this document?:</td>
      </tr>
      <tr>
        <td colspan="2" width="75%" align="left" valign="middle">
        <?php if(!isset($_REQUEST['edit'])) { ?>
        	Accountants <input name="account" type="checkbox" value="3" checked="checked" />&nbsp;&nbsp;&nbsp;&nbsp;
            Teachers <input name="teachers" type="checkbox" value="3" checked="checked" />&nbsp;&nbsp;&nbsp;&nbsp;
            Front Desk <input name="frontdesk" type="checkbox" value="3" checked="checked" />&nbsp;&nbsp;&nbsp;&nbsp;
            Librarians <input name="librarians" type="checkbox" value="3" checked="checked" />&nbsp;&nbsp;&nbsp;&nbsp;
            Parents <input name="parents" type="checkbox" value="3" checked="checked" />&nbsp;&nbsp;&nbsp;&nbsp;
            Students <input name="students" type="checkbox" value="3" checked="checked" />&nbsp;&nbsp;&nbsp;&nbsp;
            Other Staffs <input name="others" type="checkbox" value="3" checked="checked" />&nbsp;&nbsp;&nbsp;&nbsp;
         <?php } else {
		 $prev = $row['privilege'];
		 $list = explode(',',$prev);
		 ?>
            Accountants <input name="account" type="checkbox" value="3" <?php if (in_array(3, $list)) {
  echo 'checked="checked"'; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;
            Teachers <input name="teachers" type="checkbox" value="3" <?php if (in_array(4, $list)) {
  echo 'checked="checked"'; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;
            Front Desk <input name="frontdesk" type="checkbox" value="3" <?php if (in_array(7, $list)) {
  echo 'checked="checked"'; } ?>/>&nbsp;&nbsp;&nbsp;&nbsp;
            Librarians <input name="librarians" type="checkbox" value="3" <?php if (in_array(8, $list)) {
  echo 'checked="checked"'; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;
            Parents <input name="parents" type="checkbox" value="3" <?php if (in_array(5, $list)) {
  echo 'checked="checked"'; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;
            Students <input name="students" type="checkbox" value="3" <?php if (in_array(6, $list)) {
  echo 'checked="checked"'; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;
            Other Staffs <input name="others" type="checkbox" value="3" <?php if (in_array(9, $list)) {
  echo 'checked="checked"'; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;
        <?php } ?>
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="library" value="<?php echo $library; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Document</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Create Document</button>
        <?php } ?>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Document...</div>
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

	$upload_path = 'media/uploads/';
	$file1 = $_FILES['photo']['name'];
	$filename1 = date("d-m-Y").$_FILES['photo']['name'];
	$ext = end(explode(".", $_FILES['photo']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","","mpeg","flv","wav","mp4","mp3","exe","sldm","pub","accdb","dot","totx","docm","xlst","xlsm","pot","pps","doc","docx","pdf","zip","ppt","pptx","pptm","xps","xls","xlsx","csv");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
	} else {
		$date = date('Y-m-d');
		$privilege = '1,2';
		if(isset($_POST['account'])) { $privilege .= ',3';}
		if(isset($_POST['teachers'])) { $privilege .= ',4';}
		if(isset($_POST['frontdesk'])) { $privilege .= ',7';}
		if(isset($_POST['librarians'])) { $privilege .= ',8';}
		if(isset($_POST['parents'])) { $privilege .= ',5';}
		if(isset($_POST['students'])) { $privilege .= ',6';}
		if(isset($_POST['others'])) { $privilege .= ',9';}

		if($file1 !=="") {
			move_uploaded_file($_FILES['photo']['tmp_name'],$upload_path . $filename1);
			$sql=$query="INSERT INTO library (`id`, `school_id`, `user_id`, `date_created`, `privilege`, `url`, `description`, `title`)
			VALUES (NULL, '$school_id', '$userID', '$date', '$privilege', '$filename1', '$description', '$title');";
			mysqli_query($server, $query) or die(mysqli_error($server));

		$message = 'The new document was succesfully uploaded.';
		$class = 'green';
		} else {
		$message = 'Sorry but your document cannot be uploaded at this time. Please check your file and try again';
		$class = 'yellow';
		}
	}
}

if(isset($_POST['save'])){
$library = $_POST['library'];
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

	$privilege = '1,2';
	if(isset($_POST['account'])) { $privilege .= ',3';}
	if(isset($_POST['teachers'])) { $privilege .= ',4';}
	if(isset($_POST['frontdesk'])) { $privilege .= ',7';}
	if(isset($_POST['librarians'])) { $privilege .= ',8';}
	if(isset($_POST['parents'])) { $privilege .= ',5';}
	if(isset($_POST['students'])) { $privilege .= ',6';}
	if(isset($_POST['others'])) { $privilege .= ',9';}

	$sql=$query="UPDATE `library` SET `title` =  '$title' WHERE `id` = '$library';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `library` SET `description` =  '$description' WHERE `id` = '$library';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `library` SET `privilege` =  '$privilege' WHERE `id` = '$library';";
	mysqli_query($server, $query) or die(mysqli_error($server));

	//upload father
	$upload_path = 'media/uploads/';
	$file1 = $_FILES['photo']['name'];
	$filename1 = date("d-m-Y").$_FILES['photo']['name'];
	$ext = end(explode(".", $_FILES['photo']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","","mpeg","flv","wav","mp4","mp3","exe","sldm","pub","accdb","dot","totx","docm","xlst","xlsm","pot","pps","doc","docx","pdf","zip","ppt","pptx","pptm","xps","xls","xlsx","csv");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
	} else {
		//update father photo if set
		if($file1 !=="") {
			move_uploaded_file($_FILES['photo']['tmp_name'],$upload_path . $filename1);
			$sql=$query="UPDATE `library` SET `url` =  '$filename1' WHERE `id` = '$library';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
		$message = 'The selected document was succesfully updated.';
		$class = 'green';
	}
}

if(isset($_GET['keyword']))
{
$class_id = $_GET['class'];
$subject_id = $_GET['subject'];

$school_id = $school_id;

$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server, $_GET['keyword']);
	$ser = explode(' ', mysqli_real_escape_string($server, $_GET['keyword']));
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "title LIKE '%$term%' OR description LIKE '%$term%' OR url LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM library WHERE school_id = '$school_id' AND $filter ";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM library WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "No records found!";
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
        <input type="search" name="keyword" placeholder="Search Documents"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <a href="admin/document?new"><button type="button" class="submit btn-success">Add <hide>New</hide></button></a>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="30%" scope="col">Title</th>
                <th width="10%" scope="col">File Type</th>
                <th width="13%" scope="col">Created On</th>
                <th width="20%" scope="col">Created By</th>
                <th width="17%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$role = $row['privilege'];
					$size = end(explode('.', $row['url']));
					$date = date('d M, Y', strtotime($row['date_created']));
					$user = adminData('name', $row['user_id']);

					 $list = explode(',',$role);
					 if(in_array(userRole($userID), $list)) {
							?>
			             <div class="virtualpage hidepeice">
			              <tr class="inner">
			                <td > <?php echo $title; ?></td>
			                <td width=""> <?php echo strtoupper($size); ?></td>
			                <td width=""> <?php echo $date; ?></td>
			                <td width=""> <?php echo $user; ?></td>
			                <td width="" valign="middle">
			                <a href="admin/document?view=<?php echo $id;?>"><button class="btn-success">View</button></a>
							<?php if(userRole($userID) < 3 || userDocument($userID, $id)) {  ?>
			                <a href="admin/document?edit=<?php echo $id;?>"><button class="btn-warning">Edit</button></a>
			                <a href="admin/document?delete=<?php echo $id;?>"><button class="btn-danger">Remove</button></a>
			                <?php } ?>
			                </td>
			              </tr>
			              </div>
			              <?php
					}
					$i++;
				} ?>
			              </table>

<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
</div>
