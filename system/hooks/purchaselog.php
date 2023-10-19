<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		purchaselog.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			8/11/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(!isset($_GET['stock_id'])) {
	header('location: admin/store');
} else {
	$stock_id = filterinp($_GET['stock_id']);
}
function itemName($item) {
	global $server;
$sql=$query = "SELECT * FROM stock WHERE id = '$item'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	return $row['title'];
}

function itemCategoryName($item) {
	global $server;

$sql=$query = "SELECT * FROM stock_category WHERE id = '$item'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	return $row['title'];
}


if(userRole($userID) > 2 && userRole($userID) != 10 && userRole($userID) == 7) {
header('location: admin.php');
}



if(isset($_GET['keyword']))
{
$stock_id = filterinp($_GET['stock_id']);

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
	         $clauses[] = "p.date LIKE '%$term%' OR p.added_by LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "p.date LIKE '%%' OR p.id LIKE '%%' ";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM stock p WHERE $filter ";
	if($category > 0) {
		$sql=$query = "select * FROM purchase p WHERE p.school_id = '$school_id' AND stock_id = '$stock_id' AND $filter ";
	}
 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search term.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM purchase WHERE school_id = '$school_id' AND stock_id = '$stock_id' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no purchase records to display for this item!";
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
        <input type="search" name="keyword" placeholder="Search Purchase Log" style="width:200px"/>
        <button class="submit" type="submit" name="stock_id" value="<?php echo $_GET['sales']; ?>"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3 || userRole($userID) == 10) { ?>
        <a href="admin/store"><button type="button" class="submit">Store Items</button></a>
        <a href="admin/store?addPurchase"><button type="button" class="submit btn-success">Add Purchase</button></a>
        <?php } ?>
        </form>
    </div>
    <h3>Purchase Records for <?php echo itemName(filterinp($_GET['stock_id'])); ?></h3>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="15%" scope="col">Date</th>
                <th width="15%" scope="col">Unit Cost</th>
                <th width="15%" scope="col">Quantity</th>
                <th width="15%" scope="col">Total Cost</th>
                <th width="15%" scope="col">Added By</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$date = date('d M, Y', strtotime($row['date']));
					$price = $row['cost'];
					$added_by = $row['added_by'];
					$quantity = $row['quantity'];

				?>
              <tr class="inner">
                <td > <?php echo $date; ?></td>
                <td > <?php echo $userSymbul.number_format($price, 2); ?></td>
                <td > <?php echo $quantity; ?></td>
                <td > <?php echo $userSymbul.number_format($quantity*$price, 2); ?></td>
                <td > <?php echo $added_by; ?></td>
              </tr>
              <?php
						$i++;
					} ?>
			</table>
<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
</div>
