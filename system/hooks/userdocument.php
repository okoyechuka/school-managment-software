<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		userdocument.php
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
	$result = mysqli_query($server, $sql) or die(mysqli_error($server));

	$message = "The selected document was successfully deleted.";
	$class="green";
}


if(isset($_REQUEST['view']))
{
	$library = $_REQUEST['view'];
		//get students profile
		$sql=$query="SELECT * FROM library WHERE id = '$library'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);


?>
<div id="add-new">
   <div id="add-new-head"><?php echo $row['title']; ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #CCC;">
        	<div class="action-box">
           <a href="<?php echo 'media/uploads/'.$row['url']; ?>" target="_blank"><button class="submit">View/Download</button></a>
           <?php if(userRole($userID) < 3 && userDocument($userID, $_REQUEST['view'])) {  ?>
            <a href="userdocument?edit=<?php echo $library; ?>&done"><button class="submit">Edit</button></a>
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

if(isset($_GET['keyword']))
{
$class_id = $_GET['class'];
$subject_id = $_GET['subject'];

$school_id = $school_id;

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
	         $clauses[] = "p.title LIKE '%$term%' OR p.description LIKE '%$term%' OR p.url LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "p.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM library p WHERE p.school_id = '$school_id' AND $filter ";

 	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM library WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "No teachers records found!";
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
        <input type="search" name="keyword" placeholder="Search Documents" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="40%" scope="col">Title</th>
                <th width="10%" scope="col">File Type</th>
                <th width="13%" scope="col">Created On</th>
                <th width="20%" scope="col">Created By</th>
                <th width="10%" scope="col">Action</th>
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
              <tr class="inner">
                <td width="40%"> <?php echo $title; ?></td>
                <td width="10%"> <?php echo strtoupper($size); ?></td>
                <td width="13%"> <?php echo $date; ?></td>
                <td width="20%"> <?php echo $user; ?></td>
                <td width="10%" valign="middle">
                <a href="userdocument?view=<?php echo $id;?>"><button class="btn-success">View</button></a>
				<?php if(userDocument($userID, $id)) {  ?>
                <a href="userdocument?delete=<?php echo $id;?>"><button class="btn-danger">Remove</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php }
						$i++;
					} ?>

             </table>
<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
</div>
