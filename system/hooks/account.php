<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		transaction.php
Description:	This is the transaction page
Developer: 		Ynet Interactive
Date: 			5/3/2015
*/
global $server;
if(userRole($userID) > 3 && userRole($userID) != 7) {
header('location: index.php');
}

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());


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
        <img src="media/uploads/<?php echo getSetting('logo'); ?>" style="width: 40px;" /><br />
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
                <td> <?php echo $userSymbul.$amount; ?></td>
              </tr>
              <tr class="cont">
                <td> </td>
                <td><strong>Total:</strong></td>
                <td> <strong><?php echo $userSymbul.$amount; ?></strong></td>
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


if(isset($_REQUEST['edit']) || isset($_REQUEST['new']))
{
	if(isset($_REQUEST['edit'])) {
	   $book = filterinp($_REQUEST['edit']);
		$sql=$query="SELECT * FROM transactions WHERE id = '$book'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
    ?>
    <div id="add-new">
      <div id="add-new-head">Update Record
    <?php
	} else {
		$book = '';
		$row['date'] = date('Y-m-d');

    ?>
    <div id="add-new">
       <div id="add-new-head">Create New Record
    <?php
	}
  ?>
  <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #fff;">

    <form method="post" action="admin/account" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="left" colspan="2" valign="middle"><strong><blue></blue></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Transaction Type:</td>
        <td  align="left" valign="middle">
        	<select name="direction" style="width: 90%;">
            	<option value="IN">Income</optgroup>
            	<option value="OUT">Ependiture</option>
        	</select>
        </td>
       </tr>

       <tr>
        <td align="left" valign="middle">Amount:</td>
        <td  align="left" valign="middle">
        	<input type="text" name="amount" id="amount2" required="required" placeholder="Transaction Amount in your currency" value="<?php echo @$row['amount']; ?>">
        </td>
       </tr>

      <tr>
        <td align="left" valign="middle">Description:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="description" id="description" required="required" placeholder="" value="<?php echo @$row['description']; ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Date:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="date" id="date" required="required" placeholder="" value="<?php echo @$row['date']; ?>">
        </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <?php if(isset($_REQUEST['edit'])) { ?>
        <input type="hidden" name="save" value="yes" />
        <input type="hidden" name="account" value="<?php echo $book; ?>" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Record</button>
        <?php } else { ?>
        <input type="hidden" name="add" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Add Record</button>
        <?php } ?>
	  </form>
     	<div id="login-loading">
          <i class="fa fa-spinner fa-spin"></i>
          Saving Changes...
      </div>
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
	$approvedBy = getAdmin();
	$status = 'Completed';

	if(!is_numeric($amount)) {
		$message = 'Sorry but you must provide a Numeric value in the Amount field';
		$class = 'red';
	} else {
		//create new prents
			$sql=$query = "INSERT INTO transactions (`id`, `school_id`, `date`, `description`, `status`, `direction`, `amount`, `approvedBy`) VALUES (NULL, '$school_id', '$date', '$description','$status', '$direction','$amount', '$approvedBy');";

      mysqli_query($server, $query) or die(mysqli_error($server));

	$message = 'The new account record was succesfully created.';
	$class = 'green';
	}

}



if(isset($_REQUEST['delete'])){
	$trnxID = filterinp($_REQUEST['delete']);
	mysqli_query($server,"DELETE from transactions WHERE id = '$trnxID'");
}
if(isset($_GET['keyword'])){
$direction = $_GET['direction'];
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
	$sql=$query= "select * FROM transactions t JOIN invoices i ON t.invoice_id = i.id WHERE $filter AND t.status = 'Completed' AND t.direction LIKE '%$direction%' AND (t.school_id = '$school_id') ORDER BY t.id DESC";

	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")	{
		$message = "No match found! Please try another search terms.";
		$class="blue";
	}

	//sum totals
	$sql=$query="SELECT sum(t.amount) as total FROM transactions t JOIN invoices i ON t.invoice_id = i.id WHERE $filter AND t.status = 'Completed' AND (t.school_id = '$school_id') AND t.direction = 'IN'";
	$result2 = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result2);
	$toamount1 = $row['total'];

	$sql=$query="SELECT sum(t.amount) as total FROM transactions t JOIN invoices i ON t.invoice_id = i.id WHERE $filter AND t.status = 'Completed' AND (t.school_id = '$school_id') AND t.direction = 'OUT'";
	$result2 = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result2);
	$toamount2 = $row['total'];

} else {
	$sql=$query= "SELECT * FROM transactions WHERE school_id = '$school_id' AND status = 'Completed' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);

	//sum totals
	$sql=$query="SELECT sum(amount) as total FROM transactions WHERE school_id = '$school_id' AND status = 'Completed' AND direction = 'IN'";
	$result2 = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result2);
	$toamount1 = $row['total'];

	$sql=$query="SELECT sum(amount) as total FROM transactions WHERE school_id = '$school_id' AND status = 'Completed' AND direction = 'OUT'";
	$result2 = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result2);
	$toamount2 = $row['total'];
}

?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <select name="direction" style="width: 200px;">
        	<option value="">All Transactions</option>
            <option value="IN">Income</optgroup>
            <option value="OUT">Epense</option>
        </select>

        <input type="search" name="keyword" placeholder="Search transactions"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        &nbsp;&nbsp;<a href="admin/account?new"><button class="submit" type="button"><i class="fa fa-plus"></i> Add Record</button></a>
        </form>
    </div>

 	<div class="panel">
    	<div class="panel-head"><i class="fa fa-money"></i> <?php echo date('Y'); ?> Cash Flow</div>
	    <div class="panel-body" style="width: 100%;">
               <canvas id="canvas3" height="300px" width="700"></canvas>
        </div>
    </div>

	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
               <th width="10%" scope="col">Trnx Date</th>
                <th width="37%" scope="col">Description</th>
                <th width="15%" scope="col">Income</th>
                <th width="15%" scope="col">Expense</th>
                <th width="13%" scope="col">Processed By</th>
                <th width="10%" scope="col">Action</th>
              </tr>
               <?php
				if($num < 1) echo 'No records found';
        $i=0;
        while($row_num = mysqli_fetch_assoc($result)){
					$date = date('d M, Y', strtotime($row_num['date']));
					$id = $row_num['id'];
					$invoice_id = $row_num['invoice_id'];
					$desc = $row_num['description'];
					$amount = $row_num['amount'];
					$direction = $row_num['direction'];
					$status = $row_num['status'];
					$processedBy = adminData('name',$row_num['approvedBy']);
					if($row_num['approvedBy'] < 1) {
						$processBy = 'System';
					}
					if($direction=='OUT') { $amount1 = 0; $amount2 = $amount;}
					if($direction=='IN') { $amount2 = 0; $amount1 = $amount;}

					if($direction=='IN') {
            $img = '<img style=" vertical-align:middle;" src="media/images/in.png" /> <green>IN</green>';
          }
          else {
            $img = '<img style=" vertical-align:middle;" src="media/images/out.png" /> <red>OUT</red>';
          }
				  ?>

          <tr class="inner">
            <td > <?php echo $date; ?></td>
            <td > <?php echo $desc; ?></td>
            <td > <?php echo '<green>'.$userSymbul.number_format($amount1,2).'</green>'; ?></td>
            <td > <?php echo '<red>'.$userSymbul.number_format($amount2,2).'</red>'; ?></td>
            <td > <?php echo $processedBy; ?></td>
            <td valign="middle">
	          <?php
            if($invoice_id < 1) {
              ?><a onclick="return confirm('Are you sure you want to delete this entry from your record?')" href="admin/account?delete=<?php echo $id;?>"><button class="btn-danger">Delete</button></a><?php
            }
            else {
				      echo '-';
				    }
            ?></td>
          </tr>
          <?php

          $i++;
        } ?>
              <tr class="inner">
                <td style="background-color:#eee"> </td>
                <td style="background-color:#eee;text-align: right"><strong>Total &nbsp;</strong></td>
                <td style="background-color:#eee"> <strong><?php echo '<green>'.$userSymbul.number_format($toamount1,2).'</green>'; ?></strong></td>
                <td style="background-color:#eee"> <strong><?php echo '<red>'.$userSymbul.number_format($toamount2,2).'</red>'; ?></strong></td>
                <td style="background-color:#eee"> </td>
                <td style="background-color:#eee"></td>
              </tr>
         </table>
<!-- Pagination start -->

<?php
  //there was many sql above, ambiguous --ceejay@edit
  displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
<?php     ?>
</div>

<?php

$cYear = date('Y');
//10 yers income
$thisYearIncome = '"'.monthIncome($cYear.'-01',$school_id).'", '.'"'.monthIncome($cYear.'-02',$school_id).'", '.'"'.monthIncome($cYear.'-03',$school_id).'", '.'"'.monthIncome($cYear.'-04',$school_id).'", '.'"'.monthIncome($cYear.'-05',$school_id).'", '.'"'.monthIncome($cYear.'-06',$school_id).'", '.'"'.monthIncome($cYear.'-07',$school_id).'", '.'"'.monthIncome($cYear.'-08',$school_id).'", '.'"'.monthIncome($cYear.'-09',$school_id).'", '.'"'.monthIncome($cYear.'-10',$school_id).'", '.'"'.monthIncome($cYear.'-11',$school_id).'", '.'"'.monthIncome($cYear.'-12',$school_id).'"';

//10 yers expense
$thisYearExpense = '"'.monthExpense($cYear.'-01',$school_id).'", '.'"'.monthExpense($cYear.'-02',$school_id).'", '.'"'.monthExpense($cYear.'-03',$school_id).'", '.'"'.monthExpense($cYear.'-04',$school_id).'", '.'"'.monthExpense($cYear.'-05',$school_id).'", '.'"'.monthExpense($cYear.'-06',$school_id).'", '.'"'.monthExpense($cYear.'-07',$school_id).'", '.'"'.monthExpense($cYear.'-08',$school_id).'", '.'"'.monthExpense($cYear.'-09',$school_id).'", '.'"'.monthExpense($cYear.'-10',$school_id).'", '.'"'.monthExpense($cYear.'-11',$school_id).'", '.'"'.monthExpense($cYear.'-12',$school_id).'"';

?>
<script>

//current year income / expense
var barChartData3 = {
		labels : ["January","February","March","April","May","June","July","August","September","October","November","December"],
		datasets : [
			{
				label: "Expenditure",
				fillColor : "rgba(220,0,0,0.2)",
				strokeColor : "rgba(220,0,0,1)",
				pointColor : "rgba(220,0,0,1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(220,220,220,1)",
				data : [<?php echo $thisYearExpense; ?>]
			} ,
			{
				label: "Income",
				fillColor : "rgba(0,120,0,0.2)",
				strokeColor : "rgba(0,120,0,1)",
				pointColor : "rgba(0,320,0,1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(220,220,220,1)",
				data : [<?php echo $thisYearIncome; ?>]
			}
		]

	}

	window.onload = function(){
		var ctx = document.getElementById("canvas3").getContext("2d");
		window.myBar = new Chart(ctx).Line(barChartData3, {
			responsive : true
		});

	}
</script>
