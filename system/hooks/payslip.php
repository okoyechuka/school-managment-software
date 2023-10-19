<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		payslip.php
Description:	This is the transaction page
Developer: 		Ynet Interactive
Date: 			9/4/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate($school_id);
$userSymbul = $Currency->Symbul($school_id);

if(isset($_REQUEST['delete'])) {

	if(isset($_REQUEST['ID'])) {
		$id = filterinp($_REQUEST['ID']);

		$sql=$query="DELETE FROM salary_pay WHERE month = '$id'";
		mysqli_query($server,$query) or die(mysqli_error($server));

		$message = "The selected Payslip was successfully deleted";
		$class="green";
	}
}

if(isset($_GET['payAll'])) {
	$month = $_GET['ID'];
	$date = date('Y-m-d');
	$uerID = getAdmin();

	//pay all
		$sql=$query = "select * FROM salary_pay WHERE month = '$month' AND status = 'Un-Paid' AND school_id = '$school_id'" or die(mysqli_error($server));

	$result = mysqli_query($server,$query);
	$num = mysqli_num_rows($result);
	$i=0;
	while($row = mysqli_fetch_assoc($result)){
		if(!slipPaid($ps_id)) {
		$ps_id = $row['id'];
		$sql=$query="UPDATE `salary_pay` SET `status` = 'Paid' WHERE `id` = '$ps_id'";
		mysqli_query($server,$query) or die(mysqli_error($server));
		$sql=$query="UPDATE `salary_pay` SET `date_pay` = '$date' WHERE `id` = '$ps_id'";
		mysqli_query($server,$query) or die(mysqli_error($server));
		$sql=$query="UPDATE `salary_pay` SET `approved_by` = '$userID' WHERE `id` = '$ps_id'";
		mysqli_query($server,$query) or die(mysqli_error($server));

		//create account records
		$payslipAmount = payslipAmount($ps_id);
		$payslipMonth = payslipMonth($ps_id).'-01';
		$salary_month = date('F Y', strtotime($payslipMonth));
		$description = $staff_name.' Salary for '.$salary_month;
		$direction = 'OUT';
		$date = date('Y-m-d');
		$status = 'Completed';
		$approvedBy = getAdmin();
		//create
			$sql=$query = "INSERT INTO transactions (`id`, `school_id`, `date`, `description`, `status`, `direction`, `amount`, `approvedBy`)
					VALUES (NULL, '$school_id', '$date', '$description','$status', '$direction','$payslipAmount', '$approvedBy');";
			mysqli_query($server,$query) or die(mysqli_error($server));
		}
		$i++;
	}

	$message = 'All Un-Paid salaries in this Pay-Slip group have been successfully marked as Paid. <br>Expense Records were also created for these transactions';
	$class = '';
}

if(isset($_POST['paySelected'])) {
	foreach ($_POST as $key => $value ){
		$$key = filterinp($value);
	}
	$date = date('Y-m-d');
	$uerID = getAdmin();
	//pay all
	foreach($_POST['ps_id'] as $ps_id) {
		if(!slipPaid($ps_id)) {
		$sql=$query="UPDATE `salary_pay` SET `status` = 'Paid' WHERE `id` = '$ps_id'";
		mysqli_query($server,$query) or die(mysqli_error($server));
		$sql=$query="UPDATE `salary_pay` SET `date_pay` = '$date' WHERE `id` = '$ps_id'";
		mysqli_query($server,$query) or die(mysqli_error($server));
		$sql=$query="UPDATE `salary_pay` SET `approved_by` = '$userID' WHERE `id` = '$ps_id'";
		mysqli_query($server,$query) or die(mysqli_error($server));

		//create account records
		$payslipAmount = payslipAmount($ps_id);
		$payslipMonth = payslipMonth($ps_id).'-01';
		$salary_month = date('F Y', strtotime($payslipMonth));
		$description = $staff_name.' Salary for '.$salary_month;
		$direction = 'OUT';
		$date = date('Y-m-d');
		$status = 'Completed';
		$approvedBy = getAdmin();
		//create
			$sql=$query = "INSERT INTO transactions (`id`, `school_id`, `date`, `description`, `status`, `direction`, `amount`, `approvedBy`)
			VALUES (NULL, '$school_id', '$date', '$description','$status', '$direction','$payslipAmount', '$approvedBy');";
			mysqli_query($server,$query) or die(mysqli_error($server));
		}
	}

	$message = 'All selected Pay-Slips have been successfully marked as Paid. <br>Expense Records were also created for these transactions';
	$class = 'green';
}

if(isset($_REQUEST['view'])) {
		$id = filterinp($_REQUEST['ID']);

		$sql=$query = "select * FROM salary_pay WHERE month = '$id'" or die(mysqli_error($server));
		$result = mysqli_query($server, $query);
		$num = mysqli_num_rows($result);
		if($num <1) {
			header('location: PaySlip.php');
		}


	//display form
?>
    <div id="add-new">
        <div id="add-new-head">View <?php echo $id ?> Pay-Slips
        <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">x</div></a>
        </div>
<form action="" method="post">
       <div class="inside">
           <a href="" onClick="javascript:printDiv('print-this1')">
        <button class="submit view" style="float: right; margin-left: 5px;"><i class="fa fa-print"></i> Print</button></a>
        	<?php if(userRole($userID) < 3) { ?>
                <a title="Pay All " onClick="showLoading();" href="admin/payslip?payAll&ID=<?php echo $id;?>"><button class="submit view" style="float: right; margin-left: 5px;"><i class="fa fa-check"></i> Pay All</button></a>
               <button name="paySelected" value="<?php echo $id;?>" class="submit view" type="submit" style="float: right; margin-left: 5px;"><i class="fa fa-check-o"></i> Pay Selected</button>
            <?php }?>
         <div id="print-this1">
		<!-- display slips ---------------------------------->
        <div style="overflow-x:auto; width: 100%;">
		<?php
			$i=0;
			while($row = mysqli_fetch_assoc($result)){
			$ps_id = $row['id'];
			$month2 = $row['month'];
			$month = payslipMonth($ps_id).'-01'; $month = date('F', strtotime($month));
			$year = payslipMonth($ps_id).'-01'; $year = date('Y', strtotime($month));
			$status = $row['status'];
			$salary = $row['amount'];
			$date = date('d/m/Y', strtotime($row['date_due']));
			$staff = $row['staff_id'];
			$salary1 = getStaffData('payroll',$staff);
			$staffName = getStaffData('first_name',$staff).' '.getStaffData('last_name',$staff);;
			$designation = getStaffData('designation',$staff);
			$monthLoan = 0;
			$allowance = getStaffData('allowance',$staff);;
			$paye = getStaffData('paye',$staff);;
			$deductions = getStaffData('deduction',$staff);;
		?>
        <div class="slip_box">
        <table class="salary_table" width="99.8%"><tr><td>
        <input class="selected" type="checkbox" name="ps_id[<?php echo $ps_id; ?>]" value="<?php echo $ps_id; ?>" />
        <!-- first line ------>
        	<div class="comp_name"><?php echo getSetting('name'); ?></div>
            <div class="slip_paid"><?php echo $status; ?></div>
        <!-- second line full---->
            <div class="slip_date"><?php echo $date; ?></div>
        <!-- third line -->
        	<div class="employee_left">
            	<strong>Employee: </strong><?php echo $staffName; ?><br />
                <strong>Designation: </strong><?php echo $designation; ?>
            </div>
            <div class="slip_right" >
            	<strong>Year: </strong><?php echo $year; ?><br />
                <strong>Month: </strong><?php echo $month; ?>
            </div>
        <!-- salary box --->
        	<div class="salary_box">
            	<table class="salary_table" width="99.8%">
                	<tr>
                    	<td width="35%" valign="top">
                        	<strong>Basic Salary: &nbsp;&nbsp;&nbsp;</strong><?php echo $userSymbul.number_format($salary1, 2); ?><br>
                            <strong>Allowances: &nbsp;&nbsp;&nbsp;</strong><?php echo $userSymbul.number_format($allowance, 2); ?>
                        </td>
                        <td width="35%" valign="top">
                            <strong>Deductions: &nbsp;&nbsp;&nbsp;</strong>-<?php echo $userSymbul.number_format($deductions, 2); ?><br>
                            <strong>PAYE: &nbsp;&nbsp;&nbsp;</strong>-<?php echo $userSymbul.number_format($paye, 2); ?>
                        </td>
                        <td valign="top">
                        	Received a Sum of:<br />
                            <p style="font-size:22px; margin-top:0; text-indent: 10px;"><strong><?php echo $userSymbul.number_format(($salary+$allowance-$deductions-$paye), 2); ?></strong></p><br />
                            <br />
                            <hr />
                            <p style="text-align:center;">Signature</p>
                        </td>
                    </tr>
                    <tr>
                    	<td><strong>Gross Salary: </strong><?php echo $userSymbul.number_format(($salary1+$allowance), 2); ?></td>
                        <td><strong>Net Salary: </strong><?php echo $userSymbul.number_format(($salary+$allowance-$deductions-$paye), 2); ?></td>
                        <td></td>
                    </tr>
                </table>
            </div>
		</td></tr></table>	
        </div>
        
        <?php
				$i++;
			} ?>
        </div>
        </div>
       </div>
  </form>
   </div>

<?php }

if(isset($_POST['doPaySlip'])) {
	$date_due = $_POST['year'].'-'.$_POST['month'].'-'.'29';
	$generated_by = getAdminName(getAdmin());
	$status = 'Un-Paid';

	$message = paySlip($date_due, $generated_by, $school_id, $status);
	$class = 'green';
}

if(isset($_REQUEST['payslip'])) {

		$month = date('m');
	//display form
?>

<div id="add-new">
	<div id="add-new-head">Generate Pay-Slip
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close"><i class="fa fa-close"></i></div></a></div>
     <div class="inside">
    <form method="post" action="admin/payslip" enctype="multipart/form-data">

             <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left" valign="middle"><strong>Choose Month</strong>:</td>
                <td  align="left" valign="middle">
                <select name="month" style="width: 98%;" >
                   <option <?php if($month == '01') echo 'selected'; ?> value="01">January</option>
                   <option <?php if($month == '02') echo 'selected'; ?> value="02">February</option>
                   <option <?php if($month == '03') echo 'selected'; ?> value="03">March</option>
                   <option <?php if($month == '04') echo 'selected'; ?> value="04">April</option>
                   <option <?php if($month == '05') echo 'selected'; ?> value="05">May</option>
                   <option <?php if($month == '06') echo 'selected'; ?> value="06">June</option>
                   <option <?php if($month == '07') echo 'selected'; ?> value="07">July</option>
                   <option <?php if($month == '08') echo 'selected'; ?> value="08">August</option>
                   <option <?php if($month == '09') echo 'selected'; ?> value="09">September</option>
                   <option <?php if($month == '10') echo 'selected'; ?> value="10">October</option>
                   <option <?php if($month == '11') echo 'selected'; ?> value="11">November</option>
                   <option <?php if($month == '12') echo 'selected'; ?> value="12">December</option>

                </select>
                </td>
              </tr>

              <tr>
                <td align="left" valign="middle"><strong>Select Year</strong>:</td>
                <td  align="left" valign="middle">
                    <input type="year" name="year" id="from_date" value="<?php echo date('Y') ?>" required="required" >
                </td>
              </tr>

           </table>

      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="doPaySlip" value="1"/>
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="payslip2" value="1" type="submit"><i class="fa fa-floppy-o"></i> Generate Pay-Slips</button>
	</form>
     	<div id="login-loading" style="width: 100%;"><i class="fa fa-spinner fa-spin"></i> Processing...</div>
        </td>
      </tr>
   </table>
   </div>
 </div>
 <?php
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
	         $clauses[] = "l.amount LIKE '%$term%' OR l.pay_date LIKE '%$term%' OR l.month LIKE '%$term%' OR l.id LIKE '%$term%' OR s.first_name LIKE '%$term%' OR s.last_name LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "l.amount LIKE '%%' ";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';

	//build and execute the required SQL
			$sql=$query = "select * FROM salary_pay l JOIN staffs s ON l.staff_id = s.id JOIN teachers t ON l.staff_id = t.id WHERE $filter GROUP BY l.month";

	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search term.";
		$class="blue";
		}
} else {

	$sql=$query = "select * FROM salary_pay GROUP BY month" or die(mysqli_error($server));
	$result = mysqli_query($server,$query);
	$num = mysqli_num_rows($result);

}


?>
  <?php if(!empty($message)) { showMessage($message,$class); } ?>

	<div id="search-pan">
    	<form action="" method="get">
        Search: <input type="search" name="keyword" placeholder="Search "/>
        <button type="submit" class="submit"><i class="fa fa-search"></i></button>
         <?php if(userRole($userID) < 3) { ?>
        <a href="admin/payslip?payslip" onClick="showLoading();">
        <button type="button"class="submit">Generate Payslip</button></a>
        <?php } ?>
        </form>
    </div>

	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table id="table" class="table tablesorter" width="100%" border="0" cellspacing="0" cellpadding="0">
            <thead>
              <tr>
                <th width="15%" scope="col">Pay-Slip For</th>
                <th width="20%" scope="col">Generated By</th>
                <th width="15%" scope="col">Total</th>
                <th width="12%" scope="col">No. of Slips</th>
                <th width="18%" scope="col">Status</th>
                <th width="20%" scope="col">Action</th>
              </tr>
            </thead>
              <tbody>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$by = $row['generated_by'];
					$total = sumSlip($row['month']);
					$month2 = $row['month'];
					$numb = countSlip($row['month']);
					$month = payslipMonth($id).'-01'; $month = date('F Y', strtotime($month));
					$status = $row['status'];

				?>
              <tr class="inner">
                <td > <?php echo $month; ?></td>
                <td > <?php echo $by; ?></td>
                <td > <?php echo $userSymbul.number_format($total, 2); ?></td>
                <td > <?php echo $numb.' Slip(s)'; ?></td>
                <td > <?php echo $status; ?></td>
                <td valign="middle">
                <a title="View Slips" onClick="showLoading();" href="admin/payslip?view&ID=<?php echo $month2;?>"><button class="edit"><i class="fa fa-eye"></i> View</button></a>
				<?php if(userRole($userID) < 3) { ?>
                <a title="Pay All Slips" onClick="showLoading();" href="admin/payslip?payAll&ID=<?php echo $month2;?>"><button class="play"><i class="fa fa-check"></i> Process</button></a>
                <a title="Delete All Slips" onClick="showLoading();" href="admin/payslip?delete&ID=<?php echo $month2;?>"><button class="btn-danger"><i class="fa fa-trash"></i> Delete</button></a>
                <?php } ?>
				</td>
              </tr>
              <?php
				$i++;	}
			  ?>
              </tbody>
              </table>
<!-- Pagination start -->
<?php displayPagination($setLimit,$page,$query) ?>

        </div>
    </div>

</div>
