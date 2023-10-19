<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		hostel.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			4/03/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) > 2) {
header('location: admin.php');
}

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

if(isset($_REQUEST['delete']))
{
	$book = filterinp($_REQUEST['delete']);
	if(!hostelHasStudent($book, getSetting('current_session'))) {
		$sql=$query = "DELETE FROM hostel WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));

		$message = "The selected hostel was successfully deleted.";
		$class="green";
	} else {
		$message = "Sorry but you cannot delete this hostel as students are currently assigned to the selected hostel.";
		$class="yellow";
	}
}


if(isset($_REQUEST['edit']) || isset($_REQUEST['new']))
{
	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM hostel WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['$title']; ?>
<?php
	} else {
		$book = '';

?>
<div id="add-new">
   <div id="add-new-head">Create New Hostel
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/hostel" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Hostel ID:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="category" id="id" readonly="readonly" placeholder="Auto Assign" value="<?php echo @$row['id']; ?>">
        </td>
       </tr>
      <tr>
        <td align="left" valign="middle">Hostel Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" required="required" placeholder="" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="hostel" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Hostel</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Hostel</button>
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
			$sql=$query = "INSERT INTO hostels (`id`, `school_id`, `title`) VALUES (NULL, '$school_id', '$title');";
			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new hostel was succesfully created.';
	$class = 'green';

}



if(isset($_POST['save']))
{
$hostel = $_POST['hostel'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "hostel") && ($key != "save") && ($key != "photo") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
			$sql=$query="UPDATE `hostels` SET `$key` =  '$value' WHERE `id` = '$hostel';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}



	$message = 'The selected hostel was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['view']))
{

	$hostel = $_REQUEST['view'];

?>
<div id="add-new">
   <div id="add-new-head">View Hostel Occupants

       <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>

   <div class="inside" style="background-color: #fff;">

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
     <td align="left" colspan="3" valign="middle"><br /></td>
    </tr>
      <tr>
        <td align="left" valign="middle"><blue>Student</blue></td>
        <td align="left"  valign="middle"><blue>Class</blue></td>
      </tr>
   <?php
   $sql=$query="SELECT * FROM student_hostel WHERE hostel_id = '$hostel'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

   $i=0;
	 while($row = mysqli_fetch_assoc($result)){
		 ?>
   		<tr>

        <td align="left"  valign="middle"><?php echo studentName($row['student_id']); ?></td>
        <td align="left" valign="middle"><?php echo className(getClass($row['student_id'],$currentSession)); ?></td>
        </td>
    	</tr>
        <?php
			$i++;
		} 	?>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" colspan="2" valign="top">&nbsp;</td>
      </tr>
    </table>

	</div>
</div>
<?php
}


if(isset($_GET['keyword'])){

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
	$sql=$query = "select * FROM hostels p WHERE p.school_id = '$school_id' AND $filter ";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM hostels WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no hostels created for your school";
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
        <input type="search" name="keyword" placeholder="Search Hostels" />
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/hostel?new"><button type="button" class="submit success">Add Hostel</button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="35%" scope="col">Hostel Name</th>
                <th width="20%" scope="col">Occupants Count</th>
                <th width="20%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$available = countHostel($id).' Student(s)';

				?>
              <tr class="inner">
                <td width=""> <?php echo sprintf('%05d',$id); ?></td>
                <td width=""> <?php echo $title; ?></td>
                <td width=""> <?php echo $available; ?></td>
                <td width="" valign="middle">
                <a href="admin/hostel?view=<?php echo $id;?>"><button>Occupants</button></a>
                <a href="admin/hostel?edit=<?php echo $id;?>"><button class="warning">Edit</button></a>
                <a href="admin/hostel?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
                </td>
              </tr>
              <?php
						$i++;
					} ?>
              </table>

<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
</div>
