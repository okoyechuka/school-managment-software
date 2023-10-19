<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;
  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

$message = '';
//$message = 'Sorry but this feature is not available on your current version of SOA';
//$class = 'yellow';

if(userRole($userID) > 2) { 
header('location: index.php');
}
?>
<script>
//function to chect sender id lenght
	function senderLenght() {
		var sender = document.getElementById('senderID').value.lenght;
		if (sender > 1) 
		{
			alert('Your Sender ID cannt be longer than 11 characters!');
		} else {
		}
	}
			
//functin to toggle recipients
$(document).ready(function() {
	document.getElementById('numberBox').style.display = 'block';
	document.getElementById('numberMsg').style.display = 'block';
	document.getElementById('numberButton').style.backgroundColor = '#930';
 });
</script>
<div class="wrapper">
	<div class="inner-left" style="width: 90%;">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>     

<div id="add-new">
	<div id="add-new-head"><?php echo 'Import Records from CSV File'; ?>
    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
     <div class="inside" style="overflow: hidden;">    
		<div id="mess" style="position: relative; top: 0;">     
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>        
		<iframe style="width: 99%; min-height: 100%; border: 1px solid transparent;" src="../drivers/csvimporter.php"></iframe>
	</div>
</div>    
  </div>
</div>
