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

 if(isset($_REQUEST['viewinvoice']) && !empty($_REQUEST['viewinvoice'])) {
	$data = $_REQUEST['viewinvoice'];
?>
        <div id="dialog">
            <div id="dialog-head">Invoice Size
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('dialog').style.display='none';"><div class="close">&times;</div></a>
            </div>
            <div class="inside" style="background-color: #eee;">
                <?php showMessage('Choose "<strong>Standard Receipt Size</strong>" to generate an invoice compatible with regular printers<br>Choose "<strong>POS Receipt Size</strong>" to generate an invoice compatible with 80mm Termal Printers','blue'); ?>
                <div style="margin: 0px auto; padding-bottom: 50px; width: 250px;">
                	<a href="transaction?invoice=<?=$data?>"><button class="submit" style="width: 90%"><i class="fa fa-print fa-x3"></i><br>Standard <br>Receipt Size</button></a>
                    <a href="transaction?invoicep=<?=$data?>"><button class="submit" style="width: 90%"><i class="fa fa-print fa-x3"></i><br>82mm POS <br>Receipt Size</button></a>
                    <a href="transaction?invoicep1=<?=$data?>"><button class="submit" style="width: 90%"><i class="fa fa-print fa-x3"></i><br>57mm POS <br>Receipt Size</button></a><p><br /><br /></p>
                </div>
       		</div>
     </div>
 <?php }

if(isset($_REQUEST['invoicep1']) && !empty($_REQUEST['invoicep1'])) {
	$data = $_REQUEST['invoicep1'];
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
        <div id="dialog" style="min-height: 600px;">
            <div id="dialog-head">Invoice Number <?php echo sprintf('%07d',$data); ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('dialog').style.display='none';"><div class="close">&times;</div></a></div>
             <div class="inside" style="background-color: #CCC; min-height: 600px;">
             	<div id="reciept">

	<div class="panel" style="width: 96%; padding: 2%; width: 215.43px; margin: 10px auto;">
        <br /><div style="text-align: center">
        <img src="media/uploads/<?php echo getSetting('logo'); ?>" style="width: 40%; max-width: 60px;" /><br />
		<?php echo '<strong class="capitalize">'.getSetting('name').'</strong><br>';
		echo '<small>'.getSetting('address').', ';
		echo ''.getSetting('city').', '.getSetting('state').'</small><br>';
		echo '<small>Email: '.getSetting('email').'</small> <br>';
		echo '<small>Tel: '.getSetting('phone1').' '.getSetting('phone2').'</small><br>';   ?>
        <br />INVOICE #<?=sprintf("%07d",$data)?><hr>
        </div>

    	<div id="invoice-to" style="width: 99.99%; margin-left: -5px;" >
        <?php
		$sql=$query="SELECT * FROM students WHERE id = '$student_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$address = $row['address'];
		$city = $row['city'];
		$state = $row['state'];
		echo '<left><strong><blk>Date:</blk> </strong>'.date('d-m-Y', strtotime($date)).'</left>';
		echo '<br><left><strong><blk>Status:</blk> '.$tag1.$status.$tag2.'</strong></left><br><br>';

		echo '<left><strong><blue>INVOICE TO: </blue></strong></left><br>';
		echo '<left><strong class="capitalize">'.studentName($student_id).'</strong></left><br>';
		if(!empty($address)) echo '<left>'.$address.'</left><br>';
		if(!empty($city)) echo '<left>'.$city.', '.$state.'</left><br>';
		if(getClass($student_id,$session_id)>0) echo '<left>'.className(getClass($student_id,$session_id)).'</left><br>';
		?>
        </div>
        <div id="invoice-body">
         <div class="panel-body">
        	<table style="width: 210px; max-width: 210px; min-width: 210px;" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th style="background: #222;" width="75%" scope="col">Description</th>
                <th style="background: #222;" width="25%" scope="col">Amount</th>
              </tr>
              <tr class="cont">
               <td> <?php if($session_id>0) echo sessionName($session_id).' ';
				echo feeName($fee_id); ?></td>
                <td> <?php echo $userSymbul.number_format($amount,2); ?></td>
              </tr>
              <tr class="cont">
                <td style="text-align: right;"><strong>Total:</strong></td>
                <td> <strong><?php echo $userSymbul.number_format($amount,2); ?></strong></td>
              </tr>
            </table>
            <br>
            <p style="text-align: center">
				<?php
				$colorFront = new BCGColor(0, 0, 0);
				$colorBack = new BCGColor(255, 255, 255);
				$font = new BCGFont(DOCUMENT_ROOT.'drivers/includes/barcodegen/class/font/Arial.ttf', 18);
				$code = new BCGcode128();
				$code->setScale(3);
				$code->setThickness(30);
				$code->setForegroundColor($colorFront);
				$code->setBackgroundColor($colorBack);
				$code->setFont($font);
				$code->parse(sprintf("%07d",$data));
				$drawing = new BCGDrawing(sprintf("%07d",$data).'.png', $colorBack);
				$drawing->setBarcode($code);
				$drawing->draw();
				$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
				?>
                <img src="<?php echo sprintf("%07d",$data).'.png'; ?>" style="width: 70%; max-width: 180px;" />
                <?php unlink(sprintf("%07d",$data).'.png'); ?>
                </p>
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

if(isset($_REQUEST['invoicep']) && !empty($_REQUEST['invoicep'])) {
	$data = $_REQUEST['invoicep'];
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
        <div id="dialog" style="min-height: 600px;">
            <div id="dialog-head">Invoice Number <?php echo sprintf('%07d',$data); ?>
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('dialog').style.display='none';"><div class="close">&times;</div></a></div>
             <div class="inside" style="background-color: #CCC; min-height: 600px;">
             	<div id="reciept">

	<div class="panel" style="width: 96%; padding: 2%; max-width: 303.36px">
        <br /><div style="text-align: center">
        <img src="media/uploads/<?php echo getSetting('logo'); ?>" style="width: 40%; max-width: 70px;" /><br />
		<?php echo '<strong class="capitalize">'.getSetting('name').'</strong><br>';
		echo '<small>'.getSetting('address').', ';
		echo ''.getSetting('city').', '.getSetting('state').'</small><br>';
		echo '<small>Email: '.getSetting('email').'</small> <br>';
		echo '<small>Tel: '.getSetting('phone1').' '.getSetting('phone2').'</small><br>';   ?>
        <br />INVOICE #<?=sprintf("%07d",$data)?><hr>
        </div>

    	<div id="invoice-to" style="width: 99.99%" >
        <?php
		$sql=$query="SELECT * FROM students WHERE id = '$student_id'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$address = $row['address'];
		$city = $row['city'];
		$state = $row['state'];
		echo '<left><strong><blk>Date:</blk> </strong>'.date('d-m-Y', strtotime($date)).'</left>';
		echo '<br><left><strong><blk>Status:</blk> '.$tag1.$status.$tag2.'</strong></left><br><br>';

		echo '<left><strong><blue>INVOICE TO: </blue></strong></left><br>';
		echo '<left><strong class="capitalize">'.studentName($student_id).'</strong></left><br>';
		if(!empty($address)) echo '<left>'.$address.'</left><br>';
		if(!empty($city)) echo '<left>'.$city.', '.$state.'</left><br>';
		if(getClass($student_id,$session_id)>0) echo '<left>'.className(getClass($student_id,$session_id)).'</left><br>';
		?>
        </div>
        <div id="invoice-body">
         <div class="panel-body">
        	<table style="width: 300px; max-width: 300px; min-width: 300px;" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th style="background: #222;" width="75%" scope="col">Description</th>
                <th style="background: #222;" width="25%" scope="col">Amount</th>
              </tr>
              <tr class="cont">
               <td> <?php if($session_id>0) echo sessionName($session_id).' ';
				echo feeName($fee_id); ?></td>
                <td> <?php echo $userSymbul.number_format($amount,2); ?></td>
              </tr>
              <tr class="cont">
                <td style="text-align: right;"><strong>Total:</strong></td>
                <td> <strong><?php echo $userSymbul.number_format($amount,2); ?></strong></td>
              </tr>
            </table>
            <br>
            <p style="text-align: center">
				<?php
				$colorFront = new BCGColor(0, 0, 0);
				$colorBack = new BCGColor(255, 255, 255);
				$font = new BCGFont(DOCUMENT_ROOT.'drivers/includes/barcodegen/class/font/Arial.ttf', 18);
				$code = new BCGcode128();
				$code->setScale(3);
				$code->setThickness(30);
				$code->setForegroundColor($colorFront);
				$code->setBackgroundColor($colorBack);
				$code->setFont($font);
				$code->parse(sprintf("%07d",$data));
				$drawing = new BCGDrawing(sprintf("%07d",$data).'.png', $colorBack);
				$drawing->setBarcode($code);
				$drawing->draw();
				$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
				?>
                <img src="<?php echo sprintf("%07d",$data).'.png'; ?>" style="width: 70%; max-width: 200px;" />
                </p>
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
		echo '<left><strong><blue>Date:</blue> </strong>'.date('d F, Y', strtotime($date)).'</left>';
		echo '<br><left><strong><blue>Status:</blue> '.$tag1.$status.$tag2.'</strong></left>';
		echo '<br><left><blue>Inv. No.</blue> '. sprintf("%07d",$data).'</left><br>';
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
		if(getClass($student_id,$session_id)>0) echo '<left>'.className(getClass($student_id,$session_id)).'</left><br>';
		?>
        </div>
        <div id="invoice-body">
         <div class="panel-body">
        	<table width="100%" style="" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="75%" scope="col">Description</th>
                <th width="25%" scope="col">Amount</th>
              </tr>
              <tr class="cont">
                <td> <?php if($session_id>0) echo sessionName($session_id).' ';
				echo feeName($fee_id); ?></td>
                <td> <?php echo $userSymbul.number_format($amount,2); ?></td>
              </tr>
              <tr class="cont">
                <td style="text-align: right;"><strong>Total:</strong></td>
                <td> <strong><?php echo $userSymbul.number_format($amount,2); ?></strong></td>
              </tr>
            </table>
             <p style="text-align: center">
				<?php
				$colorFront = new BCGColor(0, 0, 0);
				$colorBack = new BCGColor(255, 255, 255);
				$font = new BCGFont(DOCUMENT_ROOT.'drivers/includes/barcodegen/class/font/Arial.ttf', 18);
				$code = new BCGcode128();
				$code->setScale(3);
				$code->setThickness(30);
				$code->setForegroundColor($colorFront);
				$code->setBackgroundColor($colorBack);
				$code->setFont($font);
				$code->parse(sprintf("%07d",$data));
				$drawing = new BCGDrawing(sprintf("%07d",$data).'.png', $colorBack);
				$drawing->setBarcode($code);
				$drawing->draw();
				$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
				?>
                <img src="<?php echo sprintf("%07d",$data).'.png'; ?>" style="width: 70%; max-width: 200px;" />
                </p>
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
$direction = $_GET['direction'];
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
	$sql=$query = "select * FROM transactions t JOIN invoices i ON t.invoice_id = i.id WHERE $filter AND t.direction  ='IN' AND t.status LIKE '%$direction%' AND (t.school_id = '$school_id') ORDER BY t.id DESC";

	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT * FROM transactions WHERE school_id = '$school_id' AND direction = 'IN' ORDER BY id DESC LIMIT $pageLimit,$setLimit";
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
        <select name="direction" style="">
        	<option value="" disabled selected>Select Status:</option>
            <option value="">All Transactions</option>
            <option value="Pending">Pending</optgroup>
            <option value="Completed">Successfull</option>
            <option value="Canceled">Canceled</option>
            <option value="Failed">Failed</option>
        </select>

        <input type="search" name="keyword" placeholder="Search transactions"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
               <th width="10%" scope="col">Trnx Date</th>
                <th width="40%" scope="col">Description</th>
                <th width="15%" scope="col">Amount</th>
                <th width="10%" scope="col">Status</th>
                <th width="15%" scope="col">Processed By</th>
                <th width="10%" scope="col">Action</th>
              </tr>
               <?php
				if($num < 1) echo 'No records found';
        $i=0;
        while($row = mysqli_fetch_assoc($result)){
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

					if($direction=='IN') { $img = '<img style=" vertical-align:middle;" src="'.BASE.'media/images/in.png" /> <green>IN</green>';} else {$img = '<img style=" vertical-align:middle;" src="'.BASE.'media/images/out.png" /> <red>OUT</red>';}
				?>

              <div class="virtualpage hidepeice">
              <tr class="inner">
                <td > <?php echo $date; ?></td>
                <td > <?php echo $desc; ?></td>
                <td > <?php echo $userSymbul.number_format($amount, 2); ?></td>
                <td > <?php echo $tag1.$status.$tag2; ?></td>
                <td > <?php echo $processedBy; ?></td>
                <td valign="middle">
				<?php	if($invoice_id>0) { ?>
                <a href="transaction?viewinvoice=<?php echo $invoice_id;?>"><button>Invoicee</button></a> 
                <?php }	else {
				echo '-';
				} ?></td>
              </tr>
              </div>
              <?php $i++;
            } ?>
              </table>
<!-- Pagination start -->
<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
<?php     ?>
</div>
