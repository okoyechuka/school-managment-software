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
if(!isset($_REQUEST['course'])){
	header('location: courses');
} else {
	$course_id = filterinp($_REQUEST['course']);
	$query="SELECT * FROM e_courses WHERE id = '$course_id' AND school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$course = mysqli_fetch_assoc($result);
	if($course['id'] < 1 ) header('location: courses');
	if(userRole($userID) == 4) {
		if($course['teacher_id'] != userProfile($userID)) {
			header('location: courses');
		}
	}
}
if(isset($_REQUEST['delete'])){
	$book = filterinp($_REQUEST['delete']);
	$sql=$query = "DELETE FROM e_courses_contents WHERE id = '$book' AND course_id = '$course_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$message = "The selected leason was successfully deleted.";
	$class="green";
}

if(isset($_REQUEST['view']) && is_numeric($_REQUEST['view'])){
	$book = filterinp($_REQUEST['view']);
	$sql=$query="SELECT * FROM e_courses_contents WHERE id = '$book' AND course_id = '$course_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head"><?php echo shorten($row['title'],65); ?>
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Course:</td>
        <td  align="left" valign="middle"><?php echo $course['title']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Lesson:</td>
        <td  align="left" valign="middle"><?php echo $row['title']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assigned Teacher:</td>
        <td  align="left"><?=$course['teacher_id']>0?teacherName($course['teacher_id']):"Not Assigned" ?> </td>
      </tr>
	  <?php if(!empty($row['document'])) { ?>
      <tr>
        <td align="left" colspan="2" valign="top" ><br><center><a data-featherlight-iframe-webkitallowfullscreen="true" data-featherlight-iframe-allowfullscreen="true" data-featherlight-iframe-style="display:block;border:none;height:85vh;width:85vw;" data-featherlight="iframe" target="_blank" href="media/uploads/<?=$row['document']; ?>"><button class="btn btn-lg btn-success"> <i class="fa fa-folder"></i> Open Lesson (PDP File)</button></a></center></td>
      </tr>
      <?php } ?>
      <?php if(!empty($row['video'])) { $ext = strtolower(@end(explode(".", $row['video']))); ?>
      <tr>
        <td align="left" colspan="2" valign="top" align="center"><br><h4>Watch Lesson</h4><center>
        <video width="560" height="345" poster="media/uploads/videoplayer.png" controls>
                  <source src="media/uploads/<?=$row['video']?>" type="video/<?=$ext?>">
                Your browser does not support the video player. Try Google Chrome
         </video></center>
         </td>
      </tr>
      <?php } ?>
      <?php if(!empty($row['youtube'])) { ?>
      <tr>
        <td align="left" colspan="2" valign="top" align="center"><br><h4>Watch Lesson</h4><center>
        <iframe width="560" height="345"  src="https://www.youtube.com/embed/<?=$row['youtube']?>"> </iframe></center>
         </td>
      </tr>
      <?php } ?>
      <?php if(!empty($row['audio'])) { $ext = strtolower(@end(explode(".", $row['audio']))); ?>
      <tr>
        <td align="left" colspan="2" valign="top" align="center"><br><h4>Play Lesson</h4><center>
        	<audio style="width:100%" controls>
              <source src="media/uploads/<?=$row['audio']?>" type="audio/<?=$ext=="mp3"?"mpeg":$ext?>">
            Your browser does not support the audio element. Try Google Chrome
            </audio></center>
        </td>
      </tr>
      <?php } ?>
      <?php if(!empty($row['text'])) { ?>
      <tr>
        <td align="left" colspan="2" valign="top"><hr><br><holdd><?php echo @$row['text']; ?></holdd></td>
      </tr>
      <?php } ?>
      </table>
    </div>
</div>    
<?php 
} 

if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])){
	$type="Text";
	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
	$query="SELECT * FROM e_courses_contents WHERE id = '$book' AND course_id = '$course_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$type="Text";
	if(!empty($row['audio'])) $type="Audio";
	if(!empty($row['youtube'])) $type="YouTube";
	if(!empty($row['video'])) $type="Video";
	if(!empty($row['document'])) {$type = "File"; }
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['title']; ?>
<?php	} else {
		$book = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Lesson
<?php
	}
?>
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
    <form method="post" action="admin/coursecontent?course=<?=$course_id?>" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Lesson Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" required="required" placeholder="" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>
       <tr>
        <td align="left" valign="middle">Content Type</td>
        <td>
           <select name="type" id="content_type" style="width: 90%;" >
        	  <option <?=$type=="Text"?'selected':''?> value="text">Text / HTML</option>
              <option <?=$type=="Video"?'selected':''?> value="video">Video Lesson</option>
              <option <?=$type=="YoouTube"?'selected':''?> value="youtube">YouTube Video</option>
              <option <?=$type=="Audio"?'selected':''?> value="audio">Audio Lesson</option>
              <option <?=$type=="File"?'selected':''?> value="file">File (PDF)</option>
			</select>
        </td>
      </tr>
      
      <tr id="show_file">
        <td align="left" valign="middle">Upload <span id="file-mes">PDF Document</span>:</td>
        <td  align="left" valign="middle">
        	<input type="file" style="width: 70%" name="upload" id="uploads" >
        </td>
      </tr>
      
      <tr id="show_youtube">
        <td align="left" valign="middle">YouTube Video ID:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="youtubeLink"  placeholder="Type only the video ID" value="<?php echo @$row['youtube']; ?>">
        </td>
      </tr>
      
      <tr id="show_text">
        <td align="left" colspan="2" valign="middle"><strong><blue>Lesson:</td>
      </tr>
      <tr id="show_text2">
        <td align="left" valign="top" colspan="2">
        	<textarea placeholder="Type course overview here" id="textMessage" class="ckeditor" name="text"  style="height: 400px; width:98%;" ><?php echo @$row['text']; ?>&nbsp;</textarea>
      </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="class" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Lesson</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Save Lesson</button>
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
	$texts = $video = $audio = $document = "";
	$error = 0;
	if($type != "text" && $type != "youtube") {
		$allowed = array("mp4","");
		if($type == "audio") {
			$allowed = array("mp3","wav","");
		}
		if($type == "file") {
			$allowed = array("pdf","xps","");
		}
		$upload_path = 'media/uploads/';
		$file1 = $_FILES['upload']['name'];
		$filename1 = date("d-m-Y").$_FILES['upload']['name'];
		$ext = end(explode(".", $_FILES['upload']['name']));
		if(!in_array(strtolower($ext), $allowed)) {
			$message = "The uploaded file format is not allowed"; $class = "red";
			$file1 = "";
			$error = 1;
		} 
		if($file1 !== "") {
			move_uploaded_file($_FILES['upload']['tmp_name'],$upload_path . $filename1);
			if($type == "audio") {
				$audio = $filename1;
			}
			if($type == "video") {
				$video = $filename1;
			}
			if($type == "file") {
				$document = $filename1;
			}
		} else {
			$message = "You have not uploaded any leason content."; $class = "red";
			$error = 1;
		}
	} else {
		if($type == "text") {
			$texts = filterinp($_POST['text'],true);
			if(empty($texts)) {
				$message = "You have not provided any leason content."; $class = "red";
				$error = 1;
			}
		}
		if($type == "youtube") {
			$youtubeLink = filterinp($_POST['youtubeLink']);
			if(empty($youtubeLink)) {
				$message = "You have not provided any YouTube Video ID."; $class = "red";
				$error = 1;
			}
		} 
	}
	if($error < 1) { 
	//create new prents
	$datetime = date('Y-m-d H:i:s');
	$query ="INSERT INTO e_courses_contents (`datetime`, `title`, `course_id`, `text`, `video`, `audio`, `school_id`, `document`, `youtube`) VALUES ('$datetime', '$title', '$course_id', '$texts', '$video', '$audio', '$school_id', '$document', '$youtubeLink');";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$leasn_id = mysqli_insert_id($server);
	$message = 'The new Lesson was succesfully created. <a href="admin/coursecontent?view='.$leasn_id.'">Click Here</a> to preview the Lesson now';
	$class = 'green';
	}
}

if(isset($_POST['save'])){
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$texts = $video = $audio = $document = "";
	$error = 0;
	if($type != "text" && $type != "youtube") {
		$allowed = array("mp4","");
		if($type == "audio") {
			$allowed = array("mp3","wav","");
		}
		if($type == "file") {
			$allowed = array("pdf","xps","");
		}
		$upload_path = 'media/uploads/';
		$file1 = $_FILES['upload']['name'];
		$filename1 = date("d-m-Y").$_FILES['upload']['name'];
		$ext = end(explode(".", $_FILES['upload']['name']));
		if(!in_array(strtolower($ext), $allowed)) {
			$message = "The uploaded file format is not allowed"; $class = "red";
			$file1 = "";
			$error = 1;
		} 
		if($file1 !== "") {
			move_uploaded_file($_FILES['upload']['tmp_name'],$upload_path . $filename1);
			if($type == "audio") {
				$audio = $filename1;
			}
			if($type == "video") {
				$video = $filename1;
			}
			if($type == "file") {
				$document = $filename1;
			}
		} 
	} else {
		$texts = filterinp($_POST['text'],true);
		$youtubeLink = filterinp($_POST['youtubeLink']);
	}
	if(empty($texts) && $type == "text") {
		$message = "You have not provided any Lesson content."; $class = "red";
		$error = 1;
	}
	if(empty($youtubeLink) && $type == "youtube") {
		$message = "You have not provided any YouTube Video ID."; $class = "red";
		$error = 1;
	}

	if($error < 1) { 
	mysqli_query($server,"UPDATE `e_courses_contents` SET 
	`title` =  '$title',
	`youtube` =  '$youtubeLink',
	`text` =  '$texts' 
	WHERE `id` = '$class' AND course_id = '$course_id'") or die(mysqli_error($server));
	if($file1 !== "") {
	mysqli_query($server,"UPDATE `e_courses_contents` SET 
	`video` =  '$video',
	`audio` =  '$audio',
	`document` =  '$document' 
	WHERE `id` = '$class' AND course_id = '$course_id'") or die(mysqli_error($server));
	}
	$message = 'The selected Lesson was succesfully updated.';
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
	         $clauses[] = "p.title LIKE '%$term%' OR p.text LIKE '%$term%'";
	    }	    else	    {
	         $clauses[] = "p.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM e_courses_contents p WHERE p.course_id = '$course_id' AND $filter ";
 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "SELECT * FROM e_courses_contents WHERE course_id = '$course_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")		{
		$message = "There are currently no Lessons created for this course";
		$class="blue";
	}
}
?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="admin/coursecontent?course=<?=$course_id?>" method="get">
        <input type="search" name="keyword" placeholder="Search Lessons" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <a href="admin/coursecontent?new&course=<?=$course_id?>"><button type="button" class="submit">Add <hide>Lesson</hide></button></a>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="5%" scope="col">ID</th>
                <th width="35%" scope="col">Lesson Title</th>
                <th width="15%" scope="col">Content Type</th>
                <th width="15%" scope="col">Created On</th>
                <th width="30%" scope="col">Action</th>
              </tr>
               <?php $i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];	$title = $row['title'];
					$type="Text";
					if(!empty($row['audio'])) $type="Audio";
					if(!empty($row['youtube'])) $type="Video";
					if(!empty($row['video'])) $type="Video";
					if(!empty($row['document'])) {
						$ext = @end(explode(".", $row['document'])); 
						$type = "File (".strtoupper($ext).")"; 
					}
				?>
              <tr class="inner">
                <td> <?php echo $id; ?></td>
                <td> <?php echo $title; ?></td>
                <td> <?php echo $type; ?></td>
                <td> <?php echo date('F, j Y',strtotime($row['datetime'])); ?></td>
                <td valign="middle">
                <a href="admin/coursecontent?view=<?php echo $id;?>&course=<?=$course_id?>"><button>View Lesson</button></a>
                <?php if(userRole($userID) < 3 || userRole($userID)==4) { ?>
                <a href="admin/coursecontent?edit=<?php echo $id;?>&course=<?=$course_id?>"><button>Edit</button></a>
                <a href="admin/coursecontent?delete=<?php echo $id;?>&course=<?=$course_id?>"><button class="danger">Delete</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php	$i++; } ?>
              </table>
<?php displayPagination($setLimit,$page,$query) ?>
        </div>
    </div>
</div>
