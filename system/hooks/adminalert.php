<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		adminalert.php
Description:	This is the draft page
Developer: 		Ynet Interactive
Date: 			5/3/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(isset($_REQUEST['approved']))
{
	$trnxID = filterinp($_REQUEST['approved']);
	$gateway = $_REQUEST['method'];

	//change status
	$sql=$query="UPDATE `paymentalerts` SET
	`status` =  'Approved'
	WHERE  `transaction` = '$trnxID';";

	mysqli_query($server, $query);

	$adminUser = $userID;
	payInvoice($trnxID,$gateway,$adminUser);

	$message = 'Selected payment has been successfully processed.';
	$class = 'green';
}

if(isset($_REQUEST['approve'])){
	$trnxID = filterinp($_REQUEST['trnxID']);
	$sql=$query="SELECT * FROM paymentalerts WHERE id = '$trnxID'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	$gateway = $row['gateway'];

	$message = '&nbsp;Are you sure you want to confirm payment for invoice #'.$trnxID.'? &nbsp;&nbsp;&nbsp;<a href="admin/adminalert?approved='.$trnxID.'&method='.$gateway.'">Yes I\'m Sure</a> &nbsp;&nbsp;<a href="admin/adminalert">No I dont</a>';
	$class = 'blue';
}

if(isset($_GET['keyword']))
{
$searchword = filterinp($_GET['keyword']);
	$forword = filterinp($_GET['keyword']);
	$term = mysqli_real_escape_string($_GET['keyword']);
	$ser = explode(' ', $_GET['keyword']);
	$clauses = array();
	$clauses2 = array();
	$clauses3 = array();
	foreach($ser as $look)
	{
	    $term = trim(preg_replace('/[^a-z0-9]/i', '', $look));
	    if (!empty($term))
	    {
	         $clauses[] = "t.date LIKE '%$term%' OR t.depositor LIKE '%$term%' OR t.reference LIKE '%$term%' OR t.invoice_id LIKE '%$term%' OR s.first_name LIKE '%$term%' OR s.last_name LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "t.date LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query= "select * FROM paymentalerts t JOIN invoices i ON t.invoice_id = i.id JOIN students s ON i.student_id = s.id WHERE $filter AND t.school_id = '$school_id'";

	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	 $sql=$query= "SELECT * FROM paymentalerts WHERE school_id = '$school_id' ORDER BY id DESC LIMIT 1000";
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
        Search Payment Alerts: <input type="search" name="keyword" placeholder="Search alerts"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="15%" scope="col">Payment Date</th>
                <th width="10%" scope="col">Invoice No.</th>
                <th width="10%" scope="col">Invoice Amount</th>
                <th width="15%" scope="col">Depositor</th>
                <th width="15%" scope="col">Method</th>
                <th width="10%" scope="col">Payment Ref</th>
                <th width="10%" scope="col">Status</th>
                <th width="12%" scope="col">Action</th>
              </tr>
            </table>
               <?php
				$i = 0;
				while($row = mysqli_fetch_assoc($result)){
					$date = date('d/m/Y H:m', strtotime($row['date']));
					$invoice_id = $row['invoice_id'];
					$amount = invoiceAmount($row['invoice_id']);
					$depositor = $row['depositor'];
					$method = shorten(gatewayName($row['gateway']),20);
					$reference = $row['reference'];
					$status = $row['status'];
				?>
				<div class="virtualpage hidepeice">
          <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner">
                <td width="15%"> <?php echo $date; ?></td>
                <td width="10%"> <?php echo sprintf('%07d',$invoice_id); ?></td>
                <td width="12%"> <?php echo $userSymbul.number_format($amount); ?></td>
                <td width="15%"> <?php echo $depositor; ?></td>
                <td width="15%"> <?php echo $method; ?></td>
                <td width="10%"> <?php echo $reference; ?></td>
                <td width="10%"> <?php echo $status; ?></td>
                <td width="12%" valign="middle">
                <?php
								if($status == 'Pending') {
										?>
                		<a href="admin/adminalert?approve&trnxID=<?php echo $order;?>"><button class="btn-success">Confirm</button></a>
                		<?php
								}
								else { echo $status; }
								?>
                </td>
              </tr>
          </table>
        </div>
        <?php
				$i++;
			}
			?>
<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>
<?php     ?>
</div>