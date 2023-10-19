<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		admingallery.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			5/11/2015
*/
global $server;

function countPhotos($album) {
	global $server;
	$sql=$query= "SELECT * FROM gallery WHERE album_id = '".mysqli_real_escape_string($server,$album)."'";
	$result = mysqli_query($server, $query);
return	$num = mysqli_num_rows($result);
}
function albumName($album){
	global $server;
	$sql=$query="SELECT * FROM album WHERE id = '".mysqli_real_escape_string($server,$album)."'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	return $row['title'];
}
$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(isset($_REQUEST['delete'])){
	if(userRole($userID) > 2) {
	header('location: admin.php');
	}
	$book = filterinp($_REQUEST['delete']);
	$sql=$query="SELECT * FROM album WHERE id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$file = $row['url'];
	$sql=$query= "DELETE FROM gallery WHERE id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	unlink('media/uploads/'.$file);

	$message = "The selected photo was successfully deleted.";
	$class="green";

}

if(isset($_REQUEST['deleteAlbum'])){
	if(userRole($userID) > 2) {
	header('location: admin.php');
	}
	$book = filterinp($_REQUEST['deleteAlbum']);
	$sql=$query= "DELETE FROM album WHERE id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));

	$message = "The selected aplbum was successfully deleted.";
	$class="green";
}


if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])){

	if(userRole($userID) > 2) {
	header('location: admin.php');
	}
	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
	$sql=$query="SELECT * FROM gallery WHERE id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update Photo
<?php
	} else {
		$book = '';
		$title = '';
?>
<div id="add-new">
   <div id="add-new-head">Add Photo
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/admingallery" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Choose Image:</td>
        <td  align="left" valign="middle">
        	<input style="width: 40%;" type="file" <?php if(!isset($_REQUEST['edit'])) { echo 'required="required"';} ?> name="image" accept="image/*" >
        </td>
       </tr>

      <tr>
        <td align="left" valign="middle">Caption:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" id="title2" required="required" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Album:</td>
        <td  align="left" valign="middle">
			<select name="album_id" id="e1" style="width:90%">
            	<?php
					$sql=$query= "SELECT * FROM album WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
					$result = mysqli_query($server, $query);
					$num = mysqli_num_rows($result);
					if($num < "1"){ echo '<option value="0">Please Create a new album first</option>'; }
							$i=0;
							while($row = mysqli_fetch_assoc($result)){
									$aid = $row['id'];
									$atitle = $row['title'];
									$select = '';
									if($aid == $row['album_id']) {$select = 'selected';}
									echo '<option value="'.$aid.'" '.$select.'>'.$atitle.'</option>';
									$i++;
							}
				?>
            </select>
        </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td  align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="subject" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Photo</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Photo</button>
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

if(isset($_REQUEST['editAlbum']) || isset($_REQUEST['newAlbum'])){

	if(userRole($userID) > 2) {
	header('location: admin.php');
	}
	if(isset($_REQUEST['editAlbum'])) {
	$book = filterinp($_REQUEST['editAlbum']);
		$sql=$query="SELECT * FROM album WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update Album
<?php
	} else {
		$book = '';
		$title = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Album
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/admingallery" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" required="required" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td  align="left" valign="top">
        <?php if(isset($_REQUEST['editAlbum'])) { ?>
        <input type="hidden" name="saveAlbum" value="yes" />
        <input type="hidden" name="subject" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Album</button>
        <?php } else { ?>
        <input type="hidden" name="add_album" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Create Album</button>
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

		//create new photo
		if($album_id < 1){
			$message = 'Sorry but you need to select an album first!';
			$class = 'yellow';
		} else {
			$upload_path = 'media/uploads/';
			$file1 = $_FILES['image']['name'];
			$filename1 = time().$_FILES['image']['name'];
			$ext = end(explode(".", $_FILES['image']['name']));
			$allowed = array("jpg","jpeg","gif","png","swf","bmp","",);
			if(!in_array(strtolower($ext), $allowed)) {
				//This file format is not allowed
				$message = "The uploaded file format is not allowed"; $class = "red";
			} else {
				//update father photo if set
				if($file1 !=="") {
					move_uploaded_file($_FILES['image']['tmp_name'],$upload_path . $filename1);
					$sql=$query="INSERT INTO gallery (`id`, `school_id`,`album_id`, `title`, `type`, `url`) VALUES (NULL, '$school_id','$album_id', '$title','Image','$filename1');";
					mysqli_query($server, $query) or die(mysqli_error($server));

					$message = 'The new photo was succesfully saved.';
					$class = 'green';
				} else {
					$message = 'An error was encountered while uploading your photo. Please try again.';
					$class = 'red';
				}
			}
		}
}

if(isset($_POST['add_album'])){
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

		//create new photo
			$sql=$query="INSERT INTO album (`id`, `school_id`, `title`) VALUES (NULL, '$school_id', '$title');";
			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new album was succesfully created.';
	$class = 'green';
}

if(isset($_POST['save'])){
$subject = $_POST['subject'];
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

	$sql=$query="UPDATE `gallery` SET `title` =  '$title', `album_id` =  '$album' WHERE `id` = '$subject';";
	mysqli_query($server, $query) or die(mysqli_error($server));

	$upload_path = 'media/uploads/';
	$file1 = $_FILES['image']['name'];
	$filename1 = time().$_FILES['image']['name'];
	$ext = end(explode(".", $_FILES['image']['name']));
	$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
	if(!in_array(strtolower($ext), $allowed)) {
		//This file format is not allowed
		$message = "The uploaded file format is not allowed"; $class = "red";
	} else {
		//update father photo if set
		if($file1 !=="") {
			move_uploaded_file($_FILES['image']['tmp_name'],$upload_path . $filename1);
			$sql=$query="UPDATE `gallery` SET `url` =  '$filename1' WHERE `id` = '$subject';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}

		$message = 'The selected photo was succesfully updated.';
		$class = 'green';
	}
}

if(isset($_POST['saveAlbum'])){
$subject = $_POST['subject'];
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

	$sql=$query="UPDATE `album` SET `title` =  '$title' WHERE `id` = '$subject';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$message = 'The selected album was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword'])){
$category = filterinp($_GET['category']);
	$added = '';
	if(isset($_GET['album'])) 	{
	$album = mysqli_real_escape_string($server,$_GET['album']);
	$added = " AND (album_id = '".$album."')";
	}

	$school_id = $school_id;

	$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($server,$_GET['keyword']);
	$ser = explode(' ', mysqli_real_escape_string($server,$_GET['keyword']));
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "p.title LIKE '%$term%' OR p.url LIKE '%$term%'".$added;
	    }
	    else
	    {
	         $clauses[] = "p.title LIKE '%%'".$added;
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query= "select * FROM gallery p WHERE p.school_id = '$school_id' AND $filter ";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No photos found! Please try another search term.";
		$class="blue";
		}
} else {
	//display albums
	$sql=$query= "SELECT * FROM album WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no albums in your school";
		$class="blue";
		}
}

if(isset($_REQUEST['album']) && $_REQUEST['album'] > 0) {
	//display photos
	$album = mysqli_real_escape_string($server,$_GET['album']);
	$sql=$query= "SELECT * FROM gallery WHERE album_id = '$album' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no photos in the selected album";
		$class="blue";
		}
} else {
	//display albums
	$sql=$query= "SELECT * FROM album WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no albums in your school";
		$class="blue";
	}
}

$add_album = "";
if(isset($_REQUEST['album'])) {
	$add_album = "&album=".$_REQUEST['album'];	
}
?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <input type="search" name="keyword" placeholder="Search Photos" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3 ) { ?>
        <a href="admin/admingallery?newAlbum"><button type="button" class="submit">Add Album</button></a>
        <a href="admin/admingallery?new<?=$add_album?>"><button type="button" class="submit success">Add Photos</button></a>
        <?php } ?>
        </form>
    </div>
 <?php if(@$album > 0) { ?>
    <h3><?php echo albumName($album) ?> &nbsp;&nbsp;<small><a class="btn btn-default" href="admin/admingallery">Back to Albums</a></small></h3>
 <?php } ?>
	<div class="panel row" style="border-color: transparent;">
               <?php
if(@$album > 0) {
		//display photos
		$i=0;
		while($row = mysqli_fetch_assoc($result)){
				$id = $row['id'];
				$title = $row['title'];
				$image = $row['url'];
				$start_mark = $row['album_id'];

				?>
		      <div class="col-sm-12 col-md-4 photos">
		        <a class="fancybox" rel="gallery" href="<?php echo 'media/uploads/'.$image;?>"><img title="<?php echo $title; ?>" src="<?php echo 'media/uploads/'.$image;?>" /></a>
		        <a class="fancybox" rel="gallery" href="<?php echo 'media/uploads/'.$image;?>"><button>View</button></a>
		        <a href="admin/admingallery?album=<?php echo $album; ?>&edit=<?php echo $id;?>"><button class="warning">Edit</button></a>
		        <a href="admin/admingallery?album=<?php echo $album; ?>&delete=<?php echo $id;?>"><button class="danger">Remove</button></a>
		       </div>
		      <?php
				$i++;
		}
} else {
		//display albums
			$i=0;
			while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];

				?>
              <div class="col-sm-12 col-md-4 photos">
                <a href="admin/admingallery?album=<?php echo $id;?>"><album><i class="fa fa-photo"></i><br /><?php echo $title.'<br>('.countPhotos($id).' Photos)'; ?></album></a><br>
                <a href="admin/admingallery?album=<?php echo $id;?>"><button>Photos</button></a>
                <a href="admin/admingallery?editAlbum=<?php echo $id;?>"><button class="warning">Edit</button></a>
                <a href="admin/admingallery?deleteAlbum=<?php echo $id;?>"><button class="danger">Remove</button></a>
               </div>
              <?php
					$i++;
			}

}
		  ?>

<?php displayPagination($setLimit,$page,$query) ?>

    </div>
</div>
