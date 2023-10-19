<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		store.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			5/11/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());
function itemExist($item, $school) {
	global $server;
$sql=$query = "SELECT * FROM stock WHERE item = '$item' AND school_id = '$school'";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < 1) { return false ;} else { return true ; }
}

function itemName($item) {
	global $server;
$sql=$query = "SELECT * FROM stock WHERE id = '$item'";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	return $row['title'];
}

function itemCategoryName($item) {
	global $server;
$sql=$query = "SELECT * FROM stock_category WHERE id = '$item'";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	return $row['title'];
}


if(userRole($userID) > 2 && userRole($userID) != 10 && userRole($userID) == 7) {
header('location: admin.php');
}

if(isset($_REQUEST['delete'])){
	$book = filterinp($_REQUEST['delete']);
	$sql=$query = "DELETE FROM stock WHERE id = '$book'";
	$result = mysqli_query($server, $sql) or die(mysqli_error($server));

	$message = "The selected item was successfully deleted.";
	$class="green";
}


if(isset($_REQUEST['view']))
{
	$book = filterinp($_REQUEST['view']);
		//get students profile
		$sql=$query="SELECT * FROM stock WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$title = $row['title'];

?>
<div id="add-new">
   <div id="add-new-head">Viewing <?php echo $title; ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">
        	<div class="action-box">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print</button></a>
            <a href="admin/saleslog?stock_id=<?php echo $book; ?>"><button class="submit">Sales Log</button></a>
            <a href="admin/purchaselog?stock_id=<?php echo $book; ?>"><button class="submit">Purchase Log</button></a>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
     <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle"><?php echo $row['title']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Description:</td>
        <td  align="left" valign="middle"><?php echo $row['description']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Cost Price:</td>
        <td  align="left" valign="middle"><?php echo $userSymbul.number_format($row['cost'],2); ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Selling Price:</td>
        <td  align="left" valign="middle"><?php echo $userSymbul.number_format($row['price'],2); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Quantity in Stock:</td>
        <td  align="left" valign="middle"><?php echo $row['quantity']; ?></td>
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

if(isset($_GET['addPurchase']))
{
	if(!empty($_REQUEST['addPurchase'])) {
	$book = $_REQUEST['addPurchase'];

		$sql=$query="SELECT * FROM stock WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Add Purchase for <?php echo $row['$title']; ?>
<?php
	} else {
		$book = '';
?>
<div id="add-new">
   <div id="add-new-head">Add New Purchase
<?php
}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/store" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
       <tr>
        <td  align="left" valign="middle">Purchased Item:</td>
        <td  align="left" valign="middle">
        <select name="stock_id" style="width:90%">
			<?php
                $sql=$query="SELECT * FROM stock WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($book == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php  	}   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Purchase Date:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="date" id="date1" required="required"  value="<?php echo date('Y-m-d');?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Quantity:</td>
        <td  align="left" valign="middle">
        	<input type="number"  name="quantity" id="quanty" required="required"  value="">
        </td>
      </tr>
       <tr>
        <td align="left" valign="middle">Cost Per Unit:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="cost" id="date1" required="required"  value="">
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="return" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Purchase</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php }

if(isset($_GET['addSale']))
{
	if(!empty($_REQUEST['addSale'])) {
	$book = $_REQUEST['addSale'];
		$sql=$query="SELECT * FROM stock WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Add Sales for <?php echo $row['$title']; ?>
<?php
	} else {
		$book = '';
?>
<div id="add-new">
   <div id="add-new-head">Add Sales Record
<?php
}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/store" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
       <tr>
        <td  align="left" valign="middle">Select Item to Sale:</td>
        <td  align="left" valign="middle">
        <select name="stock_id" id="e2feetpay" style="width:90%">
			<?php
                $sql=$query="SELECT * FROM stock WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

					while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'].' ('.$userSymbul.$row['price'].')';
            ?>
               <option data-amount="<?=$row['price']?>" value="<?php echo $g_id; ?>" <?php if($book == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php
							}   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Sold To:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="buyer" id="buyer" required="required"  value="">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Quantity:</td>
        <td  align="left" valign="middle">
        	<input type="number"  name="quantity" id="quantity" required="required"  value="1">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Price per Item:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="price" id="total" required="required"  value="<?php echo @$row['price']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Date:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="date" id="date2" required="required" value="<?php echo date('Y-m-d'); ?>">
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="issue" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Sales</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php }

if(isset($_REQUEST['edit']) || isset($_REQUEST['new']))
{
	if(isset($_REQUEST['edit'])) {
	$book = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM stock WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$bc = $row['category_id'];
?>
<div id="add-new">
   <div id="add-new-head">Update <?php echo $row['$title']; ?>
<?php
	} else {
		$book = '';
		$bc = ''
?>
<div id="add-new">
   <div id="add-new-head">Create New Item
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/store" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
       <tr>
        <td  align="left" valign="middle">Store Category:</td>
        <td  align="left" valign="middle">
        <select name="category_id" style="width:90%">
			<?php
                $sql=$query="SELECT * FROM stock_category WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

								$i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($bc == $g_id) { echo 'selected'; }?>><?php echo $title; ?></option>
            <?php  $i++;
							}   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" required="required" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Description:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="description" id="description" value="<?php echo @$row['description']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Quantity in Stock:</td>
        <td  align="left" valign="middle">
        	<input type="number"  name="quantity" id="quantity"  value="<?php echo @$row['quantity']; ?>">
        </td>
      <tr>
        <td align="left" valign="middle">Cost Price:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="cost" id="cost"  value="<?php echo @$row['cost']; ?>">
        </td>
      <tr>
        <td align="left" valign="middle">Selling Price:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="price" id="price"  value="<?php echo @$row['price']; ?>">
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="book" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Item</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Item</button>
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
		if(!itemExist($title, $school_id)) {
			$sql=$query = "INSERT INTO stock (`id`, `school_id`, `category_id`, `title`, `description`, `quantity`, `cost`,`price`)
			VALUES (NULL, '$school_id', '$category_id', '$title', '$description', '$quantity', '$cost', '$price');";
			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new item was succesfully added to your store.';
	$class = 'green';
	} else {
	$message = 'Sorry but this item already exist. Please adding a defferent item';
	$class = 'yellow';
	}

}

if(isset($_POST['issue']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$user_id = getAdminName(getAdmin());
		//create new prents

			$sql=$query = "INSERT INTO sales (`id`, `school_id`, `buyer`, `added_by`, `date`, `quantity`, `price`, `stock_id`)
						VALUES (NULL, '$school_id', '$buyer', '$user_id', '$date', '$quantity', '$price', '$stock_id');";
			mysqli_query($server, $query) or die(mysqli_error($server));

	//reduce stock
	$sql=$query = "UPDATE stock SET quantity = quantity - '$quantity' WHERE id = '$stock_id'";
	mysqli_query($server, $query) or die(mysqli_error($server));
	if($price > 0) {
		//add to account as income
		$approvedBy = getAdmin();
		$status = 'Completed';
		$direction = 'IN';
		$description = 'Sales Income from '.$quantity.' units of '.itemName($stock_id);
		$amount = $price*$quantity;
		$sql=$query = "INSERT INTO transactions (`id`, `school_id`, `date`, `description`, `status`, `direction`, `amount`, `approvedBy`)
		 VALUES (NULL, '$school_id', '$date', '$description','$status', '$direction','$amount', '$approvedBy');";
		mysqli_query($server, $query) or die(mysqli_error($server));

	}

	$message = 'Sales Record created successfully.';
	$class = 'green';

}

if(isset($_POST['return'])){
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$user_id = getAdminName(getAdmin());
		//create new prents
			$sql=$query = "INSERT INTO purchase (`id`, `school_id`, `added_by`, `date`, `quantity`, `cost`, `stock_id`)
				VALUES (NULL, '$school_id', '$user_id', '$date', '$quantity', '$cost', '$stock_id');";
			mysqli_query($server, $query) or die(mysqli_error($server));

	//reduce stock
	$sql=$query = "UPDATE stock SET quantity = quantity + '$quantity' WHERE id = '$stock_id'";
	mysqli_query($server, $query) or die(mysqli_error($server));
	if($cost > 0) {
		//add to account as income
		$approvedBy = getAdmin();
		$status = 'Completed';
		$direction = 'OUT';
		$description = 'Purchase of '.$quantity.' units of '.itemName($stock_id);
		$amount = $cost*$quantity;
		$sql=$query = "INSERT INTO transactions (`id`, `school_id`, `date`, `description`, `status`, `direction`, `amount`, `approvedBy`)
		VALUES (NULL, '$school_id', '$date', '$description','$status', '$direction','$amount', '$approvedBy');";
		mysqli_query($server, $query) or die(mysqli_error($server));

	}
	$message = 'Purchase record successfully saved. Stock level was also updated for this item';
	$class = 'green';

}

if(isset($_POST['save']))
{
$book = $_POST['book'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "book") && ($key != "save") && ($key != "photo") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
			$sql=$query="UPDATE `stock` SET `$key` =  '$value' WHERE `id` = '$book';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}



	$message = 'The selected item was succesfully updated.';
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
	         $clauses[] = "sp.title LIKE '%$term%' OR p.title LIKE '%$term%' OR p.description LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "p.cost LIKE '%%' OR p.id LIKE '%%' OR p.description LIKE '%%' OR p.title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM stock p WHERE $filter ";
	if($category > 0) {
		$sql=$query = "select * FROM stock p JOIN store_category sp ON p.category_id = sp.id WHERE p.school_id = '$school_id' AND $filter ";
	}
 	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search term.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM stock WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "There are currently no items in your store!";
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
        <input type="search" name="keyword" placeholder="Search Store Items"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3 || userRole($userID) == 10) { ?>
        <a href="admin/storecategory"><button type="button" class="submit">Categories</button></a>
        <a href="admin/store?new"><button type="button" class="submit">Create Item</button></a>
        <a href="admin/store?addSale"><button type="button" class="submit">Add Sales</button></a>
        <a href="admin/store?addPurchase"><button type="button" class="submit">Add Purchase</button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="22%" scope="col">Title</th>
                <th width="15%" scope="col">Category</th>
                <th width="13%" scope="col">Price</th>
                <th width="10%" scope="col">Stock</th>
                <th width="33%" scope="col">Action</th>
              </tr>
               <?php

					 $i=0;
	 				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$category = itemCategoryName($row['category_id']);
					$price = $row['price'];
					$stock = $row['quantity'];

				?>
              <tr class="inner">
                <td > <?php echo $title; ?></td>
                <td > <?php echo $category; ?></td>
                <td > <?php echo $userSymbul.number_format($price, 2); ?></td>
                <td > <?php echo $stock; ?></td>
                <td valign="middle">
                <a href="admin/store?view=<?php echo $id;?>"><button>View</button></a>
                <a href="admin/store?addSale=<?php echo $id;?>"><button class="success">Add Sales</button></a>
                <a href="admin/store?addPurchase=<?php echo $id;?>"><button>Add Purchase</button></a>
                <a href="admin/store?edit=<?php echo $id;?>"><button class="warning">Edit</button></a>
                <a href="admin/store?delete=<?php echo $id;?>"><button class="danger">Delete</button></a>
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
