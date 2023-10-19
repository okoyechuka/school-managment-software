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

$session = getSetting('current_session');
$term = getSetting('current_term');

$student_id = userProfile($userID);
$student_class = getClass($student_id,$session);

if(userRole($userID) != 6) {
header('location: index.php');
}

if(!isset($_REQUEST['course'])){
	header('location: usercourses');
} else {
	$course_id = filterinp($_REQUEST['course']);
	$query="SELECT a.* FROM e_courses a JOIN class_course b ON a.id = b.course_id WHERE b.class_id = '$student_class' AND a.id = '$course_id' AND a.school_id = '$school_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$course = mysqli_fetch_assoc($result);
	if($course['id'] < 1 ) header('location: usercourses');
	$fee = $course['fee_id'];
	if($fee > 0) {
		if(feeTotal($fee) > totalPaid($fee,0,0,0,$student_id)) {
			header('location: usercourses?no_pay='.$fee);
		}
	}
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
      <?php if(!empty($row['youtube'])) { ?>
      <tr>
        <td align="left" colspan="2" valign="top" align="center"><br><h4>Watch Lesson</h4><center>
        <iframe width="560" height="345"  src="https://www.youtube.com/embed/<?=$row['youtube']?>"> </iframe></center>
         </td>
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
		$message = "There are currently no Lessons available for this online course";
		$class="blue";
	}
}
?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="usercoursecontent?course=<?=$course_id?>" method="get">
        <input type="search" name="keyword" placeholder="Search Lessons" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="40%" scope="col">Lesson Title</th>
                <th width="15%" scope="col">Content Type</th>
                <th width="15%" scope="col">Created On</th>
                <th width="30%" scope="col">Action</th>
              </tr>
               <?php $i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];	$title = $row['title'];
					$type="Text";
					if(!empty($row['audio'])) $type="Audio";
					if(!empty($row['video'])) $type="Video";
					if(!empty($row['youtube'])) $type="Video";
					if(!empty($row['document'])) {
						$ext = @end(explode(".", $row['document'])); 
						$type = "File (".strtoupper($ext).")"; 
					}
				?>
              <tr class="inner">
                <td> <?php echo $title; ?></td>
                <td> <?php echo $type; ?></td>
                <td> <?php echo date('F, j Y',strtotime($row['datetime'])); ?></td>
                <td valign="middle">
                <a href="usercoursecontent?view=<?php echo $id;?>&course=<?=$course_id?>"><button>View Lesson</button></a>
                </td>
              </tr>
              <?php	$i++; } ?>
              </table>
<?php displayPagination($setLimit,$page,$query) ?>
        </div>
    </div>
</div>
