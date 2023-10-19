<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		bookhistory.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			18/02/2015
*/
global $server;

if(userRole($userID) > 2 && userRole($userID) != 8) {
header('location: index.php');
}

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(isset($_GET['keyword']))
{

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
	    if (!empty($term))  {
	         $clauses[] = "title LIKE '%$term%' OR author LIKE '%$term%' OR serial LIKE '%$term%' OR date LIKE '%$term%' OR sub_title LIKE '%$term%'";
	    }  else  {
	         $clauses[] = "title LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM book_issues i WHERE $filter ";

 	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1"){
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}
} else {
	$sql=$query = "SELECT * FROM book_issues ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1"){
		$message = "There are currently no issued books!";
		$class="blue";
	}
}

if(isset($_GET['book'])) {
	$book = $_GET['book'];
	$sql=$query = "SELECT * FROM book_issues WHERE book_id = '$book' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1"){
		$message = "There are currently no issue records for the selected book!";
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
        <input type="search" name="keyword" placeholder="Search Issued Books" />
        <button class="submit"><i class="fa fa-search"></i></button>
        <?php if(userRole($userID)<3 || userRole($userID) == 8) { ?>
        <a href="admin/library"><button type="button" class="submit">Manage Books</button></a>
        <a href="admin/library?issue"><button type="button" class="submit">Issue Book</button></a>
        <a href="admin/library?return"><button type="button" class="submit">Return Book</button></a>
        <?php } ?>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="32%" scope="col">Book Title</th>
                <th width="20%" scope="col">Issued To</th>
                <th width="10%" scope="col">Issued On</th>
                <th width="10%" scope="col">Due Date</th>
                <th width="10%" scope="col">Issued By</th>
                <th width="10%" scope="col">Status</th>
                <th width="10%" scope="col">Action</th>
              </tr>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$date1 = $row['date_issued'];
					$date2 = $row['date_due'];
					$book = $row['book_id'];
					$student = $row['student_id'];
					$by = $row['user_id'];
					$status = $row['status'];
					$tag1='<green>'; $tag2='</green>';
					if($status == '0') {
						$status2 = 'Un-Returned';} else { $status = 'Returned';
						$tag1='<blue>'; $tag2='</blue>';
					}
					if((strtotime($date2) >= strtotime(date('d-m-y'))) && ($status == 'Un-Returned')) {
						$status2 = 'Overdue';
						$tag1='<red>'; $tag2='</red>';
					}
				?>
              <tr class="inner">
                <td width="32%"> <?php echo $tag1.bookName($book).$tag2; ?></td>
                <td width="20%"> <?php echo $tag1.studentName($student).$tag2; ?></td>
                <td width="10%"> <?php echo $tag1.date('d/m/Y', strtotime($date1)).$tag2; ?></td>
                <td width="10%"> <?php echo $tag1.date('d/m/Y', strtotime($date2)).$tag2; ?></td>
                <td width="10%"> <?php echo $tag1.adminData('name',$by).$tag2; ?></td>
                <td width="10%"> <?php echo $tag1.$status2.$tag2; ?></td>
                <td width="10%" valign="middle">
                <?php if($status == '0') {  ?>
                <a href="admin/library?return=<?php echo $book;?>&student=<?php echo $student;?>"><button class="success">Return</button></a>
                <?php } ?>
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
