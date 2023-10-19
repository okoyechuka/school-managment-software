<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		customreportdata.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			2/12/2019
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) > 3 && userRole($userID) != 4) {
header('location: index.php');
}
$session = getSetting('current_session');
$term = getSetting('current_term');
$class="0";

$currentSession = getSetting('current_session');
$currentTerm = getSetting('current_term');

if (getSetting('current_session') < 1) {
$message = 'You have not defined the current accademic session yet!. <br>You must fix this before you can manage fees. ';
	if(userRole($userID) <3) {
	$message .= '<a href="admin/generalsetting" title="Difine Active Session">Click Here</a> to fix it now.';
	} else {
	$message .= 'Consult your administrator for assistance';
	}
$class='yellow';
}

if(isset($_REQUEST['delete'])){
	$delete = filterinp($_REQUEST['delete']);
	if(isset($_REQUEST['yes'])) {
		$sql=$query = "DELETE FROM reportcard_extras WHERE id = '$delete' AND school_id = '$school_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));

		$message = "The record was successfully deleted.";
		$class="green";
	} else {
		$message = "Are you sure you want to delete the selected record? <br><a href='admin/customreportdata?delete=".$id."&yes=1'>Yes I'm sure</a> <a href='admin/customreportdata'>Cancel</a>";
		$class="yellow";
	}
}

if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])){
	if(isset($_REQUEST['edit'])) {
		$book = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM reportcard_extras WHERE id = '$book'";
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
   <div id="add-new-head">Create New Custom Report Data
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/customreportdata" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Record ID:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="id" id="id" readonly="readonly" placeholder="Auto Assign" value="<?php echo @$row['id']; ?>">
        </td>
       </tr>
      <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" id="names" required="required" placeholder="" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle">Assign Class:</td>
      <td>
        <select name="class_id" id="e4" style="width: 90%;" >
        <option  value="<?php echo 0; ?>"><?php echo 'All Classes'; ?></option>
			<?php
                $sql=$query="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title ASC";
                $result2 = mysqli_query($server, $query);
				$i=0;
				while($rows = mysqli_fetch_assoc($result2)){
                $c_id = $rows['id'];
                $title = $rows['title'];
            ?>
               <option <?php if(@$row['class_id'] == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php  $i++;
				}   ?>
			</select>
        </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="reportcard_extras" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Custom Report Data</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Custom Report Data</button>
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
	//create new prents
	$query = "INSERT INTO reportcard_extras (`id`, `school_id`, `title`, `class_id`, `parent_only`)
	VALUES (NULL, '$school_id', '$title', '$class_id', '0');";
	mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new custom report data was succesfully created.';
	$class = 'green';

}

if(isset($_POST['save'])){
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

	//create new prents
	$sql=$query="UPDATE `reportcard_extras` SET `class_id` =  '$class_id' WHERE `id` = '$reportcard_extras';";
	mysqli_query($server, $query) or die(mysqli_error($server));
	$sql=$query="UPDATE `reportcard_extras` SET `title` =  '$title' WHERE `id` = '$reportcard_extras';";
	mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The selected custom report data was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword'])){

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
	    if (!empty($term))
	    {
	         $clauses[] = "title LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "f.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM reportcard_extras WHERE school_id = '$school_id' AND $filter";

 	$resultF = mysqli_query($server, $query) or die(mysqli_error($server));
	$numF = mysqli_num_rows($resultF);

	if($numF < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "SELECT * FROM reportcard_extras WHERE school_id = '$school_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";

	$resultF = mysqli_query($server, $query);
	$numF = mysqli_num_rows($resultF);
	if($numF < "1")	{
		$message = "There are no custom report data created for your school";
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
        <input type="search" name="keyword" placeholder="Search Custom Report Data" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3) { ?>
        <a href="admin/customreportdata?new"><button type="button" class="submit success">Add <hide>Custom Report Data</hide></button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="25%" scope="col">Title</th>
                <th width="19%" scope="col">Assigned Class</th>
                <th width="20%" scope="col">Action</th>
              </tr>
               <?php

				while($row=mysqli_fetch_assoc($resultF)){
					$id = $row['id'];
					$class = $row['class_id'];
					$title = $row['title'];


				?>
              <tr class="inner">
                <td width=""> <?php echo $title; ?></td>
                <td width=""> <?php echo className($class); ?></td>
                <td width="" valign="middle">
				<?php if(userRole($userID)<3) { ?>
                <a href="admin/customreportdata?edit=<?php echo $id;?>"><button class="btn-warning">Edit </button></a>
                <a href="admin/customreportdata?delete=<?php echo $id;?>"><button class="btn-danger">Delete</button></a>
                <?php } ?>
                </td>
              </tr>
              <?php
			} ?>
              </table>

<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
</div>
