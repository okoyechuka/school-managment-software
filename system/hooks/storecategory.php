<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		storecategory.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			5/11/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

function countItems($category) {
	global $server;

$sql=$query = "SELECT * FROM stock WHERE category_id = '$category'  AND school_id = '$school_id'";
	$result = mysqli_query($server, $sql);
return	$num = mysqli_num_rows($result);
}

if(userRole($userID) > 2 && userRole($userID) != 10) {
header('location: admin.php');
}

if(isset($_REQUEST['delete']))
{
	$book = filterinp($_REQUEST['delete']);
	$sql=$query = "DELETE FROM stock_category WHERE id = '$book'  AND school_id = '$school_id'";
	$result = mysqli_query($server, $sql) or die(mysqli_error($server));

	$message = "The selected category was successfully deleted.";
	$class="green";
}




if(isset($_REQUEST['edit']) || isset($_REQUEST['new']))
{
	if(isset($_REQUEST['edit'])) {
	$category = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM stock_category WHERE id = '$category' AND school_id = '$school_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['$title']; ?>
<?php
	} else {
		$category = '';
?>
<div id="add-new">
   <div id="add-new-head">Create New Category
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/storecategory" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Category ID:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="category" id="id" readonly="readonly" placeholder="Auto Assign" value="<?php echo @$row['id']; ?>">
        </td>
      <tr>
        <td align="left" valign="middle">Name:</td>
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
        <input type="hidden" name="category" value="<?php echo $category; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Category</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Category</button>
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
			$sql=$query = "INSERT INTO stock_category (`id`, `school_id`, `title`) VALUES (NULL, '$school_id', '$title');";
			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new category was succesfully created.';
	$class = 'green';

}

if(isset($_POST['save']))
{
$category = $_POST['category'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "category") && ($key != "save") && ($key != "photo") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
			$sql=$query="UPDATE `stock_category` SET `$key` =  '$value' WHERE `id` = '$category';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}



	$message = 'The selected category was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword']))
{
$category = filterinp($_GET['category']);

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
	$sql=$query = "select * FROM stock_category p WHERE p.school_id = '$school_id' AND $filter ";

 	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search term.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM stock_category WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no categories to display";
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
        <input type="search" name="keyword" placeholder="Search Categories" style=""/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3 || userRole($userID) == 8) { ?>
        <a href="admin/storecategory?new"><button type="button" class="submit">Add <hide>New</hide> Category</button></a>
        <a href="admin/store"><button type="button" class="submit">Store Items</button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="10%" scope="col">ID</th>
                <th width="35%" scope="col">Category Name</th>
                <th width="20%" scope="col">Item Count</th>
                <th width="20%" scope="col">Action</th>
              </tr>
               <?php
				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$available = countItems($id).' Item(s)';

				?>
              <tr class="inner">
                <td width=""> <?php echo $id; ?></td>
                <td width=""> <?php echo $title; ?></td>
                <td width=""> <?php echo $available; ?></td>
                <td width="" valign="middle">
                <a href="admin/storecategory?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/storecategory?delete=<?php echo $id;?>"><button class="danger">Remove</button></a>
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
