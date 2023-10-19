<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		usertransaction.php
Description:	This is the transaction page
Developer: 		Ynet Interactive
Date: 			10/3/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

$parent = userProfile($userID);

 if(isset($_REQUEST['invoice']) && !empty($_REQUEST['invoice'])) {
	$data = filterinp($_REQUEST['invoice']);
		//get students profile
		$sql=$query="SELECT * FROM invoices WHERE id = '$data'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$amount = $row['amount'];
		$school_id = $row['school_id'];
		$fee_id = $row['fee_id'];
		$student_id = $row['student_id'];
		$date = $row['date'];
		$session_id = $row['session_id'];
		$term_id = $row['term_id'];
		$status = $row['status'];

		if($status=='Un-Paid') { $tag1='<red>'; $tag2='</red>';}
		if($status=='Paid') { $tag1='<green>'; $tag2='<green>';}
	?>
        <div id="add-new">
            <div id="add-new-head">Invoice Number <?php echo sprintf('%07d',$data); ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
             <div class="inside" style="background-color: #CCC;">
             	<div id="reciept">

	<div class="panel" style="width: 99%;">
        <br /><div style="text-align: center">
        <img src="media/uploads/<?php echo getSetting('logo'); ?>" style="width: 150px;" /><br />
		<?php echo '<strong class="capitalize">'.getSetting('name').'</strong></left><br>';
		echo '<small>'.getSetting('address').', ';
		echo ''.getSetting('city').', '.getSetting('state').'</small><br>';
		echo '<small>Email: '.getSetting('email').'</small> &nbsp;';
		echo '<small>Tel: '.getSetting('phone1').' '.getSetting('phone2').'</small><br>';   ?>
        <br />INVOICE
        </div>
    	<div id="invoice-from" >
        <?php
		echo '<left><strong><blue>Invoice Date:</blue> </strong>'.date('d F, Y', strtotime($date)).'</left>';
		echo '<br><left><strong><blue>Status:</blue> '.$tag1.$status.$tag2.'</strong></left>';
		echo '<br><left><blue>Invoice No.</blue> '. sprintf("%07d",$data).'</left><br>';
		?>
        </div>
    	<div id="invoice-to">
        <?php
		$sql=$query="SELECT * FROM students WHERE id = '$student_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$address = $row['address'];
		$city = $row['city'];
		$state = $row['state'];

		echo '<left><strong><blue>INVOICE TO: </blue></strong></left><br>';
		echo '<left><strong class="capitalize">'.studentName($student_id).'</strong></left><br>';
		if(!empty($address)) echo '<left>'.$address.'</left><br>';
		if(!empty($city)) echo '<left>'.$city.', '.$state.'</left><br>';
		echo '<left>'.className(getClass($student_id,$session_id)).'</left><br>';
		?>
        </div>
        <div id="invoice-body">
         <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="60%" scope="col">Description</th>
                <th width="10%" scope="col">Quantity</th>
                <th width="25%" scope="col">Amount</th>
              </tr>
              <tr class="cont">
                <td> <?php echo sessionName($session_id).' '.feeName($fee_id); ?></td>
                <td> <?php echo 1; ?></td>
                <td> <?php echo $userSymbul.number_format($amount,2); ?></td>
              </tr>
              <tr class="cont">
                <td> </td>
                <td><strong>Total:</strong></td>
                <td> <strong><?php echo $userSymbul.number_format($amount,2); ?></strong></td>
              </tr>
            </table>
           </div>
        </div>
        <div id="invoice-instruction" style="margin-bottom: 20px;">
			<a href="" onClick="javascript:printDiv('reciept')"><button class="submit">Print Invoice</button></a><br /><br />
        </div>
    </div>

       </div>
     </div>
     </div>
 <?php }


if(isset($_GET['keyword']))
{
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
	         $clauses[] = "t.date LIKE '%$term%' OR t.description LIKE '%$term%' OR t.invoice_id LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "t.date LIKE '%%' OR t.description LIKE '%%' OR t.invoice_id LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select t.* FROM transactions t JOIN invoices i ON t.invoice_id = i.id WHERE $filter AND (t.school_id = '$school_id') AND (i.parent_id = '$parent' OR i.student_id = '$parent') ORDER BY t.id DESC";

	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT t.* FROM transactions t JOIN invoices i ON t.invoice_id = i.id WHERE t.school_id = '$school_id' AND (i.parent_id = '$parent' OR i.student_id = '$parent') ORDER BY t.id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
}
?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <input type="search" name="keyword" placeholder="Search Payments"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
               <th width="15%" scope="col">Payment Date</th>
                <th width="35%" scope="col">Description</th>
                <th width="15%" scope="col">Amount</th>
                <th width="10%" scope="col">Status</th>
                <th width="10%" scope="col">Action</th>
              </tr>
               <?php
				if($num < 1) echo 'No records found';
				$i=0;
        while($row=mysqli_fetch_assoc($result)){
					$date = date('d M, Y', strtotime($row['date']));
					$invoice_id = $row['invoice_id'];
					$desc = $row['description'];
					$amount = $row['amount'];
					$direction = $row['direction'];
					$status = $row['status'];
					$processedBy = adminData('name',$row['approvedBy']);
					if($row['approvedBy'] < 1) {
						$processBy = 'System';
					}
					if($status=='Pending') { $tag1='<blue>'; $tag2='</blue>';}
					if($status=='Canceled') { $tag1='<red>'; $tag2='</red>';}
					if($status=='Completed') { $tag1='<green>'; $tag2='<green>';}
					if($status=='Failed') { $tag1='<red>'; $tag2='</red>';}

					if($direction=='IN') { $img = '<img style=" vertical-align:middle;" src="media/images/in.png" /> <green>IN</green>';} else {$img = '<img style=" vertical-align:middle;" src="media/images/out.png" /> <red>OUT</red>';}
				?>
 
              <tr class="inner">
                <td width="15%"> <?php echo $date; ?></td>
                <td width="35%"> <?php echo $desc; ?></td>
                <td width="15%"> <?php echo $userSymbul.number_format($amount,2); ?></td>
                <td width="10%"> <?php echo $tag1.$status.$tag2; ?></td>
                <td width="10%" valign="middle">
				<?php	if($invoice_id>0) { ?>
                <a href="usertransaction?invoice=<?php echo $invoice_id;?>"><button>Invoicee</button></a>
                <?php }	else {
				echo '-';
				} ?></td>
              </tr>
              <?php $i++; } ?>
              </table>
<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
<?php     ?>
</div>
