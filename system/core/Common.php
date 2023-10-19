<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
*
* Create database connection
* @access	public
* @param	string
* @return	bool	TRUE if the current version is $version or higher
*/
$Config = new CI_Config();
if($Config->installed()!='INSTALLED') header('location: install/index.php');
$server=mysqli_connect($Config->db_host(), $Config->db_username(), $Config->db_password());
$db=mysqli_select_db($server, $Config->db_name());
global $server;

function utf8_to_unicode($str) {
	$unicode = array();
	$values = array();
	$lookingFor = 1;
	for ($i = 0; $i < strlen($str); $i++) {	
		$thisValue = ord($str[$i]);		
		if ($thisValue < 128) {			
			$unicode[] = str_pad(dechex($thisValue), 4, "0", STR_PAD_LEFT);
		} else {
			if (count($values) == 0) $lookingFor = ($thisValue < 224) ? 2 : 3;
				$values[] = $thisValue;
			if (count($values) == $lookingFor) {
				$number = ($lookingFor == 3) ?
				(($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64):
				(($values[0] % 32) * 64) + ($values[1] % 64);
				$number = strtoupper(dechex($number));		
				$unicode[] = str_pad($number, 4, "0", STR_PAD_LEFT);
				$values = array();
				$lookingFor = 1;
			} 
		} 
	} 
	return ($unicode);
}


if ( ! function_exists('sendSMS')){
	function sendSMS($message_id) {
		global $LANG;
		global $server;
		mysqli_query($server, "UPDATE messagedetails SET `status` = 'processing' WHERE `id` = '$message_id'");
		$THIS_MESSAGE_ID = $message_id;
		$main_id = singleMessageData($THIS_MESSAGE_ID,'message_id');
		$customer_id = singleMessageData($THIS_MESSAGE_ID,'customer_id');
		$to = singleMessageData($THIS_MESSAGE_ID,'recipient');
		$message = singleMessageData($THIS_MESSAGE_ID,'message');
		//load the message gateway. Remember $THIS_MESSAGE_GATEWAY and $THIS_MESSAGE_ID will be useful in your module
		$alias = 'custom';
		include 'smsapi/'.$alias.'/index.php';
	}
	return true;
}

	function sendEmail($from,$sender,$subject,$to,$message,$school_id) {
		global $LANG;
		if($school_id<1) $school_id = $_SESSION['school_id'];
		$email_from = $sender.'<'.$from.'>';
		$email_subject = $subject;
		$to = $to;
		//Build HTML Message
		$emessage2 = '<html><body>';
		$emessage2 .= $message;
		$emessage2 .= '</body></html>';
		$body = $emessage2;
		$smtpfrom = getSetting('smtpUsername',$school_id);
		
		if(!empty($smtpfrom)) {
			require_once BASEPATH.'Mailer/PHPMailerAutoload.php';
			$mail = new PHPMailer;

			//$mail->SMTPDebug = 3;                               // Enable verbose debug output			
			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host =  getSetting('smtpServer',$school_id);  			// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = getSetting('smtpUsername',$school_id);         // SMTP username
			$mail->Password = getSetting('smtpPassword',$school_id);          // SMTP password
			$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = getSetting('smtpPort',$school_id);                  // TCP port to connect to
			
			$mail->setFrom($smtpfrom, $sender);
			//set recipients
			$emailList = explode(',', $to);
			if(count($emailList) > 1) {
				foreach($emailList as $emailAdd) {
					$mail->addAddress($emailAdd);   
					$mail->isHTML(true);                                  // Set email format to HTML
					
					$mail->Subject = $subject;
					$mail->Body    = $body;
					$mail->AltBody = 'This email contain HTML that could not be displayed by your email reader. Please use a HTML email reader instead.';
					
					if(!$mail->send()) {
						$return = resendEmail($from,$sender,$subject,$emailAdd,$message);
					} else {
						$return = 'Sent';
					}				
				}
			} else {
				$mail->addAddress($to); 
				$mail->isHTML(true);                                  // Set email format to HTML
				
				$mail->Subject = $subject;
				$mail->Body    = $body;
				$mail->AltBody = 'This email contain HTML that could not be displayed by your email reader. Please use a HTML email reader instead.';
				
				if(!$mail->send()) {
					$return = resendEmail($from,$sender,$subject,$to,$message);
				} else {
					$return = 'Sent';
				}				
			} 
		} else {
		//Send Mail using PHP Mailer
			$headers = "From: " . getSetting('smtpUsername',$school_id). "\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";	
			//Send the email!
			mail($to,$email_subject,$emessage2,$headers);	
		}
	}	


	function resendEmail($from,$sender,$subject,$to,$message,$school_id) {
		global $LANG;
		if($school_id<1) $school_id = $_SESSION['school_id'];
		$email_from = $sender.'<'.$from.'>';
		$email_subject = $subject;
		$to = $to;
		//Build HTML Message
		$emessage = '<html><body>';
		$emessage .= $message;
		$emessage .= '</body></html>';
		$body = $emessage;
		$smtpfrom = getSetting('smtpUsername',$school_id);
		
		if(!empty($smtpfrom)) {
			require_once BASEPATH.'Mailer/PHPMailerAutoload.php';
			$mail = new PHPMailer;

			//$mail->SMTPDebug = 3;                               // Enable verbose debug output			
			$mail->isMail() ;//$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host =  getSetting('smtpServer',$school_id);  			// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = getSetting('smtpUsername',$school_id);         // SMTP username
			$mail->Password = getSetting('smtpPassword',$school_id);          // SMTP password
			$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = getSetting('smtpPort',$school_id);                  // TCP port to connect to
			
			$mail->setFrom($smtpfrom, $sender);
			//set recipients
			$emailList = explode(',', $to);
			if(count($emailList) > 1) {
				foreach($emailList as $emailAdd) {
					$mail->addAddress($emailAdd);   
					$mail->isHTML(true);                                  // Set email format to HTML
					
					$mail->Subject = $subject;
					$mail->Body    = $body;
					$mail->AltBody = 'This email contain HTML that could not be displayed by your email reader. Please use a HTML email reader instead.';
					
					if(!$mail->send()) {
						$return = 'Error: ' . $mail->ErrorInfo;
					} else {
						$return = 'Sent';
					}				
				}
			} else {
				$mail->addAddress($to); 
				$mail->isHTML(true);                                  // Set email format to HTML
				
				$mail->Subject = $subject;
				$mail->Body    = $body;
				$mail->AltBody = 'This email contain HTML that could not be displayed by your email reader. Please use a HTML email reader instead.';
				
				if(!$mail->send()) {
					$return = 'Error: ' . $mail->ErrorInfo;
				} else {
					$return = 'Sent';
				}				
			} 
		} else {
		//Send Mail using PHP Mailer
			$headers = "From: " . getSetting('smtpUsername',$school_id). "\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";	
			//Send the email!
			mail($to,$email_subject,$emessage,$headers);	
		}
	}	

function setTimeZone() {
	global $server;
	$serverTimeZone = getSetting('timeZone',$_SESSION['school_id']);
	if(!empty($serverTimeZone))	
	date_default_timezone_set($serverTimeZone);
	$timzn = date_default_timezone_get();	
}

function format_size($size) {
      $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
      if ($size == 0) { return('0 Bytes'); } else {
      return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); }
}
function curl_get_contents($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function gateAPIBalance($url) {
	if(empty($url)) return 'Unknown';
	$returned = curl_get_contents($url);
	if(!empty($returned)) {
		return $balance = correctNumber($returned);
	}	
	return 'Unknown';
}
function paymentGatewayData($id,$field) {
  global $server;	
	$query="SELECT $field as value FROM paymentgateways WHERE id = '$id'"; 
	$result = mysqli_query($server,$query) or die(mysqli_error($server));  
	$row = mysqli_fetch_assoc($result); 
	$value = $row['value'];	
	if($value=='0') return '0';
	if(empty($value)) {
	return '';
	} else {
	return $value;
	}
}
function singleMessageData($id,$field) {
  global $server;	
	$query="SELECT $field as value FROM messagedetails WHERE id = '$id'"; 
	$result = mysqli_query($server,$query) or die(mysqli_error($server));  
	$row = mysqli_fetch_assoc($result); 
	$value = $row['value'];	
	if($value=='0') return '0';
	if(empty($value)) {
	return '';
	} else {
	return $value;
	}
}

function smsGatewayData($id,$field) {
  global $server;	
	$query="SELECT value FROM settings WHERE school_id = '$id' AND field = '$field'"; 
	$result = mysqli_query($server,$query) or die(mysqli_error($server));  
	$row = mysqli_fetch_assoc($result); 
	$value = $row['value'];	
	if($value=='0') return '0';
	if(empty($value)) {
	return '';
	} else {
	return $value;
	}
}
function paymentGatewayTemplateData($alias,$field) {
  global $server;	
	$query="SELECT $field as value FROM paymentgateway_templates WHERE alias = '$alias'"; 
	$result = mysqli_query($server,$query) or die(mysqli_error($server));  
	$row = mysqli_fetch_assoc($result); 
	$value = $row['value'];	
	if($value=='0') return '0';
	if(empty($value)) {
	return '';
	} else {
	return $value;
	}
}
function smsGatewayTemplateData($alias,$field) {
  global $server;	
	$query="SELECT $field as value FROM api_templates WHERE id = '$alias'"; 
	if(!is_numeric($alias)) $query="SELECT $field as value FROM api_templates WHERE alias = '$alias'"; 
	$result = mysqli_query($server,$query) or die(mysqli_error($server));  
	$row = mysqli_fetch_assoc($result); 
	$value = $row['value'];	
	if($value=='0') return '0';
	if(empty($value)) {
	return '';
	} else {
	return $value;
	}
}

function removeHTML($text) {
	$a = array('<p>','</p>','<div>','</div>');
	$b = array(' ','<br>',' ','<br>');
	return str_replace($a,$b,$text);	
}

function convertTime($date) {
	if(is_numeric($date)) {
		$date = date('Y-m-d H:i:s',$date);
		$date2 = date('Y-m-d',$date);
	} else {
		$date = strtotime($date);
		$date = date('Y-m-d H:i:s', $date);
		$date2 = date('Y-m-d',$date);
	}
	return str_replace($date2.' ','',$date);
}

function exportToCSV($name,$table,$fields,$extra='') {
	global $server;
	$name .= date('Y_m_d_h_i_s');
    $sql = "SELECT $fields FROM `$table` ".$extra;
	$result = $server->query($sql);
	if (!$result) die('Couldn\'t fetch records');
	$num_fields = mysqli_num_fields($result);
	$headers = array();
	foreach(explode(',',$fields) as $heds)  {
		$headers[] = ucfirst($heds);
	}
	$fp = fopen('php://output', 'w');
	if ($fp && $result) {
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="'.$name.'.csv"');
		header('Pragma: no-cache');
		header('Expires: 0');
		fputcsv($fp, $headers);
		while ($row = $result->fetch_array(MYSQLI_NUM)) {
			fputcsv($fp, array_values($row));
		}
		die;
	}
} 

function showMessage($message, $class) {
	if(empty($class) || is_numeric($class)) {
		$class='blue';
	}
	$info = 'info';
	$tit = 'Attention';
	if($class=='green') { 
		$info = 'success';
		$tit = 'Done';
	}
	if($class=='yellow') { 
		$info = 'warning';
	}
	if($class=='red') { 
		$info = 'warning';
		$tit = 'Oops!';
	}
	?>
	<script>
	swal({ title: "<?=$tit?>", text: '<?=str_replace("'","\'",trim(preg_replace('/\s+/',' ',$message)))?>',type: "<?=$info?>",html: true ,showCancelButton: false, confirmButtonColor: "#086", confirmButtonText: "OK",closeOnConfirm: true }, function(){ });
	</script> 
<?php
}


if(!function_exists('activatePIN')) {
	function activatePIN($message, $class, $pin_id) {
		//load defaults
	$siteName = getSetting('name');
	$logo = getSetting('logo');
	$siteLogo = 'media/uploads/'.getSetting('logo');
	$pin_enabled = getSetting('pin_enabled');
	if(empty($siteName)): $siteName = 'SOA'; endif;
	if(empty($logo)): $siteLogo = 'media/images/logo.png'; endif;
?>
    	<div id="mess" style="width: 100%;">
            <?php if(!empty($message)) { showMessage($message, $class); } ?>
        </div>

   <div id="login-center">
            <div id="login-head"><img src="<?php echo BASE; ?>media/images/login-white.png" /> Profile Activation</div>
			<div id="login-form">
            <img src="<?php echo BASE.$siteLogo; ?>" alt="<?php echo $siteName; ?>" title="<?php echo $siteName; ?>" />
            <form method="post" action="" name="login">
                <p><input type="text" name="student_profile" onfocus="if(this.value  == 'Admission Number') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Admission Number'; } "></p>
                <p><button type="submit" id="profile-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Next</button></p>
                <input type="hidden" name="pin_id" value="<?php echo $pin_id; ?>" />
            </form>
			<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Please wait...</div>
        </div>
    </div>

  </body>
</html>
 <?php
 	die();
	}
}


if(!function_exists('confirmStudentID')) {
	function confirmStudentID($pin_id,$student_id,$name) {
		$message = 'We have identified you as <b>'.$name.'</b>. Click "Activate" to link your PIN to this student';
		$class = 'blue';

		//load defaults
	$siteName = getSetting('name');
	$logo = getSetting('logo');
	$siteLogo = 'media/uploads/'.getSetting('logo');
	$pin_enabled = getSetting('pin_enabled');
	if(empty($siteName)): $siteName = 'SOA'; endif;
	if(empty($logo)): $siteLogo = 'media/images/logo.png'; endif;
?>
    	<div id="mess" style="width: 100%;">
            <?php showMessage($message, $class); ?>
        </div>


   <div id="login-center">
            <div id="login-head"><img src="<?php echo BASE; ?>media/images/login-white.png" /> Profile Activation</div>
			<div id="login-form"><p><?php echo $message; ?><br /></p>
            <img src="<?php echo BASE.$siteLogo; ?>" alt="<?php echo $siteName; ?>" title="<?php echo $siteName; ?>" />
            <form method="post" action="" name="login">
                <input type="hidden" name="pin_id" value="<?php echo $pin_id; ?>" />
                <input type="hidden" name="student_confirm" value="<?php echo $student_id; ?>" />
                <p><button type="submit" id="profile-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Activate</button></p>
                <p><button type="button" id="prof-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Cancel</button></p>
            </form>
			<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Activating...</div>
        </div>
    </div>

  </body>
</html>
 <?php
 	die();
	}
}

function setBackground() {
	return false;
}
function setGateway($id,$value) {
	global $server;
	$sql=$query="SELECT * FROM gateways WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row[$value];
	if(empty($value)) {
	return '';
	} else {
	return $value;
	}
}
function setBackgroundColor() {
	return false;
}
function setHeaderColor() {
	return false;
}
function setFooterColor() {
	return false;
}
function canAccessPortal($id) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$role = $row['role_id'];
	$profile_id = $row['profile_id'];

	if(($role == 5) || ($role == 6)) {
		if($role == 6) {
			$sql=$query="SELECT * FROM students WHERE id = '$profile_id'";
			$result = mysqli_query($server, $query) or die(mysqli_error($server));
			$row = mysqli_fetch_assoc($result);
			$portal_access = $row['portal_access'];
		}
		if($role == 5) {
			$sql=$query="SELECT * FROM parents WHERE id = '$profile_id'";
			$result = mysqli_query($server, $query) or die(mysqli_error($server));
			$row = mysqli_fetch_assoc($result);
			$portal_access = $row['portal_access'];
		}
	} else {
		$portal_access = 0;
	}

	if($portal_access < 1) {
	return false;
	} else {
	return true;
	}
}
function getAdmissionNumber($school_id,$year) {
	global $server;
	$sql=$query= "SELECT * FROM students WHERE school_id = '$school_id'";
	$result = mysqli_query($server, $query);
	$studentCount = mysqli_num_rows($result)+1;
	$year = date('y', strtotime($year.'-01-01'));
	$admission_number = 'ST/'.$year.'/'.sprintf('%04d',$studentCount);
	return $admission_number;
}
function getApplicationNumber($school_id,$year) {
	global $server;
	$sql=$query= "SELECT * FROM applicants WHERE school_id = '$school_id'";
	$result = mysqli_query($server, $query);
	$studentCount = mysqli_num_rows($result)+1;
	$year = date('y', strtotime($year.'-01-01'));
	$admission_number = 'AP/'.$year.'/'.sprintf('%04d',$studentCount);
	return $admission_number;
}
function getApplicantName($pin) {
	global $server;
	$sql=$query= "SELECT * FROM applicants WHERE id = '$pin'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	return $row['first_name'].' '.$row['last_name'];
}
function generatePIN($quantity, $school_id, $validity_type, $session_id, $term_id, $uses) {
	global $server;
	for($i3 = 0; $i3 < $quantity; $i3++){
		$characters = '1234567890';
		$random_string_length = '12';
		 $serial = '';
		 for ($i2 = 0; $i2 < $random_string_length; $i2++) {
			  $serial .= $characters[rand(0, strlen($characters) - 1)];
		 }

		 $characters = 'ABCDEFGHJKLMNPQRTUVWXY23456789';
		$random_string_length = '8';
		 $pin = '';
		 for ($i = 0; $i < $random_string_length; $i++) {
			  $pin .= $characters[rand(0, strlen($characters) - 1)];
		 }
		 $salt = genRandomPassword(32);
		 $crypt = getCryptedPassword($pin, $salt);
		 $pin2 = $crypt.':'.$salt;

		 $date = date('Y-m-d');
 		$description = $quantity.' PIN Generated on '.date('d/m/Y', strtotime($date));
 		$sql=$query= "INSERT INTO `pin` (`id`, `school_id`, `validity_type`, `session_id`, `term_id`, `serial`, `pin`, `date_generated`,`valid`,`description`,`uses`)
				VALUES (NULL, '$school_id', '$validity_type', '$session_id', '$term_id', '$serial', '$pin2', '$date','$pin','$description', '$uses');";
		mysqli_query($server, $query) or die (mysqli_error($server));
	}
return $description;
}

function feePaid($student,$fee, $session,$term) {
	global $server;
	$sql=$query="SELECT * FROM fee_paid WHERE fee_id = '$fee' AND student_id = '$student' AND term_id = '$term' AND session_id = '$session'";
	$result = mysqli_query($server, $query) ;
	$row = mysqli_fetch_assoc($result);
	$found = mysqli_num_rows($result);
	if($found < 1) {
	return false;
	} else {
	return true;
	}
}
function assignmentAnswered($student,$assignment) {
	global $server;
	$sql=$query="SELECT * FROM student_assignments WHERE assignment_id = '$assignment' AND student_id = '$student'";
	$result = mysqli_query($server, $query) ;
	$row = mysqli_fetch_assoc($result);
	$found = mysqli_num_rows($result);
	if($found < 1) {
	return 'Pending';
	} else {
	return 'Answered';
	}
}
function getName($id) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['name'];
	return $value;
}

function getStaffData($data,$user) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE id = '$user'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$role = $row['role_id'];
	$profile = $row['profile_id'];

	if($role == 4) {
		$sql=$query="SELECT ".$data." as value FROM teachers WHERE id = '$profile'";
	} else {
		$sql=$query="SELECT ".$data." as value FROM staffs WHERE id = '$profile'";
	}
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);

	return $row['value'];
}

function sumSlip($id) {
	global $server;
	$sql=$query= "SELECT SUM(amount) AS value, SUM(allowance) AS valueb, SUM(deduction) AS valuec, SUM(paye) AS valued FROM salary_pay WHERE month = '$id'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	$salary = $row['value'];
	$allowance = $row['valueb'];
	$deduction = $row['valuec'];
	$paye = $row['valued'];
	return $salary+$allowance-$paye-$deduction;
}

function slipPaid($id) {
	global $server;
	$sql=$query= "SELECT * FROM salary_pay WHERE id = '$id'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	$status = $row['status'];
	if($status =='Paid') {
	return true;
	} else {
		return false;
	}
}

function countSlip($id) {
	global $server;
	$sql=$query= "SELECT * FROM salary_pay WHERE month = '$id' ";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	return $num;
}

function payslipAmount($id) {
	global $server;
	$sql=$query= "SELECT * FROM salary_pay WHERE id = '$id'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	return $row['amount']+$row['allowance']-$row['deduction']-$row['paye'];
}

function payslipMonth($id) {
	global $server;
	$sql=$query= "SELECT * FROM salary_pay WHERE id = '$id'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	return $row['month'];
}

function paySlip($date_due, $generated_by, $school_id, $status) {
	global $server;
	//check if payslip exist for this month
	$current_month = date('Y-m', strtotime($date_due));
	$sql=$query="SELECT * FROM salary_pay l WHERE l.school_id = '$school_id' AND l.month = '$current_month'";
	$result = mysqli_query($server, $query);
	$numPay = mysqli_num_rows($result);
	if($numPay < 1) {
		//fetch all salary
		$sql=$query= "select * FROM users WHERE school_id = '$school_id' AND (role_id =2 OR role_id =3 OR role_id =4 OR role_id >6)";
		$result = mysqli_query($server, $query);
		$num = mysqli_num_rows($result);
		$count = 0;
		while ($row = mysqli_fetch_assoc($result)) {
				$staff = $id = $row['id'];
				$role = $row['role_id'];
				$salary = getStaffData('payroll',$id);
				$allowance = getStaffData('allowance',$id);
				$deduction = getStaffData('deduction',$id);
				$paye = getStaffData('paye',$id);

				if($salary > 0) {
					//create pay slip
					$sql=$query="INSERT INTO salary_pay (`id`, `staff_id`, `amount`, `allowance`, `deduction`, `paye`, `date_due`, `status`, `generated_by`, `month`, `school_id`)
								VALUES (NULL, '$staff', '$salary','$allowance','$deduction','$paye', '$date_due', '$status', '$generated_by', '$current_month', '$school_id');";

					$add = mysqli_query($server, $query) or die (mysqli_error($server));
				}else {
					$count += 1;
				}
		}

		if($count > 0) {
			$added = $count .' staff(s) with zero salary skiped';
		}else {
			$added = '';
		}
			return 'Payslips successfully generated for '.date('F Y', strtotime($date_due)).' salaries.'.$added;
	} else {
		return 'Sorry but payslip has already been generated for '.date('F Y', strtotime($date_due));
	}
}
function filterinp($input,$html=false){
	global $server;
	if(is_array($input)) return $input;
	if($html==false) {
		$strip = strip_tags($input);
		$strip = str_replace('">','',$strip);
		$strip = str_replace('/>','',$strip);
		$input = $strip;
	} else {
		$input = str_replace('<html','',$input);
		$input = str_replace('</htnl>','',$input);


		$input = str_replace('<script','',$input);
		$input = str_replace('</script>','',$input);
	}
	$previn = mysqli_real_escape_string($server, $input);
	return $previn;
}

function addDays($date, $days) {
	$time = strtotime($date);
	if(is_numeric($date)) {
		$time = $date;
	}
	return date('Y-m-d', ($time+($days*86400)) );
}
function lessDays($date, $days) {

	$time = strtotime($date);
	if(is_numeric($date)) {
		$time = $date;
	}
	return date('Y-m-d', ($time-($days*86400)) );
}
function canLogin($user,$role) {
	global $server;
	if($role == 6) {
	$sql=$query="SELECT * FROM students WHERE id = '$user'";
	} else {
	$sql=$query="SELECT * FROM parents WHERE id = '$user'";
	}
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['portal_access'];
	if($value > 0) { return true;} else { return false; }
}

function userRole($id) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['role_id'];
	return $value;
}

function countPayments() {
	global $server;
	$school = getAdminSchool();
	$sql=$query= "SELECT * FROM paymentalerts WHERE status = 'Pending' AND school_id = '$school'";
	$result = mysqli_query($server, $query);
	$value = mysqli_num_rows($result);
	return $value;
}
function countPIN($desc) {
	global $server;
	$school = getAdminSchool();
	$sql=$query= "SELECT * FROM pin WHERE description = '$desc' AND school_id = '$school'";
	$result = mysqli_query($server, $query);
	$value = mysqli_num_rows($result);
	return $value;
}
function countStudent() {
	global $server;
	$school = getAdminSchool();
	$sql=$query="SELECT COUNT(id) AS value FROM students WHERE school_id = '$school'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['value'];
	return $value;
}
function realDate($date) {
	return date('F d, Y', strtotime($date));
}
function countClass($class) {
	global $server;
	$school = getAdminSchool();
	$sql=$query="SELECT COUNT(id) AS value FROM student_class WHERE class_id = '$class'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['value'];
	return $value;
}
function countAssignment($class) {
	global $server;
	$school = getAdminSchool();
	$sql=$query="SELECT COUNT(id) AS value FROM student_assignments WHERE assignment_id = '$class'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['value'];
	return $value;
}
function countHostel($hostel) {
	global $server;
	$school = getAdminSchool();
	$sql=$query="SELECT COUNT(id) AS value FROM student_hostel WHERE hostel_id = '$hostel'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['value'];
	return $value;
}
function countBus($hostel) {
	global $server;
	$school = getAdminSchool();
	$sql=$query="SELECT COUNT(id) AS value FROM student_bus WHERE bus_id = '$hostel'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['value'];
	return $value;
}

function countStaff($sc=0) {
	global $server;
	$school = getAdminSchool();
	if($sc>0) $school = $sc;
	$sql=$query="SELECT COUNT(id) AS value FROM users WHERE role_id != '4' AND role_id != '5' AND role_id != '6' AND school_id = '$school'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['value'];
	return $value;
}
function countParent($sc=0) {
	global $server;
	$school = getAdminSchool();
	if($sc>0) $school = $sc;
	$sql=$query="SELECT COUNT(id) AS value FROM parents WHERE school_id = '$school'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['value'];
	return $value;

}


function countTeacher($sc=0) {
	global $server;
	$school = getAdminSchool();
	if($sc>0) $school = $sc;
	$sql=$query="SELECT COUNT(id) AS value FROM teachers WHERE school_id = '$school'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['value'];
	return $value;
}
function countSchoolClass($sc=0) {
	global $server;
	$school = getAdminSchool();
	if($sc>0) $school = $sc;
	$sql=$query="SELECT COUNT(id) AS value FROM classes WHERE school_id = '$school'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['value'];
	return $value;
}

function getAdminName($id) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['name'];
	return $value;
}
function getUsername($id) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['username'];
	return $value;
}
function gatewayAlias($id) {
	$sql=$query="SELECT * FROM paymentgateways WHERE id = '$id'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	$name = $row['alias'];
	if($id < 1) {
		$name	= 'System';
	}
	return $name;
}

function getUserID($id, $role) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE profile_id = '$id' AND role_id = '$role'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['id'];
	return $value;
}
function getAdminUsername($id) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['username'];
	return $value;
}

if ( ! function_exists('read_file'))
{
	function read_file($file)
	{
		if ( ! file_exists($file))
		{
			return FALSE;
		}

		if (function_exists('file_get_contents'))
		{
			return file_get_contents($file);
		}

		if ( ! $fp = @fopen($file, 'r+'))
		{
			return FALSE;
		}

		flock($fp, LOCK_SH);

		$data = '';
		if (filesize($file) > 0)
		{
			$data =& fread($fp, filesize($file));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $data;
	}
}

if ( ! function_exists('write_file'))
{
	function write_file($path, $data, $mode = 'w+')
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		return TRUE;
	}
}

function updateSystem($version,$source,$destination) {
	$return = true;
	if(!file_exists($source)) {
		$return = 'Unable to complete system update. Required update files are missing!';
	}
	copy($source, $destination);
	$file_path = dirname(__FILE__).'/config.inc';
	$resultsd = @chmod($file_path, 0777);

	include($file_path);
	$cv = $sconfig['verssion_id'];
	$data3 = read_file($file_path);
	$data3 = str_replace($cv, $version, $data3);
	write_file($file_path, $data3);
return $return;
}

function invoiceAmount($id) {
	global $server;
	$sql=$query="SELECT * FROM invoives WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['amount'];
	return $value;
}
function transactionGateway($id) {
	global $server;
	$sql=$query="SELECT * FROM transactions WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$gateway = $row['gateway'];

	$sql=$query="SELECT * FROM paymentgateways WHERE id='$gateway'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	$value = $row['name'];
	return $value;
}
function userProfile($id) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['profile_id'];
	return $value;
}

function bookName($id) {
	global $server;
	$sql=$query="SELECT * FROM books WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'].' by '.$row['author'];
	return $value;
}

function bookAvailable($id) {
	global $server;
	$sql=$query="SELECT * FROM books WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['catalog'];
	return $value;
}

function portalUsername($id, $role_id) {
	global $server;
	switch($role_id) {
		case 'Student': $role_id = '6'; break;
		case 'Parent': $role_id = '5'; break;
	}
	$sql=$query="SELECT * FROM users WHERE profile_id = '$id' AND role_id = '$role_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['username'];
	return $value;
}

function portalUserID($id, $role_id) {
	global $server;
	switch($role_id) {
		case 'Student': $role_id = '6'; break;
		case 'Parent': $role_id = '5'; break;
	}
	$sql=$query="SELECT * FROM users WHERE profile_id = '$id' AND role_id = '$role_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['id'];
	return $value;
}

function adminData($field, $id) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row[ $field ];
	if(empty($value)) {
	return 'not available';
	} else {
	return $value;
	}
}

function userData($field, $id) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row[ $field ];
	if(empty($value)) {
	return '';
	} else {
	return $value;
	}
}

function userDocument($user, $id) {
	global $server;
	$sql=$query="SELECT * FROM library WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['user_id'];
	if($value == $user) {
	return true;
	} else {
	return false;
	}
}

function getInitial($intro) {
	$string = strip_tags($intro);
	$stringCut = '';
	if (strlen($string) > 1)
	{
    // truncate string
    $stringCut = substr($string, 0, 1);
	}

	return $stringCut.'.';
}

function shorten($intro, $len) {
	$string = strip_tags($intro);
	if (strlen($string) > $len)
	{
    // truncate string
    $stringCut = substr($string, 0, $len);
    // make sure it ends in a word so assassinate doesn't become ass...
    $string = substr($stringCut, 0, strrpos($stringCut, ' ')).'... ';
	}

	return $string;
}
function parentName($id) {
	global $server;
	$sql=$query="SELECT * FROM parents WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = 'Mr. & Mrs '.$row['father_name'];
	return $value;
}
function guardianName($id) {
	global $server;
	$sql=$query="SELECT * FROM guardians WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['first_name'].' '.$row['last_name'];
	return $value;
}
function guardianData($id) {
	global $server;
	$sql=$query="SELECT * FROM guardians WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = 'Name: '.$row['first_name'].' '.$row['last_name'].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Phone: '.$row['phone'];
	return $value;
}
function guardianStudent($id) {
	global $server;
	$sql=$query="SELECT * FROM student_guardian WHERE guardian_id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['student_id'];
	return $value;
}
function getStudentByAdmissionNumber($id) {
	global $server;
	$sql=$query="SELECT * FROM students WHERE admission_number = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['id'];
	return $value;
}
function guardianStudentExist($s_id, $g_id) {
	global $server;
	$sql=$query="SELECT * FROM student_guardian WHERE guardian_id = '$g_id' AND student_id = '$s_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}

function gradeExist($min, $max, $school) {
	global $server;
	$sql=$query="SELECT * FROM grades WHERE school_id = '$school' AND (start_mark = '$min' AND end_mark = '$max') ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}

function getGrade($mark, $school) {
	global $server;
	$sql=$query="SELECT * FROM grades WHERE school_id = '$school' AND (start_mark < '$mark' AND end_mark > '$mark')";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$value = $row['id'];
	if($num < 1) { $value = '0';}
	return $value;
}

function gradeName($id) {
	global $server;
	$sql=$query="SELECT * FROM grades WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	if($id < 1) { $value = 'Unknown';}
	return $value;
}

function transactionStatus($id) {
	global $server;
	$sql=$query="SELECT * FROM transaction_status WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	if($id < 1) { $value = 'Unknown';}
	return $value;
}

function gradeCode($id) {
	global $server;
	$sql=$query="SELECT * FROM grades WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$value = $row['code'];
	if($id < 1) { $value = '-';}
	return $value;
}
function formatPosition($num) {
	$poss = substr("$num", -1);
	if($poss == 1) {
		$added = 'st';
	}elseif($poss == 2) {
		$added = 'nd';
	}elseif($poss == 3) {
		$added = 'rd';
	} else {
		$added = 'th';
	}
	return $num.$added;
}

function cbtFinished($student,$cbt) {
	global $server;
	$query="SELECT is_finished FROM cbt_answers WHERE student_id = '$student' AND cbt_id = '$cbt' ORDER BY id ASC LIMIT 1";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['is_finished'];
	if($value > 0) return true;
	return false;
}
function countCorrectAnswered($student,$cbt) {
	global $server;
	$query = "SELECT * FROM cbt_answers WHERE cbt_id = '$cbt' AND student_id = '$student'";
	$result3 = mysqli_query($server, $query);
	$correct = 0;
	while($row = mysqli_fetch_assoc($result3)){
		$answer = $row['answer']; 
		$correctanswer = questionCorrectAnswer($row['question_id']); 
		if(strtolower(trim($correctanswer))==strtolower(trim($answer))) {$correct+=1;}
	}
	return $correct;
}
function questionCorrectAnswer($id) {
	global $server;
	$sql=$query= "SELECT * FROM cbt_questions WHERE id = '$id'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	return $row['correct_answer'];
}
function countAnsweredTaken($student,$cbt) {
	global $server;
	$query="SELECT count(id) as answered FROM cbt_answers WHERE student_id = '$student' AND cbt_id = '$cbt' AND answer != ''";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['answered'];
	return $value;
}

function cbtTaken($student,$cbt) {
	global $server;
	$query="SELECT count(id) as answers FROM cbt_answers WHERE student_id = '$student' AND cbt_id = '$cbt'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['answers'];
	return $value;
}
function cbtStartTime($student,$cbt) {
	global $server;
	$query="SELECT answer_date FROM cbt_answers WHERE student_id = '$student' AND cbt_id = '$cbt' ORDER BY id ASC LIMIT 1";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['answer_date'];
	return $value;
}
function cbtFinishTime($student,$cbt) {
	global $server;
	$sql=$query="SELECT competion_time FROM cbt_answers WHERE student_id = '$student' AND cbt_id = '$cbt' ORDER BY id DESC LIMIT 1";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['competion_time'];
	return $value;
}

function cbtStudentTiming($student,$cbt) {
	global $server;
	$started = cbtStartTime($student,$cbt);
	$completed = cbtFinishTime($student,$cbt);
	$allowed = cbtData('time_duration',$cbt);
	$duration = $completed-$started;
	if($duration < 1) return 0;
	return $duration>$allowed?$allowed:$duration;
}

function totalExam($student,$exam) {
	global $server;
	$sql=$query="SELECT SUM(exam_score) as ctotal_mark FROM exam_student_score WHERE student_id = '$student' AND exam_id = '$exam'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['ctotal_mark'];
	return $value;
}

function totalAssessment($student,$exam) {
	global $server;
	$sql=$query="SELECT SUM(assessment_1 + assessment_2 + assessment_3 + assessment_4 + assessment_5) as ctotal_mark FROM exam_student_score WHERE student_id = '$student' AND exam_id = '$exam'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['ctotal_mark'];
	return $value;
}

function totalSubExam($student,$exam,$subject) {
	global $server;
	$sql=$query="SELECT SUM(exam_score) as ctotal_mark FROM exam_student_score WHERE student_id = '$student' AND exam_id = '$exam' AND subject_id = '$subject'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$value = $row['ctotal_mark'];
	return $value;
}

function totalSubAssessment($student,$exam,$subject) {
	global $server;
	$sql=$query="SELECT SUM(assessment_1 + assessment_2 + assessment_3 + assessment_4 + assessment_5) as ctotal_mark FROM exam_student_score WHERE student_id = '$student' AND exam_id = '$exam' AND subject_id = '$subject'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['ctotal_mark'];
	return $value;
}

function subjectAvrg($subject,$exam) {
	global $server;
	//sum subject exam
	$query="SELECT SUM(assessment_1 + assessment_2 + assessment_3 + assessment_4 + assessment_5) as ctotal_mark, SUM(exam_score) as etotal_mark  FROM exam_student_score WHERE  exam_id = '$exam' AND subject_id = '$subject'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['ctotal_mark']+$row['etotal_mark'];
	//check number of students
	$query="SELECT count(student_id) as peoples FROM exam_student_score WHERE exam_id = '$exam' AND subject_id = '$subject'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value2 = $row['peoples'];
	return round($value/$value2,2);
}

function classRank($student,$exam) {
	global $server;
	$query="SELECT class_id FROM exam_student_score WHERE exam_id = '$exam' AND student_id = '$student' LIMIT 1";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$class_id = $row['class_id'];
	
	$sql=$query="SELECT SUM(assessment_1 + assessment_2 + assessment_3 + assessment_4 + assessment_5 + exam_score) AS rank, class_id, session_id, student_id FROM exam_student_score WHERE exam_id = '$exam' AND class_id = '$class_id' GROUP BY student_id ORDER BY rank DESC ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$value = '-';
	$i = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		$sid = $row['student_id'];
		$csid = $row['class_id'];
		$ssid = $row['session_id'];
		if($sid == $student) {
			$value = $i+1;
		}
		$i++;
	}
	return $value;
}

function subClassRank($student,$subject,$exam) {
	global $server;
	$query="SELECT class_id FROM exam_student_score WHERE exam_id = '$exam' AND student_id = '$student' LIMIT 1";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$class_id = $row['class_id'];
	
	$sql=$query="SELECT SUM(assessment_1 + assessment_2 + assessment_3 + assessment_4 + assessment_5 + exam_score) AS rank, class_id, session_id, student_id FROM exam_student_score WHERE subject_id = '$subject' AND exam_id = '$exam' AND class_id = '$class_id' GROUP BY student_id ORDER BY rank DESC ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$value = '-';
	$i = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		$sid = $row['student_id'];
		$csid = $row['class_id'];
		$ssid = $row['session_id'];
		if($sid == $student) {
			$value = $i+1;
		}
		$i++;
	}
	return $value;
}

function examPercentage($student,$exam) {
	global $server;
	$sql=$query="SELECT COUNT(subject_id) AS count FROM exam_student_score WHERE exam_id = '$exam' AND student_id = '$student'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$total = $row['count'];
	$score = totalAssessment($student,$exam)+totalExam($student,$exam);
	$value = ($score/$total);
	return $value;
}

function examSubjectCount($exam,$subject,$class_id=0) {
	global $server;
	$classid = "";
	if($class_id >0) $classid = " AND class_id = '$class_id' ";
	$sql=$query="SELECT * FROM exam_student_score WHERE exam_id = '$exam' AND subject_id = '$subject' $classid ORDER BY id DESC";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	return mysqli_num_rows($result);
}
function examClassCount($exam,$subject) {
	global $server;
	$sql=$query="SELECT * FROM exam_student_score e WHERE e.exam_id = '$exam' AND e.class_id = '$subject' GROUP BY e.student_id ORDER BY e.id DESC";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	return mysqli_num_rows($result);
}


// -=-=== Session stuffs -================== //
function sessionClassRank($student,$session) {
	global $server;
	$sql=$query="SELECT SUM(e.assessment_1 + e.assessment_2 + e.assessment_3 + e.assessment_4 + e.assessment_5 + e.exam_score) AS rank, e.class_id, e.session_id, e.student_id FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.session_id = '$session' AND ex.is_cumulative = 1 GROUP BY e.student_id ORDER BY rank DESC ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$value = '-';

	$i=0;
	while($row = mysqli_fetch_assoc($result)){
        $sid = $row['student_id'];
	   	$rank = $row['rank'];
		$csid = $row['class_id'];
		$ssid = $row['session_id'];
		if($sid == $student) {
			$value = $i+1;
		}
		if($csid==getClass($sid,$ssid)) {
			$i++;
		}
	}
	return $value;
}

function examSessionSubjectCount($session,$subject) {
	global $server;
	$sql=$query="SELECT * FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.session_id = '$session' AND e.subject_id = '$subject' AND ex.is_cumulative = 1 GROUP BY e.student_id";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	return mysqli_num_rows($result);
}

function sessionSubjectCount($student,$session,$subject) {
	global $server;
	$sql=$query="SELECT COUNT(e.subject_id) AS count FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.session_id = '$session' AND e.student_id = '$student' AND e.subject_id = '$subject' AND ex.is_cumulative = 1 ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	return $total = $row['count'];
}
function sessionSubjectsCount($student,$session,$subject) {
	global $server;
	$sql=$query="SELECT COUNT(e.subject_id) AS count FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.session_id = '$session' AND e.student_id = '$student' AND e.subject_id = '$subject' AND ex.is_cumulative = 1 ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	return $total = $row['count'];
}
function totalSessionSubjectsTaken($student,$session) {
	global $server;
	$sql=$query="SELECT COUNT(e.subject_id) AS count FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.session_id = '$session' AND e.student_id = '$student' AND ex.is_cumulative = 1 ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	return $total = $row['count'];
}
function examSessionClassCount($session,$subject) {
	global $server;
	$sql=$query="SELECT * FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.session_id = '$session' AND e.class_id = '$subject' AND ex.is_cumulative = 1 GROUP BY e.student_id ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	return mysqli_num_rows($result);
}

function totalClassScore($student,$session) {
	global $server;
	$sql=$query="SELECT SUM(e.assessment_1 + e.assessment_2 + e.assessment_3 + e.assessment_4 + e.assessment_5 + e.exam_score) as ctotal_mark FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id  WHERE e.student_id = '$student' AND e.session_id = '$session' AND ex.is_cumulative = 1 ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$value = $row['ctotal_mark'];
	return $value;
}

function totalSessionAssessment($student,$exam) {
	global $server;
	$sql=$query="SELECT SUM(e.assessment_1 + e.assessment_2 + e.assessment_3 + e.assessment_4 + e.assessment_5) as ctotal_mark FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id  WHERE e.student_id = '$student' AND e.session_id = '$exam' AND ex.is_cumulative = 1 ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$value = $row['ctotal_mark'];
	return $value;
}

function totalSessionExam($student,$exam) {
	global $server;
	$sql=$query="SELECT SUM(e.exam_score) as ctotal_mark FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.student_id = '$student' AND e.session_id = '$exam' AND ex.is_cumulative = 1 ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$value = $row['ctotal_mark'];
	return $value;
}

function totalSessionSubAssessment($student,$exam,$subject) {
	global $server;
	$sql=$query="SELECT SUM(e.assessment_1 + e.assessment_2 + e.assessment_3 + e.assessment_4 + e.assessment_5) as ctotal_mark FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id  WHERE e.student_id = '$student' AND e.subject_id = '$subject' AND e.session_id = '$exam' AND ex.is_cumulative = 1 ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$value = $row['ctotal_mark'];
	return $value;
}

function totalSessionSubExam($student,$exam,$subject) {
	global $server;
	$sql=$query="SELECT SUM(e.exam_score) as ctotal_mark FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.student_id = '$student' AND e.subject_id = '$subject' AND e.session_id = '$exam' AND ex.is_cumulative = 1 ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$value = $row['ctotal_mark'];
	return $value;
}

function sessionPercentage($student,$exam) {
	global $server;
	$sql=$query="SELECT COUNT(e.subject_id) AS count FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.session_id = '$exam' AND e.student_id = '$student' AND ex.is_cumulative = 1 ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$total = $row['count'];
	$score = totalSessionAssessment($student,$exam)+totalSessionExam($student,$exam);
	$value = ($score/$total);
	return $value;
}
function sessionSubPercentage($student,$exam,$subject) {
	global $server;
	$sql=$query="SELECT COUNT(e.subject_id) AS count FROM exam_student_score e JOIN exams ex ON e.exam_id = ex.id WHERE e.session_id = '$exam' AND e.student_id = '$student' AND e.subject_id = '$subject' AND ex.is_cumulative = 1 ";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$total = $row['count'];
	$score = totalSessionSubAssessment($student,$exam,$subject)+totalSessionSubExam($student,$exam,$subject);
	$value = ($score/$total);
	return $value;
}

// ----------------------- End of exam CA stuffs ------------------------------- //

function lastExam($id) {
	global $server;
	$sql=$query="SELECT * FROM exam_student_score WHERE student_id = '$id' ORDER BY id DESC LIMIT 1";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$term_id = $row['exam_id'];
	return $term_id;
}

function studentAdmissionNumber($id) {
	global $server;
	$sql=$query="SELECT * FROM students WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['admission_number'];
	return $value;
}

function colorScore($score) {
	$tag1 = '<blue>'; $tag2 = '</blue>';
		if($score < 40) {
			$tag1 = '<red>'; $tag2 = '</red>';
		}
	return $tag1.$score.$tag2;
}

function bookIssueExist($book_id, $student_id) {
	global $server;
	$sql=$query="SELECT * FROM book_issues WHERE student_id = '$student_id' AND book_id = '$book_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}

function bookExist($title, $sub_title, $author, $isbn, $school) {
	global $server;
	$sql=$query="SELECT * FROM books WHERE school_id = '$school' AND title = '$title' AND sub_title = '$sub_title' AND author = '$author' AND isbn = '$isbn'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}


function feeInUse($fee_id) {
	global $server;
	$sql=$query="SELECT * FROM fee_paid WHERE fee_id = '$fee_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}


function examInUse($fee_id) {
	global $server;
	$sql=$query="SELECT * FROM exam_student_score WHERE exam_id = '$fee_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function examName($id) {
	global $server;
	$sql=$query="SELECT * FROM exams WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	if($id < 1) { $value = 'Undifined';}
	return $value;
}

function subjectClass($id) {
	global $server;
	$sql=$query="SELECT * FROM subject WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['class_id'];
	if($id < 1) { $value = '0';}
	return $value;
}

function courseName($id) {
	global $server;
	$sql=$query="SELECT * FROM e_courses WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	return $value;
}

function scoreExist($school_id, $subject_id, $exam_id, $student_id) {
	global $server;
	$sql=$query="SELECT * FROM exam_student_score WHERE school_id = '$school_id' AND subject_id = '$subject_id' AND exam_id = '$exam_id' AND student_id = '$student_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function examSession($id) {
	global $server;
	$sql=$query="SELECT * FROM exams WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['session_id'];
	if($id < 1) { $value = '0';}
	return $value;
}
function studentName($id) {
	global $server;
	$sql=$query="SELECT * FROM students WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['first_name'].' '.$row['last_name'].' '.getInitial($row['other_name']);
	return $value;
}
function className($id) {
	global $server;
	$sql=$query="SELECT * FROM classes WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	if($id < 1) { $value = 'All Classes';}
	if($id == 999999) { $value = 'Graduated Students';}
	return $value;
}
function sessionName($id) {
	global $server;
	$sql=$query="SELECT * FROM sessions WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	if($id < 1) { $value = 'All Sessions';}
	return $value;
}
function sessionEnd($id) {
	global $server;
	$sql=$query="SELECT * FROM sessions WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['end_date'];
	return $value;
}

function sessionEnded($id) {
	global $server;
	$sql=$query="SELECT * FROM sessions WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['end_date'];
	$date = new DateTime($value);
	$now = new DateTime();

	if($date < $now) {
		return true;
	} else {
	return false;
	}
}


function termName($id) {
	global $server;
	$sql=$query="SELECT * FROM terms WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	if($id < 1) { $value = 'All Terms';}
	return $value;
}
function hostelName($id) {
	global $server;
	$sql=$query="SELECT * FROM hostels WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	return $value;
}
function busName($id) {
	global $server;
	$sql=$query="SELECT * FROM vehicles WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	return $value;
}

function feeName($id) {
	global $server;
	$sql=$query="SELECT * FROM fees WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	return $value;
}

function applicantData($field,$id) {
	global $server;
	$sql=$query="SELECT * FROM applicants WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row[ $field ];
	return $value;
}

function cbtData($field,$id) {
	global $server;
	$sql=$query="SELECT * FROM cbt WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row[ $field ];
	return $value;
}

function totalPaid($fee, $session=0, $term=0, $class=0, $student) {
	global $server;
	$session_add = $term_add = $class_add = "";
	if($session > 0) { $session_add = " AND session_id = '$session'";}
	if($term > 0) { $term_add = " AND term_id = '$term'";}
	$sql=$query="SELECT * FROM fee_paid WHERE fee_id = '$fee' AND student_id = '$student' $session_add $term_add";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['amount'];
	return $value;
}

function feeTotal($id) {
	global $server;
	$sql=$query="SELECT * FROM fees WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['amount'];
	return $value;
}
function statusName($id) {
	global $server;
	$sql=$query="SELECT * FROM student_status WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	return $value;
}

function processTransaction($invoice_id,$gateway) {
	global $server;
	//fetch invoice
	$sql=$query="SELECT * FROM invoices WHERE id = '$invoice_id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$school_id = $row['school_id'];
	$fee_id = $row['fee_id'];
	$student_id = $row['student_id'];
	$session_id = $row['session_id'];
	$term_id = $row['term_id'];
	$amount = $row['amount'];
	$parent_id = $row['parent_id'];
	$date = date('Y-m-d');

		//create new prents
		if(feePaid($student_id,$fee_id,$session_id,$term_id)) {
		} else {
			$description = feeName($fee_id).' Payment by '.studentName($student_id);
			if($amount < feeTotal($fee_id)) {
				$description = feeName($fee_id).' Part-payment by '.studentName($student_id);
			}


			$sql=$query= "INSERT INTO fee_paid (`id`, `school_id`, `fee_id`, `student_id`, `date`, `approved_by`, `session_id`, `term_id`,`amount`,`gateway`)
							VALUES (NULL, '$school_id', '$fee_id', '$student_id', '$date', '0', '$session_id', '$term_id','$amount','$gateway');";
			mysqli_query($server, $query) or die(mysqli_error($server));

			//update invoice
			$sql=$query="UPDATE `invoices` SET `status` =  'Paid' WHERE `id` = '$invoice_id';";
			mysqli_query($server, $query) or die(mysqli_error($server));

			//update transaction record
			$sql=$query="UPDATE `transactions` SET `status` =  'Completed' WHERE `invoice_id` = '$invoice_id';";
			mysqli_query($server, $query) or die(mysqli_error($server));
		}
}

function monthExpense($year, $school_id) {
		global $server;
		$sql=$query= "SELECT sum(amount) as value FROM transactions WHERE direction = 'OUT' AND status = 'Completed' AND date LIKE '%$year%' AND school_id = '$school_id'";
		$result2 = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result2);
		$total = round($row['value']);
		return $total;
}

function monthIncome($year, $school_id) {
		global $server;
		$sql=$query= "SELECT sum(amount) as value FROM transactions WHERE direction = 'IN' AND status = 'Completed' AND date LIKE '%$year%' AND school_id = '$school_id'";
		$result2 = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result2);
		$total = round($row['value']);
		return $total;
}

function yearAdmission($year, $school_id) {
		global $server;
		$sql=$query= "SELECT count(id) as value FROM students WHERE year LIKE '%$year%' AND school_id = '$school_id'";
		$result2 = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result2);
		$total = round($row['value']);
		return $total;
}

function getParent($id) {
	global $server;
	$sql=$query="SELECT * FROM student_parent WHERE student_id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['parent_id'];
	return $value;
}

function getPaidList($school,$fee,$class,$session,$term) {
	global $server;
	$sql=$query= "select fp.student_id FROM fee_paid fp JOIN fees f ON f.id = fp.fee_id WHERE fp.school_id = '$school' AND fp.fee_id = '$fee' AND f.class_id = '$class' AND fp.session_id = '$session' AND fp.term_id = '$term'";
 	$resultP = mysqli_query($server, $query) or die(mysqli_error($server));
	$numP = mysqli_num_rows($resultP);

	$MailtoDelimiter = ",";
		$rsEmailList = mysqli_query($server, $query);
		$sEmailLink = '';
		while (list ($email) = mysqli_fetch_row($rsEmailList))
		{
		    $sEmail = $email;
		    if ($sEmail)
		    {
		        if ($sEmailLink) // Don't put delimiter before first email
		            $sEmailLink .= $MailtoDelimiter;

		        // Add email only if email is not already in string
		        if (!stristr($sEmailLink, $sEmail))
		            $sEmailLink .= $sEmail;
		    }
		}

	 return $sEmailLink;
}

function getClass($student,$session) {
	global $server;
	$sql=$query="SELECT * FROM student_class WHERE student_id = '$student' AND session_id = '$session'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['class_id'];
	return $value;
}
function subjectName($id) {
	global $server;
	$sql=$query="SELECT * FROM subject WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['title'];
	return $value;
}
function teacherName($id) {
	global $server;
	$sql=$query="SELECT * FROM teachers WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['first_name'].' '. $row['last_name'];
	return $value;
}
function staffName($id) {
	global $server;
	$sql=$query="SELECT * FROM teachers WHERE id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['first_name'].' '. $row['last_name'];
	return $value;
}

function examTerm($exam) {
	global $server;
	$sql=$query="SELECT * FROM exams WHERE id = '$exam'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$term_id = $row['term_id'];
	return $term_id;
}
function getTeacherClass($id) {
	global $server;
	$sql=$query="SELECT * FROM teacher_class WHERE teacher_id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['class_id'];
	return $value;
}
function getClassTeacher($id) {
	global $server;
	$sql=$query="SELECT * FROM teacher_class WHERE class_id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['teacher_id'];
	if(empty($value)) {
		$value = 'Not assigned';
	}
	return $value;
}
function getTeacherSubject($id) {
	global $server;
	$sql=$query="SELECT * FROM teacher_subject WHERE teacher_id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['subject_id'];
	return $value;

}
function getSubjectTeacher($id) {
	global $server;
	$sql=$query="SELECT * FROM teacher_subject WHERE subject_id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['teacher_id'];
	return $value;
}
function getHostel($id) {
	global $server;
	$sql=$query="SELECT * FROM student_hostel WHERE student_id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['hostel_id'];
	return $value;
}
function getBus($id) {
	global $server;
	$sql=$query="SELECT * FROM student_bus WHERE student_id = '$id'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$value = $row['bus_id'];
	return $value;
}

//sms functions
function correctCommas($csv) {
	$inpt= array("\r","\n",' ',';',':','"','.',"'",'`','\t','(',')','<','>','{','}','#',"\r\n",'-','_','?','+');
	$oupt= array(',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',',');
	$csv = str_replace($inpt,$oupt,$csv);
	while(strripos($csv,',,') !== false){
		$csv = str_replace(',,',',',$csv);
	}
	while(strripos($csv,'\r') !== false){
		$csv = str_replace('\r',',',$csv);
	}
	while(strripos($csv,'\n') !== false){
		$csv = str_replace('\n',',',$csv);
	}
	$csv = str_replace($inpt,$oupt,$csv);
	return $csv;
}

function alterPhone($gsm,$code) {
	$array = is_array($gsm);
	$gsm = ($array) ? $gsm : explode(",",$gsm);
	$homeCountry = $code;
	$outArray = array();
	foreach($gsm as $item)
	{
		if(!empty($item)){
			$item1 = (string)$item;
			$q = substr($item1,0,1);
			$w = substr($item1,0,3);
			$item1 = (substr($item1,0,1) == "+") ? substr($item1,1) : $item1;
			$item1 = (substr($item1,0,3) == "009") ? $homeCountry.substr($item1,3): $item1;
			$item1 = (substr($item1,0,1) == "0") ? $homeCountry.substr($item1,1): $item1;
			$item1 = (substr($item1,0,strlen($homeCountry)) == $homeCountry) ? $homeCountry.substr($item1,strlen($homeCountry)): $item1;
			$outArray[] = $item1;
		}
	}
	return ($array) ? $outArray : implode(",",$outArray);
}
function removeDuplicate($myArray) {
	$array = is_array($myArray);
	$myArray = ($array) ? $myArray: explode(",",$myArray);
	$myArray = array_flip(array_flip(array_reverse($myArray,true)));
	return ($array) ? $myArray : implode(',',$myArray);
}
function mceil($number) {
	$array = explode(".",$number);
	$deci = ((int)$array[1] > 0) ? 1 : 0;
	return (int)$array[0] + $deci;
}

function sendMessage($senderID,$recipientList,$textMessage) {
		$recipientList = $nn = preg_replace("/[^0-9+,]/", "", $recipientList );
		$recipientList = implode(',',array_unique(explode(',', $recipientList)));
		$senderID2 = $senderID;
		$textMessage2 = $textMessage;
		$textMessage = urlencode($textMessage);
		$senderID = urlencode($senderID);
		$activeGateway = 1;
		$SMS_username = trim(getSetting('SMS_username'));
		$SMS_password = trim(getSetting('SMS_password'));
		$senderID = urlencode(getSetting('SMS_sender'));
		$sendURL = "http://smskit.net/SMSC/smsAPI?sendsms&apikey=$SMS_username&apitoken=$SMS_password&type=sms&from=$senderID&text=$textMessage&to=$recipientList";
		$successWord = "queued";
        $url = $sendURL;

		$fields = array(
		   'do'=>'1'
		);

		$postvars='';
		$sep='';
		foreach($fields as $key=>$value) {
		   $postvars.= $sep.urlencode($key).'='.urlencode($value);
		   $sep='&';
		}

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
       curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	curl_close($ch);

	if(!$response) {
		  $return = 'Connection to Gateway Failed. Please try again later';  //. curl_error($ch)
	  } else {
           $return = 'Message Sending Failed. Unknown Gateway Error';
              if((stripos($response,$successWord) !== false)) {
                   $return = 'Message Sent Successfully';
               } else {
                   $return = 'Message sending was interrupted. '.$response;
               }
	  }

	return $return;
}


class Currency {
	function Symbul($id) {
		global $server;
		$id = getUser(); $id2 = getAdmin();
		$sql=$query="SELECT * FROM users WHERE id = '$id' OR id = '$id2'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$id = $row['school_id'];

		$sql=$query="SELECT * FROM settings WHERE school_id = '$id' AND field = 'currency_id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$client_currency = $row['value'];

		$sql=$query="SELECT * FROM currency WHERE id = '$client_currency'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$currency_name = $row['title'];
		$currency_symbul = $row['symbul'];
		$name = $currency_symbul;

		return $name;
	}

	function Rate($id) {
		global $server;
		$id = getUser(); $id2 = getAdmin();
		$sql=$query="SELECT * FROM users WHERE id = '$id' OR id = '$id2'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$id = $row['school_id'];

		$sql=$query="SELECT * FROM settings WHERE school_id = '$id' AND field = 'currency_id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$client_currency = $row['value'];

		$sql=$query="SELECT * FROM currency WHERE id = '$client_currency'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$currency_rate = $row['rate'];
		$name = $currency_rate;

		return $name;
	}
	function Code($id) {
		global $server;
		$id = getUser(); $id2 = getAdmin();
		$sql=$query="SELECT * FROM users WHERE id = '$id' OR id = '$id2'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$id = $row['school_id'];

		$sql=$query="SELECT * FROM settings WHERE school_id = '$id' AND field = 'currency_id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$client_currency = $row['value'];

		$sql=$query="SELECT * FROM currency WHERE id = '$client_currency'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$currency_rate = $row['code'];
		$name = $currency_rate;
		return $name;
	}
}
function currencyExchangeRate($code) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE id = '$id' OR id = '$id2'";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$row = mysqli_fetch_assoc($result);
	$school_id = $row['school_id'];
	$sql=$query="SELECT * FROM currency WHERE (id = '$code' OR code = '$code') AND school_id = '$school_id'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	return $row['rate'];
}
class DefaultCurrency {
	function Symbul($id) {
		global $server;
		$id = getUser(); $id2 = getAdmin();
		$sql=$query="SELECT * FROM users WHERE id = '$id' OR id = '$id2'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$id = $row['school_id'];

		$sql=$query="SELECT * FROM settings WHERE school_id = '$id' AND field = 'currency_id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$client_currency = $row['value'];

		$sql=$query="SELECT * FROM currency WHERE id = '$client_currency'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$currency_name = $row['title'];
		$currency_symbul = $row['symbul'];
		$name = $currency_symbul;

		return $name;
	}

	function Rate($id) {
		global $server;
		$id = getUser(); $id2 = getAdmin();
		$sql=$query="SELECT * FROM users WHERE id = '$id' OR id = '$id2'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$id = $row['school_id'];

		$sql=$query="SELECT * FROM settings WHERE school_id = '$id' AND field = 'currency_id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$client_currency = $row['value'];

		$sql=$query="SELECT * FROM currency WHERE id = '$client_currency'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$currency_rate = $row['rate'];
		$name = $currency_rate;

		return $name;
	}
	function Code($id) {
		global $server;
		$id = getUser(); $id2 = getAdmin();

		$sql=$query="SELECT * FROM users WHERE id = '$id' OR id = '$id2'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$id = $row['school_id'];

		$sql=$query="SELECT * FROM settings WHERE school_id = '$id' AND field = 'currency_id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$client_currency = $row['value'];

		$sql=$query="SELECT * FROM currency WHERE id = '$client_currency'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$currency_rate = $row['code'];
		$name = $currency_rate;
		return $name;
	}
}

function isCustomGateway($id) {
	global $server;
		$sql=$query="SELECT * FROM paymentgateways WHERE id = '$id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$custom = $row['custom'];
	if($custom >0) {
	return true;
	} else {
		return false;
	}
}
function gatewayName($id) {
	global $server;
		$sql=$query="SELECT * FROM paymentgateways WHERE id = '$id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$name = $row['title'];
	if($id < 1) {
		$name	= 'Others';
	}
		return $name;
}


function isAdminUser($id) {
	global $server;
	$sql=$query= "SELECT * FROM users WHERE id = '$id'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}

function attendanceTaken($school, $class,$date) {
	global $server;
	$sql=$query= "SELECT * FROM student_attendance WHERE school_id = '$school' AND  class_id = '$class' AND  date = '$date'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}

function isOwner($id) {
	global $server;
	$sql=$query= "SELECT * FROM users WHERE id = '$id' AND is_supper = 1";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function schoolusernameExist($name) {
	global $server;
	$sql=$query= "SELECT * FROM schools WHERE username = '$name'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function classHasStudent($id, $session) {
	global $server;
	$sql=$query= "SELECT * FROM student_class WHERE class_id = '$id' AND session_id = '$session'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}

function classsOfferCourse($id,$course) {
	global $server;
	$query="SELECT * FROM class_course WHERE class_id = '$id' AND course_id = '$course'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}

function hostelHasStudent($id) {
	global $server;
	$sql=$query= "SELECT * FROM student_hostel WHERE hostel_id = '$id'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function busHasStudent($id) {
	global $server;
	$sql=$query= "SELECT * FROM student_bus WHERE hostel_id = '$id'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}

function countPresent($date, $class) {
	global $server;
	$sql=$query= "SELECT * FROM student_attendance WHERE date = '$date' AND class_id = '$class' AND attendance = 'Present'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
return $num;
}

function countCourseLeason($id) {
	global $server;
	$sql=$query= "SELECT * FROM e_courses_contents WHERE course_id = '$id'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	return $num;
}

function countCBTQuestions($id) {
	global $server;
	$sql=$query= "SELECT * FROM cbt_questions WHERE cbt_id = '$id'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	return $num;
}
function getQuestions($id) {
	global $server;
	$sql=$query= "SELECT * FROM cbt_questions WHERE id = '$id'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	return $row['question'];
}
function getAnswerValue($id) {
	global $server;
	$query= "SELECT * FROM cbt_choices WHERE id = '$id'";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	return $row['answer'];
}

function noticeRead($id, $user) {
	global $server;
	$sql=$query= "SELECT * FROM notice_read WHERE notice_id = '$id' AND user_id = '$user'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);

if($num > 0) { return true; } else { return false; }
}

function countNewNotice($userID, $class, $role, $school) {

	global $server;

	$sql=$query= "SELECT * FROM notice WHERE school_id = '$school' AND (role_id = '$role' OR role_id = '0' OR user_id = '$userID' OR class_id = '0' OR class_id = '$class' OR user_id = '0') ORDER BY id DESC";
	$result = mysqli_query($server, $query) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);
	$num2 = 0;
	while($row = mysqli_fetch_assoc($result)){
		if(!noticeRead($id = $row['id'],$userID))
		$num2 += 1;
	}
return $num2;
}

function countAbsent($date, $class) {
	global $server;
	$sql=$query= "SELECT * FROM student_attendance WHERE date = '$date' AND class_id = '$class' AND attendance != 'Present'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	return $num;
}

function getCountryList($select) {
	global $server;
  $sql=$query="SELECT * FROM countries ORDER BY title DESC";
  $result = mysqli_query($server, $query);
  $num = mysqli_num_rows($result);

  while($row = mysqli_fetch_assoc($result)){
      $g_id = $row['id'];
      $title0 = $row['title'];
  		?>
     <option value="<?php echo $g_id; ?>" <?php if($select == $g_id) {echo 'selected';} ?>><?php echo $title0; ?></option>
			<?php
	}
}

function countryName($id) {
	global $server;
		$sql=$query="SELECT * FROM countries WHERE id = '$id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$customer = $row['title'];
	return $customer;
}

function customFieldValue($id,$user_id) {
	global $server;
		$sql=$query="SELECT * FROM  custom_values WHERE field_id = '$id' AND user_id = '$user_id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$customer = $row['value'];
	return $customer;
}

function usernameExist($name) {
	global $server;
	$sql=$query= "SELECT * FROM users WHERE username = '$name'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function countBookCategory($id) {
	global $server;
	$sql=$query= "SELECT * FROM books WHERE id = '$id'";
	$result = mysqli_query($server, $query);
	return mysqli_num_rows($result);
}
function bookIssued($id) {
	global $server;
	$sql=$query= "SELECT * FROM book_issues WHERE book_id = '$id' AND status = '0'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	return $num;
}

function importQuestions($files,$cbt_id,$school_id) {
	global $server;
	if($school_id<1)$school_id=$_SESSION['school_id'];
	$recipientList = '';
	$fileType = strtolower(end(explode(".", $files)));
	require BASEPATH."Excel/PHPExcel.php";
	$tmpfname = "media/uploads/".$files;
	$excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
	$excelObj = $excelReader->load($tmpfname);
	$worksheet = $excelObj->getSheet(0);
	$lastRow = $worksheet->getHighestRow();
	for ($Excelrow = 2; $Excelrow <= $lastRow; $Excelrow++) {
		 $answe = array();
		 $question = trim(mysqli_real_escape_string($server, $worksheet->getCell('A'.$Excelrow)->getValue()));
		 $answe[] = $answe1 = trim(mysqli_real_escape_string($server, $worksheet->getCell('B'.$Excelrow)->getValue()));
		 $answe[] = $answe2 =  trim(mysqli_real_escape_string($server, $worksheet->getCell('C'.$Excelrow)->getValue()));
		 $answe[] = trim(mysqli_real_escape_string($server, $worksheet->getCell('D'.$Excelrow)->getValue()));
		 $answe[] = trim(mysqli_real_escape_string($server, $worksheet->getCell('E'.$Excelrow)->getValue()));
		 $answe[] = trim(mysqli_real_escape_string($server, $worksheet->getCell('F'.$Excelrow)->getValue()));
		 $correct_answer = trim(mysqli_real_escape_string($server, $worksheet->getCell('G'.$Excelrow)->getValue()));
		 
		 if(!empty($correct_answer) && !empty($question) && !empty($answe1) && !empty($answe2)) {
				$datetime = date('Y-m-d H:i:s');
				mysqli_query($server,"DELETE FROM cbt_questions WHERE question = '$question' AND cbt_id = '$cbt_id'");
				$query ="INSERT INTO cbt_questions (`question`, `cbt_id`, `correct_answer`, `course_id`, `school_id`) VALUES ('$question', '$cbt_id', '$correct_answer', '0', '$school_id');";
				mysqli_query($server, $query) or die(mysqli_error($server));
				$question_id = mysqli_insert_id($server);
				mysqli_query($server,"DELETE FROM cbt_choices WHERE question_id = '$question_id'");
				foreach($answe as $answers) {
					$answers = filterinp($answers);
					if(!empty($answers)) {
						mysqli_query($server,"INSERT INTO cbt_choices (`question_id`, `answer`) VALUES ('$question_id', '$answers');") or die(mysqli_error($server));
					}
				}
		 }
	}
}

function importCustomers($files,$class,$school_id) {
	global $server;
	if($school_id<1)$school_id=$_SESSION['school_id'];
	$recipientList = '';
	$fileType = strtolower(end(explode(".", $files)));
	require BASEPATH."Excel/PHPExcel.php";
	$tmpfname = "media/uploads/".$files;
	$excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
	$excelObj = $excelReader->load($tmpfname);
	$worksheet = $excelObj->getSheet(0);
	$lastRow = $worksheet->getHighestRow();
	for ($Excelrow = 2; $Excelrow <= $lastRow; $Excelrow++) {
		 $first_name = trim(mysqli_real_escape_string($server, $worksheet->getCell('A'.$Excelrow)->getValue()));
		 $last_name = trim(mysqli_real_escape_string($server, $worksheet->getCell('B'.$Excelrow)->getValue()));
		 $other_name = trim(mysqli_real_escape_string($server, $worksheet->getCell('C'.$Excelrow)->getValue()));
		 $sex = ucfirst(trim(mysqli_real_escape_string($server, $worksheet->getCell('D'.$Excelrow)->getValue())));
		 $date_of_birth = trim(mysqli_real_escape_string($server, $worksheet->getCell('E'.$Excelrow)->getValue()));
		 if(!empty($date_of_birth)) {
			 $date_of_birth = date('Y-m-d',strtotime($date_of_birth));
		 } else {
			$date_of_birth = '';
		 }
		 $address = trim(mysqli_real_escape_string($server, $worksheet->getCell('F'.$Excelrow)->getValue()));
		 $city = trim(mysqli_real_escape_string($server, $worksheet->getCell('G'.$Excelrow)->getValue()));
		 $state = trim(mysqli_real_escape_string($server, $worksheet->getCell('H'.$Excelrow)->getValue()));
		 $state_origin = trim(mysqli_real_escape_string($server, $worksheet->getCell('I'.$Excelrow)->getValue()));
		 $admission_number = trim(mysqli_real_escape_string($server, $worksheet->getCell('J'.$Excelrow)->getValue()));
		 $year = trim(mysqli_real_escape_string($server, $worksheet->getCell('K'.$Excelrow)->getValue()));

		 $father_name = trim(mysqli_real_escape_string($server, $worksheet->getCell('L'.$Excelrow)->getValue()));
		 $mother_name = trim(mysqli_real_escape_string($server, $worksheet->getCell('M'.$Excelrow)->getValue()));
		 $email = trim(mysqli_real_escape_string($server, $worksheet->getCell('N'.$Excelrow)->getValue()));
		 $phone = trim(mysqli_real_escape_string($server, $worksheet->getCell('O'.$Excelrow)->getValue()));

		 $nationality = $country = getSetting('country_id');

		 $current_session = getSetting('current_session');
		 $current_term = getSetting('current_term');

		 if(!empty($last_name) && !empty($first_name) && !studentExist($first_name, $last_name, $other_name, $school_id)) {
			if(empty($admission_number)) {
				$admission_number = getAdmissionNumber($school_id,$year);
			}

			$sql=$query= "INSERT INTO students (`id`, `school_id`, `first_name`, `last_name`, `other_name`, `sex`, `date_of_birth`, `address`, `city`, `local_council`, `state`, `country`, `portal_access`, `nationality`, `state_origin`, `admission_number`, `bload_group`, `photo`, `status`, `hostel_id`, `year`,`phone`, `email`) VALUES (NULL, '$school_id', '$first_name', '$last_name', '$other_name', '$sex', '$date_of_birth', '$address', '$city', '', '$state', '$country', '1', '$nationality', '$state_origin', '$admission_number', '', '', '1', '', '$year','', '');";
			mysqli_query($server, $query);
			//get inserted id
			$student = mysqli_insert_id($server);
			//create login account
			$name = $first_name.' '.$last_name;
			$username = rand(19999999, 99999999);
			$password = rand(19999999, 99999999);
			$_SESSION['student_password'] = $password;
			$salt = genRandomPassword(32);
			$crypt = getCryptedPassword($password, $salt);
			$password2 = $crypt.':'.$salt;
			if(usernameExist($username)) {
				$username = rand(19999999, 99999999)+rand(100, 999);
			}
			$username = 'student'.$username.$student;
			$query="INSERT INTO users (`id`, `username`, `password`, `name`, `email`, `role_id`, `profile_id`, `school_id`, `phone`) VALUES (NULL, '$username', '$password2', '$name', '', '6', '$student', '$school_id', '');";
			mysqli_query($server, $query);

			$query="UPDATE students SET `pln_pa` = '$password' WHERE id = '$student'";
			mysqli_query($server, $query);

			$query= "INSERT INTO student_class (`id`, `student_id`, `class_id`, `session_id`) VALUES (NULL, '$student', '$class', '$current_session');";
			mysqli_query($server, $query);
	
			if(!empty($father_name)) {
				if(!parentExist($father_name, $mother_name, $school_id)) {
					$authorization_code = rand(1999999999, 9999999999);
					$sql=$query= "INSERT INTO parents (`id`, `school_id`, `father_name`, `mother_name`, `father_occupation`, `mother_occupation`, `father_photo`, `mother_photo`, `address`, `city`, `state`, `country`, `email`, `phone`, `phone2`, `authorization_code`) VALUES (NULL, '$school_id', '$father_name', '$mother_name', '', '', '', '', '$address', '$city', '$state', '$country', '$email', '$phone', '', '$authorization_code');";
					mysqli_query($server, $query);
					//get inserted id
					$parent = mysqli_insert_id($server);
					$name = 'Mr & Mrs '.$father_name;
					$username = rand(19999999, 99999999);
					$password = rand(19999999, 99999999);
					$salt = genRandomPassword(32);
					$crypt = getCryptedPassword($password, $salt);
					$password2 = $crypt.':'.$salt;
					if(usernameExist($username)) {
						$username = rand(19999999, 99999999)+rand(100, 999);
					}
					$username = 'parent'.$username.$parent;
					$query= "INSERT INTO users (`id`, `username`, `password`, `name`, `email`, `role_id`, `profile_id`, `school_id`, `phone`) VALUES (NULL, '$username', '$password2', '$name', '$email', '5', '$parent', '$school_id', '$phone');";
					mysqli_query($server, $query) or die(mysqli_error($server));
	
					$query="UPDATE parents SET `pln_pa` = '$password' WHERE id = '$parent'";
					mysqli_query($server, $query) or die(mysqli_error($server));
				 } else {
					$query= "SELECT * FROM parents WHERE father_name = '$father_name' AND mother_name = '$mother_name' AND school_id = '$school_id'";
					$result3 = mysqli_query($server, $query);
					$row3 = mysqli_fetch_assoc($result3);
					$parent = $row3['id'];
					$query="INSERT INTO student_parent (`id`, `student_id`, `parent_id`) VALUES (NULL, '$student', '$parent');";
					mysqli_query($server, $query);
				 }
				 $query="INSERT INTO student_parent (`id`, `student_id`, `parent_id`) VALUES (NULL, '$student', '$parent')";
				mysqli_query($server, $query);
			}
		 }
		 $recipientList .= $first_name;
	}
	if(empty($recipientList)) return 'No valid students data found';
	return 	'ok';
}


function studentExist($name, $name2, $name3, $school) {
	global $server;
	$sql=$query= "SELECT * FROM students WHERE school_id = '$school' AND first_name = '$name' AND last_name = '$name2' AND other_name = '$name3'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function parentExist($name, $name2, $school) {
	global $server;
	$sql=$query= "SELECT * FROM parents WHERE school_id = '$school' AND father_name = '$name' AND mother_name = '$name2'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function guardianExist($name, $name2, $school) {
	global $server;
	$sql=$query= "SELECT * FROM guardians WHERE school_id = '$school' AND first_name = '$name' AND last_name = '$name2'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function teacherExist($name, $name2, $school) {
	global $server;
	$sql=$query= "SELECT * FROM teachers WHERE school_id = '$school' AND first_name = '$name' AND last_name = '$name2'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function staffExist($name, $name2, $school) {
	global $server;
	$sql=$query= "SELECT * FROM staffs WHERE school_id = '$school' AND first_name = '$name' AND last_name = '$name2'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function emailExist($name) {
	global $server;
	$sql=$query="SELECT * FROM users WHERE email = '$name'";
	$result = mysqli_query($server, $query);
	$num = mysqli_num_rows($result);
	if($num >0) {
		return true;
	} else {
		return false;
	}
}
function getInsertedID($table) {
	global $server;
		$sql=$query="SELECT * FROM $table ORDER BY id DESC LIMIT 1";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$id = $row['id'];
	return $id;
}
function transactionOwner($id) {
	global $server;
		$sql=$query="SELECT * FROM transactions WHERE id = '$id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$customer = $row['customer'];
	return $customer;
}

function userFullName($id) {
	global $server;
		$sql=$query="SELECT * FROM users WHERE id = '$id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$customer = $row['name'];
	return $customer;
}

function roleName($id) {
	global $server;
		$sql=$query="SELECT * FROM user_roles WHERE id = '$id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$customer = $row['title'];
	return $customer;
}
function payInvoice($id,$gateway,$userID) {

		global $server;

		$sql=$query="SELECT * FROM invoices WHERE id = '$id'";
		$result = mysqli_query($server, $query);
		$row = mysqli_fetch_assoc($result);
		$amount = $row['amount'];
		$school_id = $row['school_id'];
		$fee_id = $row['fee_id'];

		$student_id = $row['student_id'];
		$date = $row['date'];
		$session_id = $row['session_id'];
		$term_id = $row['term_id'];

			$sql=$query="INSERT INTO fee_paid (`id`, `school_id`, `fee_id`, `student_id`, `date`, `approved_by`, `session_id`, `term_id`,`amount`,`gateway`)
			VALUES (NULL, '$school_id', '$fee_id', '$student_id', '$date', '$userID', '$session_id', '$term_id','$amount','$gateway');";
			mysqli_query($server, $query) or die(mysqli_error($server));

	//Update invoice record
		$sql=$query="UPDATE `incoices` SET
					`status` =  'Paid'
					WHERE  `id` = '$id';";
		mysqli_query($server, $query);

	//Update transaction record
		$sql=$query="UPDATE `transactions` SET
			`status` =  'Completed',
			`approvedBy` =  '$gateway'
			WHERE  `invoice_id` = '$id';";

		mysqli_query($server, $query);

}

function increaseDate($date, $by) {
	$seconds = $by*60*60*24;
	$date = strtotime($date)+$seconds;
	return date('Y-m-d', $date);
}
function reduceDate($date, $by) {
	$seconds = $by*60*60*24;
	$date = strtotime($date)-$seconds;
	return date('Y-m-d', $date);
}

# Round time down to the nearest resolution
function round_t_down($t, $resolution, $am7)
{
        return (int)$t - (int)abs(((int)$t-(int)$am7)
				  % $resolution);
}

# Round time up to the nearest resolution
function round_t_up($t, $resolution, $am7)
{
	if (($t-$am7) % $resolution != 0)
	{
		return $t + $resolution - abs(((int)$t-(int)
					       $am7) % $resolution);
	}
	else
	{
		return $t;
	}
}


function backup_tables($host,$user,$pass,$name,$type,$tables = '*'){
	global $server;
	$return = '';
	//get all of the tables
	if($tables == '*')	{
		$tables = array();
		$result = mysqli_query($server,'SHOW TABLES');
		while($row = mysqli_fetch_row($result))	{
			$tables[] = $row[0];
		}
	}else{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}

	//cycle through
	foreach($tables as $table)	{
		$result = mysqli_query($server,'SELECT * FROM '.$table);
		$num_fields = mysqli_num_fields($result);
		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysqli_fetch_row(mysqli_query($server,'SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";

		//	for ($i = 0; $i < $num_fields; $i++)	{
			while($row = mysqli_fetch_row($result))	{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++)	{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = @preg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
//		}
		$return.="\n\n\n";
	}

	//save file
	$database_backup = 'backups/soa_db_backup'.time().'.sql';
	$handle = fopen($database_backup,'w+');
	fwrite($handle,$return);
	fclose($handle);

	//insert to record
	$filename = 'soa_db_backup'.time().'.sql';
	$date = date('Y-m-d H:i:s');

	mysqli_query($server,"INSERT INTO backups (`id`, `date`, `type`, `file`)VALUES (NULL, '$date', '$type', '$filename');") or die(mysqli_error($server));
	return $filename;
}

function restore_tables($filename,$host,$username,$password,$database){
	global $server;
	$templine = '';
	// Read in entire file
	$lines = file($filename);
	// Loop through each line
	foreach ($lines as $line){
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;
		$templine .= $line;
		if (substr(trim($line), -1, 1) == ';')	{
			mysqli_query($server, $templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysqli_error($server) . '<br /><br />');
			$templine = '';
		}
	}
}
if(DEMO_MODE) {
	if(isset($_REQUEST['delete']) || isset($_REQUEST['edit']) || isset($_REQUEST['update'])) { ?>
		<script>
		 alert('Sorry but you can not Delete or Edit a record in Demo Mode\nPlease consider creating a fresh record if you are trying to update an existing record');
		 window.location = "admin.php";
		</script>
	<?php
	}
}

function showSelectSchool() {
	global $server;
	?>
           <div style="overflow-x: hidden; width: 99.99%; height: 99.99%; z-index: 1000; position: fixed; top: 0; left: 0; background: rgba(0,0,0,0.7);" id="school_switcher">
        	  <div id="add-new" style="max-width: 600px;">
           		 <div id="add-new-head">Choose a School to Manage</div>
             		<div class="inside" style="background-color: #fff;">
                    	<div id="mess" style="position: relative; top: 0;">
                    	<?php echo '<div id="messageBox" style="max-width: 1000px;" class="message blue">Hey!<br>We noticed you\'ve got more than one school on your SOA so you need to choose one to manage before you continue.<br><br>You can always switch to a different schools at any time from the "<strong>Manage Schools</strong>" link on the main menu.</div><br/>'; ?>
                        </div>
                        <p style="text-align: center">
                         <select name="switch_school" id="switch_school" onchange="if (this.value) window.location.href=this.value" >
                         	<option disabled selected value="">Select a school to manage</option>
                            <?php
								$sql=$query="SELECT * FROM schools ORDER BY name ASC";
								$resultC = mysqli_query($server, $query);
								$numC = mysqli_num_rows($resultC);

								while($row = mysqli_fetch_assoc($resultC)){
										$id = $row['id'];
										$title = $row['name'];
										?>
                    <option value="admin.php?switch_school=<?=$id?>"><?=$title?></option>
                    <?php
								} ?>
                         </select>
                         </p>
                         <p style="text-align: center">
                         <a href="admin/schools"><button type="button" class="submit"><i class="fa fa-list"></i> Browse All Schools</button></a>
                         </p>
                	</div>
                  </div>
             </div>
          </div>
    <?php
}
//load settings
if(isset($_REQUEST['switch_school']) && $_REQUEST['switch_school'] > 0) {
	 if(isOwner(getUser())) {
		$_SESSION['mana_school_id'] = $_REQUEST['switch_school'];
		$_SESSION['school_id'] = $_REQUEST['switch_school'];
		header('location: admindashboard');
		exit;
	 }
}

#====  HTML Loginn Stuffs
#	   You can customize this if you want
#====
function setsessions_var($user_id) {
	global $server;
	$_SESSION['localsession_id'] = $localsession_id = genRandomPassword(32);
	mysqli_query($server, "UPDATE  `users` SET  `localsession_id` =  '$localsession_id' WHERE `id` ='$user_id'");	
}

function adminLoginForm() {
	global $server;
	 $siteName = getSetting('name');
	 $siteLogo = getSetting('logo');
	 $siteName = 'SOA';
	 $siteLogo = 'media/images/logo.png';
	 if(!empty($message)) { showMessage($message, $class); } 		 
//Processors New Password
	if(isset($_POST['userEmail'])) {
		$email2 = mysqli_real_escape_string($server,$_POST['userEmail']);	
		$query = "SELECT * FROM users WHERE email = '$email2'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$num = mysqli_num_rows($result);	
		if($num < 1) {
			 showMessage("Oops! This email is not associated with any administrator. Please check your email", 'red');
		} else {
		$password = rand(199999, 999999);
		if(DEMO_MODE) {
			$password = 12345678;
		}
		$salt = genRandomPassword(32);
		$crypt = getCryptedPassword($password, $salt);
		$password2 = $crypt.':'.$salt;
		mysqli_query($server, "UPDATE  `users` SET `password` =  '$password2'	WHERE  `email` = '$email2';");
								
		$query="SELECT * FROM users WHERE email = '$email2'"; 
		$result = mysqli_query($server, $query) or die(mysqli_error($server));  
		$row = mysqli_fetch_assoc($result); 
		$username = $row['username'];	
		$name = $row['name'];
		$email = $row['email'];
	
		$newPasswordSMS = 'Dear [CUSTOMER NAME].<br><br> Your '.getSetting('name').' login password has been recovered. Your new login details are: <br><b>Username</b>: [USERNAME]<br> <b>Password</b>: [PASSWORD].<br><br>Please contact your school admin at once if you have not requested for a password reset. <hr>Note that this is an auto generated message from SOA school management software. Please discard if received in error!';
			
		if(!empty($newPasswordSMS)) {
			$mail = str_replace('[USERNAME]', $username, $newPasswordSMS);	
			$mail = str_replace('[PASSWORD]', $password, $newPasswordSMS);
			$mail = str_replace('[CUSTOMER NAME]', $name, $newPasswordSMS);	
			$mail = strtr($newPasswordSMS, array('[PASSWORD]'=>$password,'[CUSTOMER NAME]'=>$name,'[USERNAME]'=>$username));																
			sendEmail(getSetting('email'),getSetting('name'),'Your '.getSetting('name').' Staff Login Details',$email,$mail);		
		}			
			showMessage("A new password has been sent to your email address.", 'green');
		}	
	}
//Process Login
	if (isset($_POST['access_password'])) {
	   $name = mysqli_real_escape_string($server,$_POST['access_login']);
	    $password = mysqli_real_escape_string($server,$_POST['access_password']);
		$query="SELECT * FROM users WHERE (username = '$name') OR (email = '$name')  AND (role_id != 5 AND role_id != 6)"; 
		$result = mysqli_query($server, $query) or die(mysqli_error($server));  
		$row = mysqli_fetch_assoc($result); 
		$num = mysqli_num_rows($result);
		if($num < 1) {
			showMessage("Your login detail is not associated with any administrator. Please check your details", 'red');
		}
		$pwsalt = explode( ":",$row["password"]);	
		$pass2 = $row["password"];
		if(md5($password . $pwsalt[1]) != $pwsalt[0] && md5($password) != $row["password"]) {	
			showMessage("Oops! Your username or password is incorrect.", 'red');
		  }
		  else {
			$query="SELECT * FROM users WHERE (email = '$name') OR (username = '$name')"; 
			$result = mysqli_query($server, $query) or die(mysqli_error($server));  
			$row = mysqli_fetch_assoc($result); 
			$user_id = $row['id'];
			$_SESSION['SOAAdmin']= $user_id;
			$day = date("Y-m-d H:i:s");
			mysqli_query($server, "UPDATE  `users` SET  `last_login` =  '$day' WHERE `id` ='$user_id'");	
			setsessions_var($user_id);
			$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		   header('location: '.$actual_link);
	  }
	}		
	
	 if(isset($_GET['reset'])) {	 
?>
 <div id="login-center">
            <div id="login-head"><i class="fa fa-lock"></i> Reset Password</div>
			<div id="login-form">
            <img src="<?php echo $siteLogo; ?>" alt="<?php echo $siteName; ?>" title="<?php echo $siteName; ?>" />
            <form method="post" action="" name="login">
                <p><input type="email" name="userEmail" placeholder=" Your Email Address"></p>
                <p><button type="submit" id="login-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Reset Password</button></p>
            </form>
            <p><a href="admin.php"><button>Retry Login</button></a></p>
			<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Processing...</div>
        </div>
		
    </div>
    <?php powered_by();?>
<?php } else {		?>
    <div id="login-center"  style="height: auto; min-height: 250px;">
            <div id="login-head"><i class="fa fa-lock"></i> Staff login</div>
			<div id="login-form">
            <img src="<?php echo $siteLogo; ?>" alt="<?php echo $siteName; ?>" title="<?php echo $siteName; ?>" />
            <form method="post" action="" name="login">
                <p><input type="text" name="access_login" onfocus="if(this.value  == 'Your Username') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Your Username'; } " value="Your Username"></p>
                <p><input type="password" name="access_password" onfocus="if(this.value  == 'Password') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Password'; } " value="Password"></p>
                <p><button type="submit" id="login-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Login</button></p>
            </form>
            <p><a href="admin.php?reset" class="link">Forgot Password</a></p>
			<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Authenticating...</div>
        </div>
    </div>
  	<?php powered_by();?>
 <?php   } //end reset or login
}
if(!function_exists('powered_by')) {
function powered_by() {
	global $configapp_version;
	if(strtolower(checkLicense())!='pro'){	?>
		<div style="background:rgba(0,0,0,0.7);position:absolute; right:20px;bottom:10px;text-align:center;padding:10px;font-size: 12px; font-weight:bold;color:#fff;">Powered by SOA v<?=$configapp_version?></div>	
	<?php }
}
}
function studentLoginForm() {
	global $server;
	$siteName = getSetting('name');
	$logo = getSetting('logo');
	$school_id=$_SESSION['school_id'];
	$siteLogo = 'media/uploads/'.getSetting('logo');
	$pin_enabled = getSetting('pin_enabled');
	if(empty($siteName)): $siteName = 'SOA'; endif;
	if(empty($logo)): $siteLogo = 'media/images/logo.png'; endif;
	
	if(!empty($message)) { showMessage($message, $class); } 
	
	if (isset($_POST['access_password'])) {
		$login = $name = mysqli_real_escape_string($server,$_POST['access_login']);
		$password = mysqli_real_escape_string($server,$_POST['access_password']);
		$query="SELECT * FROM users WHERE username = '$name' AND (role_id = 5 OR role_id = 6)"; 
		$result = mysqli_query($server,$query) or die(mysqli_error($server));  
		$row = mysqli_fetch_assoc($result); 
		$num = mysqli_num_rows($result);
		if($num < 1) {
			showMessage("This account does not exist. Please check your details or contact your school management for help.", 'red');
		}
		
		$pwsalt = explode( ":",$row["password"]);	
		$pass2 = $row["password"];
		if(md5($password . $pwsalt[1]) != $pwsalt[0] && md5($password) != $row["password"]) {	
			showMessage("Oops! Your username or password is incorrect.", 'red');
		  }
		  else {
			$query="SELECT * FROM users WHERE username = '$name'"; 
			$result = mysqli_query($server,$query) or die(mysqli_error($server));  
			$row = mysqli_fetch_assoc($result); 
			$user_id = $row['id'];
			$profile_id = $row['profile_id'];
			$role_id = $row['role_id'];
			
			//chech if login is allowed
			if(!canAccessPortal($user_id)) {
				showMessage("Your are not allowed to access this portal. Please contact your school management for assistance.", "yellow");
			} else {	
				// set cookie if password was validated
				$_SESSION['SOAUser'] = $user_id;
				setcookie("SOAUser", $user_id, $timeout, '/'); 
				
				//Set Last Login Date and Time
				$day = date("Y-m-d H:i:s");
				mysqli_query($sever, "UPDATE  `users` SET  `last_login` =  '$day' WHERE `id` ='$user_id'");
				setsessions_var($user_id);
				
				//set last login IP
				$user_ip = getenv('REMOTE_ADDR');	
				mysqli_query($server, "UPDATE  `users` SET  `lastIP` =  '$user_ip' WHERE `id` ='$user_id'");
			   
			   $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			   header('location: '.$actual_link);
			}
		  }
		
		} elseif (isset($_POST['pin_serial'])) {
		  $login = $name = mysqli_real_escape_string($server,$_POST['pin_serial']);
		  $password = mysqli_real_escape_string($server,$_POST['pin']);
			$query="SELECT * FROM pin WHERE serial = '$name' AND applicant = '0'"; 
			$result = mysqli_query($server, $query) or die(mysqli_error($server));  
			$row = mysqli_fetch_assoc($result); 
			$num = mysqli_num_rows($result);
			if($num < 1) {
				showMessage("Your PIN is invalid. Please check your Serial No./PIN or contact your school management for help.", 'red');
			}
		
		$pwsalt = explode( ":",$row["pin"]);	
		$pass2 = $row["pin"];
		if(md5($password . $pwsalt[1]) != $pwsalt[0] && md5($password) != $row["pin"]) {	
			showMessage("Oops! Your Serial No. & PIN does not match", 'red');
		}	else {
			$query="SELECT * FROM pin WHERE serial = '$name'"; 
			$result = mysqli_query($server, $query) or die(mysqli_error($server));  
			$row = mysqli_fetch_assoc($result); 
			$parent_id = $row['parent_id'];
			$student_id = $row['student_id'];
			$term_id = $row['term_id'];
			$session_id = $row['session_id'];
			$validity_type = $row['validity_type'];
			$pin_id = $row['id'];
			$uses = $row['uses'];
			$used = $row['used'];
			
			//set profile id
			$user_id = $student_id;
			
			//check if PIN is still valid for current sessuion or term
			if($validity_type == 'session') {
				$current_session = getSetting('current_session');
				if($session_id != $current_session) {
					showMessage("Oops! Your PIN has expired. Please contact the school management to get a new one", 'red');
				}
			} else {
				$current_session = getSetting('current_session');
				$current_term = getSetting('current_term');
				if(($session_id != $current_session) || ($term_id != $current_term)) {
					showMessage("Oops! Your PIN has expired. Please contact the school management to get a new one", 'red');
				}	
			}
			
			//chek acces limit
			if(($uses > 0) && ($used >= $uses)) {
				showMessage("Oops! Your have axceeded the allowed portal access limit for this PIN. Please contact the school management to get a new one", 'red');
			}
			
			//chech if PIN is activated
			if($student_id < 1) {
				define('No_More','true');
				showMessage("This appears to be your first time of using this PIN so we need to link it with your Student's Profile. <br>Please enter your Student Admission Number to activate your profile. You will be able to access your Profile directly with this PIN once this is done", "blue");
			?>	
				 <div id="login-center">
					<div id="login-head"><i class="fa fa-lock"></i> Profile Activation</div>
						<div id="login-form">
           				<img src="<?php echo $siteLogo; ?>" alt="<?php echo $siteName; ?>" title="<?php echo $siteName; ?>" />
						<form method="post" action="" name="login">
							<p><input type="text" name="student_profile" onfocus="if(this.value  == 'Admission Number') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Admission Number'; } "></p>
							<p><button type="submit" id="profile-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Next</button></p>
							<input type="hidden" name="pin_id" value="<?php echo $pin_id; ?>" />
						</form>
						<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Please wait...</div>
					</div>
				</div> 
                <?php powered_by();?>
				<?php
			} else {	
				// set cookie if password was validated
				$_SESSION['SOAUser'] = $user_id;
				setcookie("SOAUser", $user_id, $timeout, '/'); 
				
				//update pin uses
				mysqli_query($server, "UPDATE  `pin` SET  `used` =  used + '1' WHERE `serial` ='$name'");
				
				//Set Last Login Date and Time
				$day = date("Y-m-d H:i:s");
				mysqli_query($server, "UPDATE  `users` SET  `last_login` =  '$day' WHERE `id` ='$user_id'");
				setsessions_var($user_id);
				
				//set last login IP
				$user_ip = getenv('REMOTE_ADDR');	
				mysqli_query($server, "UPDATE  `users` SET  `lastIP` =  '$user_ip' WHERE `id` ='$user_id'");
			   
			   $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			   header('location: '.$actual_link);
			}
		  }
		} elseif (isset($_POST['student_profile'])) {
			define('No_More','true');
			$pin_id = $_POST['pin_id'];
		  	$student = isset($_POST['student_profile']) ? $_POST['student_profile'] : '';
			$query="SELECT * FROM students WHERE id = '$student' OR admission_number = '$student'"; 
			$result = mysqli_query($server, $query) or die(mysqli_error($server));  
			$row = mysqli_fetch_assoc($result); 
			$num = mysqli_num_rows($result);
			if($num < 1) {
				showMessage("Sorry but we could not find any student associated with this Admission Number. Please check your Admission Number and try again or contact your school management for help", 'red');
				?>	
				 <div id="login-center">
					<div id="login-head"><i class="fa fa-lock"></i> Profile Activation</div>
						<div id="login-form">
           				<img src="<?php echo $siteLogo; ?>" alt="<?php echo $siteName; ?>" title="<?php echo $siteName; ?>" />
						<form method="post" action="" name="login">
							<p><input type="text" name="student_profile" onfocus="if(this.value  == 'Admission Number') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Admission Number'; } "></p>
							<p><button type="submit" id="profile-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Next</button></p>
							<input type="hidden" name="pin_id" value="<?php echo $pin_id; ?>" />
						</form>
						<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Please wait...</div>
					</div>
				</div> 
                <?php powered_by();?>
			<?php  	} else {
			
			$query="SELECT * FROM students WHERE id = '$student' OR admission_number = '$student'"; 
			$result = mysqli_query($server, $query) or die(mysqli_error($server));  
			$row = mysqli_fetch_assoc($result); 
			$student_id = $row['id'];
			$name2 = $row['last_name'].' '.$row['first_name'].' '.$row['other_name'];
			@define('No_More','true');
			showMessage("Is your full name <b>$name2</b>?<br>Click OK to dismiss this message then click the Activate button if this is correct. ", 'red');
			?>
            <div id="login-center">
                  <div id="login-head"><i class="fa fa-lock"></i> Profile Activation</div>
                    <div id="login-form">
           			<img src="<?php echo $siteLogo; ?>" alt="<?php echo $siteName; ?>" title="<?php echo $siteName; ?>" />
                    <p style="padding:10px;background:white;margin:10px auto;"><?php echo "Are you <b>$name2</b>?<br>Click the Activate button if this is correct."; ?><br /></p><form method="post" action="" name="login">
                        <input type="hidden" name="pin_id" value="<?php echo $pin_id; ?>" />
                        <input type="hidden" name="student_confirm" value="<?php echo $student_id; ?>" />
                        <p><button type="submit" id="profile-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Activate</button></p>
                        <p><button type="button" id="prof-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Cancel</button></p>
                    </form>
                   <div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Activating...</div>
                </div>
            </div>
            <?php powered_by();?>
        <?php	}
		
		} elseif (isset($_POST['student_confirm'])) {
			$id = mysqli_real_escape_string($server, $_POST['student_confirm']);
			$pin_id = mysqli_real_escape_string($server, $_POST['pin_id']);
			
			$student_user = portalUserID($id, 'Student');
			$parent_id = getParent($id);
			$parent_user = portalUserID($parent_id, 'Parent');
			$date = date('Y-m-d');
			
			//update pin
			mysqli_query($server, "UPDATE  `pin` SET  `date_activated` =  '$date' WHERE `id` ='$pin_id'");
			mysqli_query($server, "UPDATE  `pin` SET  `student_id` =  '$student_user' WHERE `id` ='$pin_id'");
			mysqli_query($server, "UPDATE  `pin` SET  `parent_id` =  '$parent_user' WHERE `id` ='$pin_id'");
			
			//do login
			showMessage("Congrats! Your Student's Profile has been activated. Enter your PIN details to procceed", 'green');
		} else {
		
		}
if(!defined('No_More')) {	 
	if(isset($_GET['reset'])) {   ?>
	<div id="login-center">
            <div id="login-head"><i class="fa fa-lock"></i> Reset Password</div>
			<div id="login-form">
            <img src="<?php echo $siteLogo; ?>" alt="<?php echo $siteName; ?>" title="<?php echo $siteName; ?>" />
            <form method="post" action="" name="login">
                <p><input type="email" name="userEmail" placeholder=" Your Email Address"></p>
                <p><button type="submit" id="login-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Reset Password</button></p>
            </form>
            <p><a href="index.php"><button>Retry Login</button></a></p>
			<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Processing...</div>
        </div>
    </div>    
    <?php powered_by();?>
    <?php } else { ?>
   <div id="login-center">
       <div id="login-head"><i class="fa fa-lock"></i> Students login</div>
		<div id="login-form">
        <img src="<?php echo $siteLogo; ?>" alt="<?php echo $siteName; ?>" title="<?php echo $siteName; ?>" />

<?php if($pin_enabled > 0) {?>
            <form method="post" action="" name="login">
                <p><input type="text" name="pin_serial" onfocus="if(this.value  == 'Your Scratch-Card No.') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Your Scratch-Card No.'; } " value="Your Scratch-Card No."></p>
                <p><input type="password" name="pin" onfocus="if(this.value  == 'Your PIN') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Your PIN'; } " value="Your PIN"></p>
                <p><button type="submit" id="pin-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Continue</button></p>
            </form>
			<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Validating PIN...</div>
<?php } else {?>
            <form method="post" action="" name="login">
                <p><input type="text" name="access_login" onfocus="if(this.value  == 'Your Username') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Your Username'; } " value="Your Username"></p>
                <p><input type="password" name="access_password" onfocus="if(this.value  == 'Your Password') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Your Password'; } " value="Your Password"></p>
                <p><button type="submit" id="login-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Login</button></p>
            </form>
			<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Authenticating...</div>
<?php } ?>
		</div>
    </div>
    <?php powered_by();?>
 <?php	} //end of No_More
 	} //end password or login
}

function applicantLoginForm() {
	global $server;
	$siteName = getSetting('name');
	$logo = getSetting('logo');
	$school_id=$_SESSION['school_id'];
	$siteLogo = 'media/uploads/'.getSetting('logo');
	$register_pin_enabled = getSetting('register_pin_enabled');
	$register_portal_enabled = getSetting('register_portal_enabled');
	if(empty($siteName)): $siteName = 'SOA'; endif;
	if(empty($logo)): $siteLogo = 'media/images/logo.png'; endif;

	if($register_portal_enabled < 1 || (strtotime(getSetting('register_close_date'))>0 && time() >= strtotime(getSetting('register_close_date')))) {
		$message = 'Sorry but our students application portal has closed for this accademic session!<br>Please check back later or contact the school administration for more information.';
		$class = 'blue';
	}
	if($register_pin_enabled < 1) {
		$message = 'Welcome to '.$siteName.' students application portal. <br>Enter your First & Last name to start your application process.';
		$class = 'blue';
	}
	if(!empty($message)) { showMessage($message, $class); } 
	
	if (isset($_POST['access_password'])) {
		$login = $name =  mysqli_real_escape_string($server,$_POST['access_login']);
		$password =  mysqli_real_escape_string($server,$_POST['access_password']);
		$query="SELECT * FROM applicants WHERE first_name = '$name' AND last_name = '$password' AND school_id = '$school_id'"; 
		$result = mysqli_query($server,$query) or die(mysqli_error($server));  
		$row = mysqli_fetch_assoc($result); 
		$num = mysqli_num_rows($result);
		if($num < 1) {
			//create new profile
			$query = mysqli_query($server,"INSERT INTO applicants (`id`, `school_id`, `first_name`, `last_name`, `serial`) VALUES (NULL, '$school_id', '$name', '$password', '');") or die(mysqli_error($server));
			$user_id = mysqli_insert_id($server);
				
			//set user-id
			//$user_id = getInsertedID('applicants');
		
			//set cookie
			$_SESSION['SOAApply'] = $user_id;
			setcookie("SOAApply", $user_id, $timeout, '/'); 
			$_SESSION['localsession_id'] = time();
			//Set Last Login Date and Time
			$day = date("Y-m-d");
			mysqli_query($server,"UPDATE  `applicants` SET  `application_date` =  '$day' WHERE `id` ='$user_id'");
		   header('location: apply.php');
			
		} else {
			$user_id = $row['id'];
			$_SESSION['localsession_id'] = time();
			// set cookie if password was validated
			$_SESSION['SOAApply'] = $user_id;
			setcookie("SOAApply", $user_id, $timeout, '/'); 
				
			//Set Last Login Date and Time
			$day = date("Y-m-d");
			mysqli_query($server,"UPDATE  `applicants` SET  `application_date` =  '$day' WHERE `id` ='$user_id'");
			 header('location: apply.php');
		  }
		
		} elseif (isset($_POST['pin_serial'])) {
		  $login = $name =  mysqli_real_escape_string($server,$_POST['pin_serial']);
		  $password =  mysqli_real_escape_string($server,$_POST['pin']);
			$query="SELECT * FROM pin WHERE serial = '$name' AND school_id = '$school_id'"; 
			$result = mysqli_query($server,$query) or die(mysqli_error($server));  
			$row = mysqli_fetch_assoc($result); 
			$num = mysqli_num_rows($result);
		if($num < 1) {
			showMessage("Your PIN is invalid. Please check your Serial No./PIN or contact your school management for help.", 'red');
		}
		
		$pwsalt = explode( ":",$row["pin"]);	
		$pass2 = $row["pin"];
		if(md5($password . $pwsalt[1]) != $pwsalt[0] && md5($password) != $row["pin"]) {	
			showMessage("Oops! Your Serial No. & PIN does not match", 'red');
		  }
		  else {
			$query="SELECT * FROM pin WHERE serial = '$name'"; 
			$result = mysqli_query($server,$query) or die(mysqli_error($server));  
			$row = mysqli_fetch_assoc($result); 
			$parent_id = $row['parent_id'];
			$student_id = $row['student_id'];
			$session_id = $row['session_id'];
			$validity_type = $row['validity_type'];
			$pin_id = $row['id'];
			
			//set profile id
			$user_id = $student_id;

				if($parent_id > 0 || $student_id > 0) {
					showMessage("Sorry! This PIN can not be used on this portal. Please contact the school management to get a new one", 'red');
				}
			
			//chech if PIN is activated
			$query="SELECT * FROM applicants WHERE serial = '$name'"; 
			$result = mysqli_query($server,$query) or die(mysqli_error($server));  
			$row = mysqli_fetch_assoc($result); 
			$numPIN = mysqli_num_rows($result);
			
			if($numPIN < 1) {
				//create new profile
				$query = mysqli_query($server,"INSERT INTO applicants (`id`, `school_id`, `first_name`, `last_name`, `serial`) VALUES (NULL, '$school_id', '', '', '$serial');") or die(mysqli_error($server));
				
				//set user-id
				$user_id = getInsertedID('applicants');
		
				//update PIN date and status 
				$date = date('Y-m-d');
				mysqli_query($server,"UPDATE  `pin` SET  `date_activated` =  '$date' WHERE `id` ='$pin_id'");
				mysqli_query($server,"UPDATE  `pin` SET  `applicant` =  '$user_id' WHERE `id` ='$pin_id'");		
				
				//set cookie
				$_SESSION['localsession_id'] = time();
				$_SESSION['SOAApply'] = $user_id;
				setcookie("SOAApply", $user_id, $timeout, '/');
				
			   header('location: apply.php');
			} else {	
				$user_id = $row['id'];
				$_SESSION['localsession_id'] = time();
				// set cookie if password was validated
				$_SESSION['SOAApply'] = $user_id;
				setcookie("SOAApply", $user_id, $timeout, '/');
				
			   header('location: apply.php');
			}
		  }
		
		} else {
		}
?>

   <div id="login-center">
       <div id="login-head"><i class="fa fa-lock"></i> Application Portal</div>
		<div id="login-form">
        <img src="<?php echo $siteLogo; ?>" alt="<?php echo $siteName; ?>" title="<?php echo $siteName; ?>" />

<?php if($register_pin_enabled > 0) {?>
            <form method="post" action="" name="login">
                <p><input type="text" name="pin_serial" onfocus="if(this.value  == 'Your Scratch-Card No.') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Your Scratch-Card No.'; } " value="Your Scratch-Card No."></p>
                <p><input type="password" name="pin" onfocus="if(this.value  == 'Your PIN') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Your PIN'; } " value="Your PIN"></p>
                <p><button type="submit" id="pin-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Continue</button></p>
            </form>
			<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Validating PIN...</div>
<?php } else {?>
            <form method="post" action="" name="login">
                <p><input type="text" name="access_login" onfocus="if(this.value  == 'Your First Name') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Your First Name'; } " value="Your First Name"></p>
                <p><input type="text" name="access_password" onfocus="if(this.value  == 'Your Last Name') { this.value = ''; } " onblur="if(this.value == '') { this.value = 'Your Last Name'; } " value="Your Last Name"></p>
                <p><button type="submit" id="login-submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" >Enter Portal</button></p>
            </form>
			<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Redirecting...</div>
<?php } ?>

		</div>
    </div>
    <?php powered_by();?>
<?php		
}

function refreshPage() {
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	header('location: '.$actual_link);	
}


function ShowProcceed() {
	$siteName = getSetting('name');
	$logo = getSetting('logo');
	$siteLogo = 'media/uploads/'.getSetting('logo');
	$pin_enabled = getSetting('pin_enabled');
	if(empty($siteName)): $siteName = 'SOA'; endif;
	if(empty($logo)): $siteLogo = 'media/images/logo.png'; endif;
	 if(!empty($message)) { showMessage($message, $class); } 
	 
?>
        <p style="text-align:center;"><a href="index.php"><button class="submit" style="float:none; margin-top: 140px; width: 200px; height: 50px; font-size: 18px;">Continue</button></a></p>
<?php powered_by();?>
 <?php
}
?>