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

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate($school_id);
$userSymbul = $Currency->Symbul($school_id);


if(isset($_POST['doPaySlip'])) {
	$date_due = mysqli_real_escape_string($server,$_POST['year']).'-'.mysqli_real_escape_string($server,$_POST['month']).'-'.'29';
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
    <form method="post" action="admin/payroll" enctype="multipart/form-data">

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
	         $clauses[] = "u.name LIKE '%$term%' OR u.username LIKE '%$term%' OR s.designation LIKE '%$term%' OR r.title LIKE '%$term%' OR t.designation LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "u.name LIKE '%%' ";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';

	//build and execute the required SQL
			$sql=$query = "select * FROM users u INNER JOIN staffs s ON s.id = u.profile_id INNER JOIN teachers t on t.id = u.profile_id INNER JOIN user_roles r ON u.role_id = r.id WHERE $filter AND u.school_id = '$school_id' AND (role_id =2 OR role_id =3 OR role_id =4 OR role_id >6)";

	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search term.";
		$class="blue";
		}
} else {

	$sql=$query = "select * FROM users WHERE school_id = '$school_id' AND (role_id =2 OR role_id =3 OR role_id =4 OR role_id >6) ORDER BY id DESC";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);

}


?>
  <?php if(!empty($message)) { showMessage($message,$class); } ?>

	<div id="search-pan">
    	<form action="" method="get">
        Search: <input type="search" name="keyword" placeholder="Search "/>
        <button type="submit" class="submit"><i class="fa fa-search"></i></button>
         <?php if(userRole($userID) < 3) { ?>
        <a href="admin/payroll?payslip" onClick="showLoading();">
        <button type="button"class="submit">Generate Payslip</button></a>
        <?php } ?>
        </form>
    </div>

	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
            <thead>
              <tr>
                <th width="30%" scope="col">Employee</th>
                <th width="12%" scope="col">Qualification</th>
                <th width="15%" scope="col">Role</th>
                <th width="15%" scope="col">Designation</th>
                <th width="15%" scope="col">Basic Salary</th>
                <th width="8%" scope="col">Action</th>
              </tr>
            </thead>
              <tbody>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$profile = $row['profile_id'];
					$salary = getStaffData('payroll',$id);
					$staff = $row['name'];
					$qualification = getStaffData('qualification',$id);
					$designation = getStaffData('designation',$id);
					$role = roleName($row['role_id']);
					if($row['role_id']==4) {
						$action = '<a href="admin/teacher?edit='.$profile.'"><button>Update</button></a>';
					} else {
						$action = '<a href="admin/staff?edit='.$profile.'"><button>Update</button></a>';
					}

	//check if user role can use report

				?>
              <tr class="inner">
                <td > <?php echo $staff; ?></td>
                <td > <?php echo $qualification; ?></td>
                <td > <?php echo $role; ?></td>
                <td > <?php echo $designation; ?></td>
                <td > <?php echo $userSymbul.number_format($salary, 2); ?></td>
                <td valign="middle">
                <?php if(userRole($userID) < 3) { echo $action; } ?>
				</td>
              </tr>
              <?php
					$i++;
		}
			  ?>
              </tbody>
              </table>
<!-- Pagination start -->
<?php displayPagination($setLimit,$page,$query) ?>
        </div>
    </div>

</div>