<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		usersubject.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			10/03/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) != 6) {
header('location: index.php');
}

$session_id = getSetting('current_session');
$term_id = getSetting('current_term');
$class_id = getClass(userProfile($userID),$session_id);
if(isset($_GET['keyword'])) {
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
	         $clauses[] = "p.title LIKE '%$term%' ";
	    }
	    else
	    {
	         $clauses[] = "p.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM subject p WHERE p.school_id = '$school_id' AND p.class_id = '$class_id' AND $filter ";

 	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM subject WHERE school_id = '$school_id' AND class_id = '$class_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no subjects for your class";
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
        <input type="search" name="keyword" placeholder="Search Subject" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="35%" scope="col">Title </th>
                <th width="20%" scope="col">Teacher</th>
                <th width="20%" scope="col">Action</th>
              </tr>
               <?php

				 $i=0;
 		 	 while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$content = $row['content'];
					$class = teacherName(getSubjectTeacher($row['id']));

				?>
             <div class="virtualpage hidepeice">
              <tr class="inner">
                <td width="10%"> <?php echo sprintf($id); ?></td>
                <td width="35%"> <?php echo $title; ?></td>
                <td width="20%"> <?php echo $class; ?></td>
                <td width="20%" valign="middle">
                 <?php if(!empty($content)) { ?>
                <a href="media/uploads/<?php echo $content;?>"><button class="btn-success">Download Syllabus</button></a>
                <?php } ?>
                </td>
              </tr>
              </div>
              <?php $i++; } ?>
              </table>

<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
</div>
