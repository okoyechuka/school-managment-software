<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;
/*
File name: 		admintransaction.php
Description:	This is the draft page
Developer: 		Ynet Interactive
Date: 			10/11/2014
*/
if(userRole($userID) > 3 && userRole($userID) != 7) {
header('location: index.php');
}

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(isset($_REQUEST['approved']))
{
	$trnxID = filterinp($_REQUEST['trnxID']);
	$adminUser = getAdminUsername(getAdmin());
	processTransaction($trnxID,$adminUser);

	$message = 'Selected transactions has been successfully approved. The associated customer\'s account has been credited with the purchased units.';
	$class = 'green';
}

if(isset($_REQUEST['approve']))
{
	$trnxID = filterinp($_REQUEST['trnxID']);
	$message = '&nbsp;Are you sure you want to approve transaction #'.$trnxID.'? &nbsp;&nbsp;&nbsp;<a href="admin/admintransaction?approved&trnxID='.$trnxID.'">Yes I\'m Sure</a> &nbsp;&nbsp;<a href="admin/admintransaction">No I dont</a>';
	$class = 'blue';
}

if(isset($_GET['keyword']))
{
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
	         $clauses[] = "t.date LIKE '%$term%' OR t.units LIKE '%$term%' OR t.cost LIKE '%$term%' OR c.username LIKE '%$term%' OR c.name LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "t.date LIKE '%%' OR t.units LIKE '%%' OR t.cost LIKE '%%' OR c.username LIKE '%%' OR c.name LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query= "select * FROM transactions t LEFT JOIN customers c ON t.customer = c.id WHERE $filter ";

	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query= "SELECT * FROM transactions ORDER BY id DESC LIMIT 1000";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "No records found!";
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
        Search Transactions: <input type="search" name="keyword" placeholder="Search transactions"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="13%" scope="col">Date</th>
                <th width="10%" scope="col">Trnx ID</th>
                <th width="15%" scope="col">Payment Method</th>
                <th width="10%" scope="col">Amount</th>
                <th width="10%" scope="col">Status</th>
                <th width="20%" scope="col">Action</th>
              </tr>
               <?php
				$i=0;
				while($row = mysqli_fetch_assoc($result)){

					$date = date('d/m/Y H:m', strtotime($row['date']));
					$id = $row['id'];
					$method = shorten(gatewayName($row['gateway']),20);
					$gateway = $row['gateway'];
					$cost = $row['amount'];
					$status = $row['status'];
					$approvedby = $row['approvedBy'];
					if($status=='Pending') { $tag1='<blue>'; $tag2='</blue>';}
					if($status==2) { $tag1='<blue>'; $tag2='</blue>';}
					if($status=='Completed') { $tag1='<green>'; $tag2='<green>';}
					if($status=='Failed') { $tag1='<red>'; $tag2='</red>';}
					?>
             <tr class="inner">
                <td width=""> <?php echo $date; ?></td>
                <td width=""> <?php echo sprintf('%07d',$id); ?></td>
                <td width=""> <?php echo $method; ?></td>
                <td width=""> <?php echo $userSymbul.number_format($cost,2); ?></td>
                <td width=""> <?php echo $tag1.$status.$tag2; ?></td>
                <td width="" valign="middle">
                <?php		if($status!='Completed') {		?>
                	<a href="admin/admintransaction?approve&trnxID=<?php echo $id;?>"><button class="btn-success">Approve</button></a>
                	<?php
				} else {
					echo 'Approved by '.$approvedby;
					}
								?>
                </td>
              </tr>
          	<?php
						$i++;
					}
					?>
              </table>

<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
<?php     ?>
</div>
