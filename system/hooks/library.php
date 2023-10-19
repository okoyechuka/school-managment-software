<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		Guardian.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			18/02/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) > 2 && userRole($userID) != 8) {
header('location: index.php');
}

if(isset($_REQUEST['delete']))
{
	$book = filterinp($_REQUEST['delete']);
	$sql=$query = "DELETE FROM books WHERE id = '$book'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));

	$message = "The selected item was successfully deleted.";
	$class="green";
}


if(isset($_REQUEST['view']))
{
	$book = $_REQUEST['view'];
		//get students profile
		$sql=$query="SELECT * FROM books WHERE id = '$book'";
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
            <a href="admin/library?edit=<?php echo $book; ?>&done"><button class="submit">Edit</button></a>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle"><?php echo $row['title']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Sub-Title: </td>
        <td  align="left" valign="middle"><?php echo $row['sub_title']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Author:</td>
        <td  align="left" valign="middle"><?php echo $row['author']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Publisher:</td>
        <td  align="left" valign="middle"><?php echo $row['publisher']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Subject:</td>
        <td  align="left" valign="middle"><?php echo $row['subject']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Description:</td>
        <td  align="left" valign="middle"><?php echo $row['description']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">ISBN:</td>
        <td  align="left" valign="middle"><?php echo $row['isbn']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Location:</td>
        <td  align="left" valign="middle"><?php echo $row['location']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Serial:</td>
        <td  align="left" valign="middle"><?php echo $row['serial']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Copies in Catalog:</td>
        <td  align="left" valign="middle"><?php echo $row['catalog']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Availability:</td>
        <td  align="left" valign="middle"><?php echo $row['catalog']-bookIssued($row['id']).' out of '.$row['catalog']; ?> Copies </td>
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

if(isset($_GET['return']))
{
	if(!empty($_REQUEST['return'])) {
	$book = $_REQUEST['return'];
	$student = filterinp($_REQUEST['student']);
		$sql=$query="SELECT * FROM books WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Return <?php echo $row['$title']; ?>
<?php
	} else {
		$book = '';
		$student = '';
?>
<div id="add-new">
   <div id="add-new-head">Return Item
<?php
}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/library" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
       <tr>
        <td  align="left" valign="middle">Issued Item:</td>
        <td  align="left" valign="middle">
        <select name="book_id" style="min-width:200px;" >
			<?php
                $sql=$query="SELECT * FROM books WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'].' by '.$row['author'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($book == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php
									$i++;
								}   ?>
			</select>
        </td>
      </tr>
       <tr>
        <td  align="left" valign="middle">Issued To:</td>
        <td  align="left" valign="middle">
        <select name="student_id" style="min-width:200px;" >
			<?php
                $sql=$query="SELECT * FROM students WHERE school_id = '$school_id' ORDER BY first_name ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['first_name'].' '.$row['last_name']. ' '.$row['last_name'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($student == $g_id) {echo 'selected';} ?> ><?php echo $title; ?></option>
            <?php  $i++;
							}   ?>
			</select>
        </td>
      </tr>

      </tr>
      <tr>
        <td align="left" valign="middle">Return Date:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="date_returned" id="date1" required="required"  value="<?php echo date('Y-m-d');?>">
        </td>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="return" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Return Item</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving...</div>
        </td>
      </tr>
    </table>

	</div>
</div>
<?php }

if(isset($_GET['issue']))
{
	if(!empty($_REQUEST['issue'])) {
	$book = $_REQUEST['issue'];
		$sql=$query="SELECT * FROM books WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
?>
<div id="add-new">
   <div id="add-new-head">Issue <?php echo $row['$title']; ?>
<?php
	} else {
		$book = '';
?>
<div id="add-new">
   <div id="add-new-head">Issue Item
<?php
}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/library" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
       <tr>
        <td  align="left" valign="middle">Select Item:</td>
        <td  align="left" valign="middle">
        <select name="book_id" style="min-width:200px;"  >
			<?php
                $sql=$query="SELECT * FROM books WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'].' by '.$row['author'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($book == $g_id) {echo 'selected';} ?>><?php echo $title; ?></option>
            <?php
									$i++;
								}   ?>
			</select>
        </td>
      </tr>
       <tr>
        <td  align="left" valign="middle">Issue To:</td>
        <td  align="left" valign="middle">
        <select name="student_id" style="min-width:200px;" >
			<?php
                $sql=$query="SELECT * FROM students WHERE school_id = '$school_id' ORDER BY first_name ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['first_name'].' '.$row['last_name']. ' '.$row['last_name'];
            ?>
               <option value="<?php echo $g_id; ?>"><?php echo $title; ?></option>
            <?php
									$i++;
								}   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Issue Date:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="date_issued" id="date1" required="required"  value="<?php echo date('Y-m-d');?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Due Date:</td>
        <td  align="left" valign="middle">
        	<input type="date"  name="date_due" id="date2" required="required" placeholder="" value="<?php echo date('Y-m-d'); ?>">
        </td>
      </tr>
      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="issue" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Issue Item</button>
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
		$sql=$query="SELECT * FROM books WHERE id = '$book'";
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
   <div id="add-new-head">Add New Item
<?php
	}
?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/library" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
        </td>
      </tr>
       <tr>
        <td  align="left" valign="middle">Category:</td>
        <td  align="left" valign="middle">
        <select name="category_id" style="min-width:200px;" >
			<?php
                $sql=$query="SELECT * FROM book_categories WHERE school_id = '$school_id' ORDER BY title ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if($bc == $g_id) { echo 'selected'; }?>><?php echo $title; ?></option>
            <?php
			$i++;
			}   ?>
			</select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="title" id="title2" required="required" placeholder="" value="<?php echo @$row['title']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Sub Title:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="sub_title" id="sub_title" placeholder="Optional" value="<?php echo @$row['sub_title']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Author(s):</td>
        <td  align="left" valign="middle">
        	<input type="text" name="author" id="author" required="required" placeholder="" value="<?php echo @$row['author']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Publisher:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="publisher" id="publisher" placeholder="Optional" value="<?php echo @$row['publisher']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Subject(s):</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="subject" id="subject" placeholder="" value="<?php echo @$row['subject']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Description:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="description" id="description"  placeholder="" value="<?php echo @$row['description']; ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Physical Location:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="location" id="location"  placeholder="Eg. Book-Shelf Number" value="<?php echo @$row['location']; ?>">
        </td>
      <tr>
        <td align="left" valign="middle">ISBN:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="isbn" id="isbn" placeholder="Optional" value="<?php echo @$row['isbn']; ?>">
        </td>
      <tr>
        <td align="left" valign="middle">Serial Number:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="serial" id="serial"  placeholder="" value="<?php echo @$row['serial']; ?>">
        </td>
      <tr>
        <td align="left" valign="middle">Copies in Catalog:</td>
        <td  align="left" valign="middle">
        	<input type="number"  name="catalog" id="catalog" required="required" placeholder="" value="<?php echo @$row['catalog']; ?>">
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
		if(!bookExist($title, $sub_title, $author, $isbn, $school_id)) {

			$sql=$query = "INSERT INTO books (`id`, `school_id`, `serial`, `author`, `category_id`, `title`, `description`,
				`location`, `catalog`,`isbn`,`publisher`,`sub_title`,`subject` )
				VALUES (NULL, '$school_id', '$serial', '$author', '$category_id', '$title', '$description', '$location', '$catalog',	'$isbn', '$publisher', '$sub_title', '$subject');";

			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new item was succesfully added to your catalog.';
	$class = 'green';
	} else {
	$message = 'Sorry but this item already exist created. Please upload a defferent item';
	$class = 'yellow';
	}

}

if(isset($_POST['issue']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$user_id = $userID;
		//create new prents
		if(bookAvailable($book_id) > bookIssued($book_id)) {
			if(bookIssueExist($book_id, $student_id)) {
				$message = 'Item was previously issued to the selected student.';
				$class = 'yellow';
			} else {
			$sql=$query = "INSERT INTO book_issues (`id`, `school_id`, `date_issued`, `date_due`, `book_id`, `student_id`, `user_id`, `status`)
			 VALUES (NULL, '$school_id', '$date_issued', '$date_due', '$book_id', '$student_id', '$user_id', '0');";
			mysqli_query($server, $query) or die(mysqli_error($server));

			$message = 'Book issued successfully.';
			$class = 'green';
			}
	} else {
	$message = 'Sorry but there are no more copies of this item available. Ensure you recorded all returned copies of this book';
	$class = 'yellow';
	}

}

if(isset($_POST['return']))
{
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}

		//create new
		if(bookIssueExist($book_id, $student_id)) {
			$sql=$query="UPDATE `book_issues` SET `status` =  '1' WHERE `book_id` = '$book_id' AND  `student_id` = '$student_id' ;";
			mysqli_query($server, $query) or die(mysqli_error($server));
			$sql=$query="UPDATE `book_issues` SET `date_returned` =  '$date_returned' WHERE `book_id` = '$book_id' AND  `student_id` = '$student_id';";
			mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'Book returned successfully.';
	$class = 'green';
	} else {
	$message = 'Sorry but no record was found matching your selection';
	$class = 'yellow';
	}

}

if(isset($_POST['save']))
{
$book = $_POST['book'];
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		//update students fields
		if(($key != "book") && ($key != "save") && ($key != "photo") && ($key != "mother_photo") && ($key != "class") && ($key != "admit")) {
			$sql=$query="UPDATE `books` SET `$key` =  '$value' WHERE `id` = '$book';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
	}



	$message = 'The selected item was succesfully updated.';
	$class = 'green';
}

if(isset($_GET['keyword'])){
$category = filterinp($_GET['category_id']);
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
	         $clauses[] = "serial LIKE '%$term%' OR author LIKE '%$term%' OR subject LIKE '%$term%' OR title LIKE '%$term%' OR sub_title LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "title LIKE '%%' OR author LIKE '%%' OR publisher LIKE '%%' OR subject LIKE '%%' OR sub_title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM books  WHERE school_id = '$school_id'  AND $filter ";
	if($category > 0) {
		$sql=$query = "select * FROM books WHERE school_id = '$school_id' AND category_id = '$category' AND $filter ";
	}
 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1") 	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "SELECT * FROM books WHERE school_id = '$school_id' ORDER BY title ASC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")	{
		$message = "There are currently no items on your catalog!";
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
        <select name="category_id" style="margin-right: 0 10px;min-width:200px" >
			<?php
			     $sqlC=$queryC="SELECT * FROM book_categories WHERE school_id = '$school_id' ORDER BY title ASC";
                $resultC = mysqli_query($server, $queryC);
                $numC = mysqli_num_rows($resultC);
                $i=0;
				while($rowc = mysqli_fetch_assoc($resultC)){
                $c_id = $rowc['id'];
                $title = $rowc['title'];
            ?>
               <option <?php if(@$category == $c_id) { echo 'selected';} ?> value="<?php echo $c_id; ?>"><?php echo $title; ?></option>
            <?php
									$i++;
								}   ?>
            <option selected value="" disabled>Choose Category</option>
		</select>  &nbsp;
        <input type="search" name="keyword" placeholder="Search Library" />
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3 || userRole($userID) == 8) { ?>
        <a href="admin/librarycategory"><button type="button" class="submit">Categories</button></a>
        <a href="admin/library?new"><button type="button" class="submit">Add Items</button></a>
        <a href="admin/bookhistory"><button type="button" class="submit">History</button></a>
        <a href="admin/library?issue"><button type="button" class="submit">Issue Book</button></a>
        <a href="admin/library?return"><button type="button" class="submit">Return Book</button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="22%" scope="col">Title</th>
                <th width="15%" scope="col">Author</th>
                <th width="12%" scope="col">Serial</th>
                <th width="13%" scope="col">Location</th>
                <th width="10%" scope="col">Availability</th>
                <th width="23%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$title = $row['title'];
					$author = $row['author'];
					$serial = $row['serial'];
					$location = $row['location'];
					$catalog = $row['catalog'];
					$available = $catalog-bookIssued($id).'/'.$catalog.' Copies';
					if(empty($serial)) { $serial = 'Not available';}

				?>
              <tr class="inner">
                <td width=""> <?php echo $title; ?></td>
                <td width=""> <?php echo $author; ?></td>
                <td width=""> <?php echo $serial; ?></td>
                <td width=""> <?php echo $location; ?></td>
                <td width=""> <?php echo $available; ?></td>
                <td width="" valign="middle">
                <a href="admin/library?view=<?php echo $id;?>"><button class="success">View</button></a>
                <a href="admin/library?edit=<?php echo $id;?>"><button>Edit</button></a>
                <a href="admin/library?delete=<?php echo $id;?>"><button class="danger">Remove</button></a>
                <a href="admin/bookhistory?book=<?php echo $id;?>"><button>History</button></a>
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
