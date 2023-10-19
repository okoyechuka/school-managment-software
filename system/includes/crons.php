<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
global $server;
global $hooks;
# ---------------------------------------------------------------------
#  Add all cron scripts here
#  This script will be executed each time cron job is executed
#----------------------------------------------------------------------

mysqli_query($server, "UPDATE settings SET `value` = '".time()."' WHERE `field` = 'lastMinuteCron'");
$hooks->do_action('BeforeMinuteCron');

//Send 60 individual message recipients
$limit = 100;
$query="SELECT * FROM messagedetails WHERE status LIKE '%queued%' ORDER BY id ASC LIMIT ".$limit;
$result = mysqli_query($server,$query) or die(mysqli_error($server));
while($rows = mysqli_fetch_assoc($result)) { 
	sendSMS($rows['id']);
	$hooks->do_action('AfterMessageProcess');
}

//process pending messages
$query="SELECT * FROM sentmessages WHERE status LIKE '%queued%' ORDER BY id ASC LIMIT 6";
$result = mysqli_query($server,$query) or die(mysqli_error($server));
while($rows = mysqli_fetch_assoc($result)) { 
	mysqli_query($server, "UPDATE sentmessages SET `status` = 'processing' WHERE `id` = '".$rows['id']."'");
	mysqli_query($server, "UPDATE sentmessages SET `sent_on` = '".date('Y-m-d H:i:s')."' WHERE `id` = '".$rows['id']."'");
	$to = $rows['recipients'];
	$tos = explode(',', $to);
	foreach($tos as $recipient){
		if(!empty($recipient)) {
			$datetime = date('Y-m-d H:i:s');
			$now = time();
			mysqli_query($server, "INSERT INTO messagedetails (`message_id`, `message`,`recipient`, `customer_id`, `date`, `sender_id`, `datetime`, `status`, `notice`) 
			VALUES ('$rows[id]', '".mysqli_real_escape_string($server,$rows['message'])."','$recipient', '$rows[customer_id]', '$now', '$rows[sender_id]', '$datetime', 'queued',  '');"); $error = mysqli_error($server);
		}
	}
	mysqli_query($server, "UPDATE sentmessages SET `status` = 'completed' WHERE `id` = '".$rows['id']."'");
}

//process pending emails
$query="SELECT * FROM emails WHERE `status` = 'queued' LIMIT 1";
$result = mysqli_query($server,$query) ;
$n = 0;
while ($rows = mysqli_fetch_assoc($result)) {	
	mysqli_query($server, "UPDATE  `emails` SET  `status` =  'processing' WHERE `id` = '$rows[id]'");	
	set_time_limit(0);
	$n += 1;
	$from = $rows['address'];
	$sender = $rows['sender'];
	$message = $rows['message'];
	$to = $rows['recipient'];
	$subject = $rows['subject'];
	$school_id = $rows['customer_id'];
	$emailList = explode(',', $to);
	foreach($emailList as $emailAdd) {
		sendEmail($from,$sender,$subject,$emailAdd,$message,$school_id);
	}
	mysqli_query($server, "UPDATE  `emails` SET  `status` =  'sent' WHERE `id` = '$rows[id]'");	
}

//take backup
$date = date('H:i');
$type = 'Automatic Backup';
global $confighost;
global $configdatabase;
global $configuser;
global $configpassword;

if($date == '12:05') backup_tables($confighost,$configuser,$configpassword,$configdatabase,$type);	
global $hooks;
$hooks->do_action('AfterMinuteCron');	
mysqli_close($server);