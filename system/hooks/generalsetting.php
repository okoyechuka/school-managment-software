<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

global $server;

if(isset($_REQUEST['msg'])) {
	$message = 'This seem to be your first time. Please configure your School Portal before you continue. <br>Remember to change your admin password too';
	$class = 'blue';
}

if(userRole($userID) > 2) {
header('location: admin.php');
}

if(isset($_POST['save-setting'])) {
	foreach ($_POST as $key => $value ){
		$n = count($_POST);
		if($key != 'save-setting' && $key != 'sendMessage' && $key != 'logo' && $key != 'background') {
			$value = mysqli_real_escape_string($server,$value);
			saveSettings($key,$value,0) ;	
			mysqli_query($server,"UPDATE `schools` SET `$key` =  '$value' WHERE `id` = '$school_id';");	
		}
	}
	//Upload files
	
	if(!empty($_FILES['logo']['name'])) {
		$upload_path = 'media/uploads/';
		$file2 = $_FILES['logo']['name'];		
		$filename = date("d-m-Y").$_FILES['logo']['name'];
		$ext = end(explode(".", $_FILES['logo']['name']));
		$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
		if(!in_array(strtolower($ext), $allowed)) {
			//This file format is not allowed
		} else {
			if(move_uploaded_file($_FILES['logo']['tmp_name'],$upload_path . $filename) || ($file2 =="")) {
				saveSettings('logo',$filename,0) ;	
				mysqli_query($server,"UPDATE `schools` SET `logo` =  '$filename' WHERE `id` = '$school_id';");	
			} 
		}
	}
	
	if(!empty($_FILES['background']['name'])) {
		$upload_path = 'media/images/';
		$file2 = $_FILES['background']['name'];		
		$filename = date("d-m-Y").$_FILES['background']['name'];
		$ext = end(explode(".", $_FILES['background']['name']));
		$allowed = array("jpg","jpeg","gif","png","swf","bmp","");
		if(!in_array(strtolower($ext), $allowed)) {
			//This file format is not allowed
		} else {
			if(move_uploaded_file($_FILES['background']['tmp_name'],$upload_path . $filename) || ($file2 =="")) {
				saveSettings('background',$filename,0) ;	
				mysqli_query($server,"UPDATE `schools` SET `background` =  '$filename' WHERE `id` = '$school_id';");	
			} 
		}
	}
	showMessage("Your settings have been successfully updated. Simply go to your Dashboard to apply these settings now", '');
}
?>

<div class="wrapper">
	<div class="inner-left" style="width: 100%;">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
    <form method="post" action="admin/generalsetting" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" class="tr-heading" colspan="2"  valign="middle"><g>School Settings</g></td>
      </tr>

      <tr>
        <td align="left" valign="middle">Your School ID:</td>
        <td  align="left" valign="middle">
        	<input type="text"  id="ud" readonly placeholder="" value="<?php echo $_SESSION['school_id']; ?>">
        </td>
      </tr>
      <?php if(HASENTERPRISE > 1) { ?>
      <tr>
        <td align="left" valign="middle">Your School Username:</td>
        <td  align="left" valign="middle">
        	<input type="text" readonly  value="<?php echo getSetting('username'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">School Portal URL:</td>
        <td  align="left" valign="middle">
        	<input type="text" readonly value="<?php echo home_base_url().'?sch='.getSetting('username'); ?>">
        </td>
      </tr>
      <?php } ?>
      <tr>
        <td align="left" valign="middle">School Name:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="name" id="name" required="required" placeholder="" value="<?php echo getSetting('name'); ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Change School Logo:</td>
        <td  align="left" valign="middle">
        	<input type="file" accept="image/*" style="width: 70%" name="logo" id="logo2" >
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Change Background Image:</td>
        <td  align="left" valign="middle">
        	<input type="file" accept="image/*" style="width: 70%" name="background" id="background" >
        </td>
      </tr>

 <tr>
        <td align="left" valign="middle">Change Theme:</td>
        <td  align="left" valign="middle">
              <select name="theme" id="e2" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM themes ORDER BY id ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

                $i=0;
								while($row= mysqli_fetch_assoc($result)){
                $url = $row['url'];
                $title0 = $row['title'];
            ?>
               <option value="<?php echo $url; ?>" <?php if(getSetting('theme') == $url) {echo 'selected';} ?>><?php echo $title0; ?></option>
            <?php
					$i++;
				}
			 ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">School Address:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="address" id="address" required="required" placeholder="" value="<?php echo getSetting('address'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">City:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="city" id="city" required="required" placeholder="" value="<?php echo getSetting('city'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Local Council:<small>optional</small></td>
        <td  align="left" valign="middle">
        	<input type="text"  name="local_council" id="lga"  placeholder="" value="<?php echo getSetting('local_council'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">State:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="state" id="state" required="required" placeholder="" value="<?php echo getSetting('state'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Country:</td>
        <td  align="left" valign="middle">
        	<select name="country_id" id="e1" style="width: 98%;" >
            	<?php getCountryList(getSetting('country_id')); ?>
            </select>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Phone Number:</td>
        <td  align="left" valign="middle">
        	<input type="text"  name="phone1" id="phone1" required="required" placeholder="" value="<?php echo getSetting('phone1'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Alternative Number:<small>optional</small></td>
        <td  align="left" valign="middle">
        	<input type="text"  name="phone2" id="phone2"  placeholder="" value="<?php echo getSetting('phone2'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email Address:</td>
        <td  align="left" valign="middle">
        	<input type="email"  name="email" id="email" required="required" placeholder="" value="<?php echo getSetting('email'); ?>">
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">School Website:<small>optional</small></td>
        <td  align="left" valign="middle">
        	<input type="url"  name="domain" id="website" placeholder="http://www.yourschool.com" value="<?php echo getSetting('domain'); ?>">
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" class="tr-heading" colspan="2" valign="middle"><b>Exams & Report Card Settings</b></td>
      </tr>

      <tr>
        <td align="left" valign="middle">Number of Continuous Assessments:</td>
        <td  align="left" valign="middle">
            <select required name="num_assignment" id="num_assignment" style="width: 90%;" >
               <option value="5" <?php if(getSetting('num_assignment')== '5') { echo 'selected';} ?> >Five Assessments</option>
               <option value="4" <?php if(getSetting('num_assignment')== '4') { echo 'selected';} ?> >Four Assessments</option>
               <option value="3" <?php if(getSetting('num_assignment')== '3') { echo 'selected';} ?> >Three Assessments</option>
               <option value="2" <?php if(getSetting('num_assignment')== '2') { echo 'selected';} ?> >Two Assessments</option>
               <option value="1" <?php if(getSetting('num_assignment')== '1') { echo 'selected';} ?> >One Assessment</option>
			</select>
        </td>
      </tr>
      
      <tr class="ass_1">
        <td align="left" valign="middle">Assessment 1 Title</td>
        <td  align="left" valign="middle">
        	<input type="text" name="assessOneName" placeholder="E.g. First Test" value="<?php echo getSetting('assessOneName'); ?>">
        </td>
      </tr>
      <tr class="ass_2">
        <td align="left" valign="middle">Assessment 2 Title</td>
        <td  align="left" valign="middle">
        	<input type="text" name="assessTwoName" placeholder="E.g. Second Test" value="<?php echo getSetting('assessTwoName'); ?>">
        </td>
      </tr>
      <tr class="ass_3">
        <td align="left" valign="middle">Assessment 3 Title</td>
        <td  align="left" valign="middle">
        	<input type="text" name="assessThreeName" placeholder="E.g. Third Test" value="<?php echo getSetting('assessThreeName'); ?>">
        </td>
      </tr>
      <tr class="ass_4">
        <td align="left" valign="middle">Assessment 4 Title</td>
        <td  align="left" valign="middle">
        	<input type="text" name="assessFourName" placeholder="E.g. 4th Test" value="<?php echo getSetting('assessFourName'); ?>">
        </td>
      </tr>
      <tr class="ass_5">
        <td align="left" valign="middle">Assessment 5 Title</td>
        <td  align="left" valign="middle">
        	<input type="text" name="assessFiveName" placeholder="E.g. 5th Test" value="<?php echo getSetting('assessFiveName'); ?>">
        </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" class="tr-heading" colspan="2" valign="middle"><b>Accademic Settings</b></td>
      </tr>
       <tr>
        <td align="left" valign="middle">Active Accademic Session:</td>
        <td  align="left" valign="middle">
              <select name="current_session" id="e2" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM sessions WHERE school_id = '$school_id' ORDER BY start_date DESC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title0 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(getSetting('current_session') == $g_id) {echo 'selected';} ?>><?php echo $title0; ?></option>
            <?php  }
			 if($num < 1) { ?>
               <option value="" >You need to Create a Session first</option>
             <?php
						}
			 ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Active Accademic Term:</td>
        <td  align="left" valign="middle">
        	<select name="current_term" id="e3" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM terms WHERE school_id = '$school_id' ORDER BY title DESC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title0 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(getSetting('current_term') == $g_id) {echo 'selected';} ?>><?php echo $title0; ?></option>
            <?php
							}
			 if($num < 1) { ?>
               <option value="" >You need to Create a Term first</option>
             <?php }
			 ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Graduation Class:</td>
        <td  align="left" valign="middle">
        	<select name="graduate_class_id" id="e4" style="width: 90%;" >
			<?php
                $sql=$query="SELECT * FROM classes WHERE school_id = '$school_id' ORDER BY title DESC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

				while($row = mysqli_fetch_assoc($result)){
                $g_id = $row['id'];
                $title0 = $row['title'];
            ?>
               <option value="<?php echo $g_id; ?>" <?php if(getSetting('graduate_class_id') == $g_id) {echo 'selected';} ?>><?php echo $title0; ?></option>
            <?php
					}
			 if($num < 1) { ?>
               <option value="" >You need to Create a Class first</option>
             <?php }
			 ?>
			</select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td class="tr-heading" colspan="2" align="left" valign="middle"><b>School Portal Settings</b></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Students Portal Authentication:</td>
        <td  align="left" valign="middle">
        	<select name="pin_enabled" id="reg_p" style="width: 98%;" >
            	 <option <?php if(getSetting('pin_enabled') == '0') { echo 'selected';} ?> value="0">Username & Password</option>
                 <option <?php if(getSetting('pin_enabled') == '1') { echo 'selected';} ?> value="1">Scratch-Card Serial No. & PIN </option>
            </select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">Enable Application Portal :</td>
        <td  align="left" valign="middle">
        	<select name="register_portal_enabled" id="en_re" style="width: 98%;" onclick="craateUserJsObject.ShowPrivileges();">
            	 <option <?php if(getSetting('register_portal_enabled') == '0') { echo 'selected';} ?> value="0">No</option>
                 <option <?php if(getSetting('register_portal_enabled') == '1') { echo 'selected';} ?> value="1">Yes </option>
            </select>
        </td>
      </tr>

      <tr>
        <td align="left" valign="middle">
        <div id="reg_start1" class="resources" style="">
        Application Close Date:<br /><small>(YYYY-MM-DD) Applicants will not be able to submit form after this date</small>
        </div>
        </td>
        <td  align="left" valign="middle">
        <div id="reg_start" class="resources" style="">
        	<input  type="date"  name="register_close_date" id="register_close_date" placeholder="" value="<?php echo getSetting('register_close_date'); ?>">
         </div>
        </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Application Portal Authentication:</td>
        <td  align="left" valign="middle">
        	<select name="register_pin_enabled" id="e" style="width: 98%;" >
            	 <option <?php if(getSetting('register_pin_enabled') == '0') { echo 'selected';} ?> value="0">Open (No login required)</option>
                 <option <?php if(getSetting('register_pin_enabled') == '1') { echo 'selected';} ?> value="1">Scratch-Card Serial No. & PIN </option>
            </select>
        </td>
      </tr>

	   <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td class="tr-heading" colspan="2" align="left" valign="middle"><b>Email Server Settings</b></td>
      </tr>
       <tr class="ass_2">
        <td align="left" valign="middle">SMTP Server<br><small></small></td>
        <td  align="left" valign="middle">
        	<input type="text" name="smtpServer" placeholder="E.g. example.com" value="<?php echo getSetting('smtpServer'); ?>">
        </td>
      </tr>
       <tr class="ass_2">
        <td align="left" valign="middle">SMTP Port<br><small></small></td>
        <td  align="left" valign="middle">
        	<input type="text" name="smtpPort" placeholder="Eg. 465" value="<?php echo getSetting('smtpPort'); ?>">
        </td>
      </tr>
       <tr class="ass_2">
        <td align="left" valign="middle">SMTP Username<br><small></small></td>
        <td  align="left" valign="middle">
        	<input type="text" name="smtpUsername" placeholder="mail@example.com" value="<?php echo getSetting('smtpUsername'); ?>">
        </td>
      </tr>
       <tr class="ass_2">
        <td align="left" valign="middle">SMTP Password<br><small></small></td>
        <td  align="left" valign="middle">
        	<input type="text" name="smtpPassword" placeholder="" value="<?php echo getSetting('smtpPassword'); ?>">
        </td>
      </tr>
      
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td class="tr-heading" colspan="2" align="left" valign="middle"><b>Other Settings</b></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Default Time Zone:</td>
        <td  align="left" valign="middle">
            <select name="defaultTimeZone" id="defaultTimeZone" style="width:90%">
                <option value="Pacific/Midway">(GMT-11:00) Midway Island</option>
                <option value="US/Samoa">(GMT-11:00) Samoa</option>
                <option value="US/Hawaii">  (GMT-10:00) Hawaii</option>
                <option value="US/Alaska">  (GMT-09:00) Alaska</option>
                <option value="US/Pacific">   (GMT-08:00) Pacific Time (US &amp; Canada)</option>
                <option value="America/Tijuana">  (GMT-08:00) Tijuana</option>
                <option value="US/Arizona"> (GMT-07:00) Arizona</option>
                <option value="US/Mountain">  (GMT-07:00) Mountain Time (US &amp; Canada)</option>
                <option value="America/Chihuahua">    (GMT-07:00) Chihuahua</option>
                <option value="America/Mazatlan">     (GMT-07:00) Mazatlan</option>
                <option value="America/Mexico_City">  (GMT-06:00) Mexico City</option>
                <option value="America/Monterrey">    (GMT-06:00) Monterrey</option>
                <option value="Canada/Saskatchewan">  (GMT-06:00) Saskatchewan</option>
                <option value="US/Central">           (GMT-06:00) Central Time (US &amp; Canada)</option>
                <option value="US/Eastern">           (GMT-05:00) Eastern Time (US &amp; Canada)</option>
                <option value="US/East-Indiana">      (GMT-05:00) Indiana (East)</option>
                <option value="America/Bogota">(GMT-05:00) Bogota</option>
                <option value="America/Lima">         (GMT-05:00) Lima</option>
                <option value="America/Caracas">      (GMT-04:30) Caracas</option>
                <option value="Canada/Atlantic">      (GMT-04:00) Atlantic Time (Canada)</option>
                <option value="America/La_Paz">(GMT-04:00) La Paz</option>
                <option value="America/Santiago">     (GMT-04:00) Santiago</option>
                <option value="Canada/Newfoundland">  (GMT-03:30) Newfoundland</option>
                <option value="America/Buenos_Aires"> (GMT-03:00) Buenos Aires</option>
                <option value="Greenland">            (GMT-03:00) Greenland</option>
                <option value="Atlantic/Stanley">     (GMT-02:00) Stanley</option>
                <option value="Atlantic/Azores">      (GMT-01:00) Azores</option>
                <option value="Atlantic/Cape_Verde">  (GMT-01:00) Cape Verde Is.</option>
                <option value="Africa/Casablanca">    (GMT) Casablanca</option>
                <option value="Europe/Dublin">        (GMT) Dublin</option>
                <option value="Europe/Lisbon">        (GMT) Lisbon</option>
                <option value="Europe/London">        (GMT) London</option>
                <option value="Africa/Monrovia">      (GMT) Monrovia</option>
                <option value="Europe/Amsterdam">     (GMT+01:00) Amsterdam</option>
                <option value="Europe/Belgrade">      (GMT+01:00) Belgrade</option>
                <option value="Europe/Berlin">        (GMT+01:00) Berlin</option>
                <option value="Europe/Bratislava">    (GMT+01:00) Bratislava</option>
                <option value="Europe/Brussels">      (GMT+01:00) Brussels</option>
                <option value="Europe/Budapest">      (GMT+01:00) Budapest</option>
                <option value="Europe/Copenhagen">    (GMT+01:00) Copenhagen</option>
                <option value="Europe/Ljubljana">     (GMT+01:00) Ljubljana</option>
                <option value="Europe/Madrid">        (GMT+01:00) Madrid</option>
                <option value="Europe/Paris">         (GMT+01:00) Paris</option>
                <option value="Europe/Paris">         (GMT+01:00) West/Central Africa</option>
                <option value="Africa/Lagos">         (GMT+01:00) Lagos / Nigeria</option>
                <option value="Europe/Prague">        (GMT+01:00) Prague</option>
                <option value="Europe/Rome">          (GMT+01:00) Rome</option>
                <option value="Europe/Sarajevo">      (GMT+01:00) Sarajevo</option>
                <option value="Europe/Skopje">        (GMT+01:00) Skopje</option>
                <option value="Europe/Stockholm">     (GMT+01:00) Stockholm</option>
                <option value="Europe/Vienna">        (GMT+01:00) Vienna</option>
                <option value="Europe/Warsaw">        (GMT+01:00) Warsaw</option>
                <option value="Europe/Zagreb">        (GMT+01:00) Zagreb</option>
                <option value="Europe/Athens">        (GMT+02:00) Athens</option>
                <option value="Europe/Bucharest">     (GMT+02:00) Bucharest</option>
                <option value="Africa/Cairo">         (GMT+02:00) Cairo</option>
                <option value="Africa/Harare">        (GMT+02:00) Harare</option>
                <option value="Europe/Helsinki">      (GMT+02:00) Helsinki</option>
                <option value="Europe/Istanbul">      (GMT+02:00) Istanbul</option>
                <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                <option value="Europe/Kiev">          (GMT+02:00) Kyiv</option>
                <option value="Europe/Minsk">         (GMT+02:00) Minsk</option>
                <option value="Europe/Riga">          (GMT+02:00) Riga</option>
                <option value="Europe/Sofia">         (GMT+02:00) Sofia</option>
                <option value="Europe/Tallinn">(GMT+02:00) Tallinn</option>
                <option value="Europe/Vilnius">(GMT+02:00) Vilnius</option>
                <option value="Asia/Baghdad">         (GMT+03:00) Baghdad</option>
                <option value="Asia/Kuwait">          (GMT+03:00) Kuwait</option>
                <option value="Africa/Nairobi">(GMT+03:00) Nairobi</option>
                <option value="Asia/Riyadh">          (GMT+03:00) Riyadh</option>
                <option value="Asia/Tehran">          (GMT+03:30) Tehran</option>
                <option value="Europe/Moscow">        (GMT+04:00) Moscow</option>
                <option value="Asia/Baku">            (GMT+04:00) Baku</option>
                <option value="Europe/Volgograd">     (GMT+04:00) Volgograd</option>
                <option value="Asia/Muscat">          (GMT+04:00) Muscat</option>
                <option value="Asia/Tbilisi">         (GMT+04:00) Tbilisi</option>
                <option value="Asia/Yerevan">         (GMT+04:00) Yerevan</option>
                <option value="Asia/Kabul">           (GMT+04:30) Kabul</option>
                <option value="Asia/Karachi">         (GMT+05:00) Karachi</option>
                <option value="Asia/Tashkent">        (GMT+05:00) Tashkent</option>
                <option value="Asia/Kolkata">         (GMT+05:30) Kolkata</option>
                <option value="Asia/Kathmandu">(GMT+05:45) Kathmandu</option>
                <option value="Asia/Yekaterinburg">   (GMT+06:00) Ekaterinburg</option>
                <option value="Asia/Almaty">          (GMT+06:00) Almaty</option>
                <option value="Asia/Dhaka">           (GMT+06:00) Dhaka</option>
                <option value="Asia/Novosibirsk">     (GMT+07:00) Novosibirsk</option>
                <option value="Asia/Bangkok">         (GMT+07:00) Bangkok</option>
                <option value="Asia/Jakarta">         (GMT+07:00) Jakarta</option>
                <option value="Asia/Krasnoyarsk">     (GMT+08:00) Krasnoyarsk</option>
                <option value="Asia/Chongqing">(GMT+08:00) Chongqing</option>
                <option value="Asia/Hong_Kong">(GMT+08:00) Hong Kong</option>
                <option value="Asia/Kuala_Lumpur">    (GMT+08:00) Kuala Lumpur</option>
                <option value="Australia/Perth">      (GMT+08:00) Perth</option>
                <option value="Asia/Singapore">(GMT+08:00) Singapore</option>
                <option value="Asia/Taipei">          (GMT+08:00) Taipei</option>
                <option value="Asia/Ulaanbaatar">     (GMT+08:00) Ulaan Bataar</option>
                <option value="Asia/Urumqi">          (GMT+08:00) Urumqi</option>
                <option value="Asia/Irkutsk">         (GMT+09:00) Irkutsk</option>
                <option value="Asia/Seoul">           (GMT+09:00) Seoul</option>
                <option value="Asia/Tokyo">           (GMT+09:00) Tokyo</option>
                <option value="Australia/Adelaide">   (GMT+09:30) Adelaide</option>
                <option value="Australia/Darwin">     (GMT+09:30) Darwin</option>
                <option value="Asia/Yakutsk">         (GMT+10:00) Yakutsk</option>
                <option value="Australia/Brisbane">   (GMT+10:00) Brisbane</option>
                <option value="Australia/Canberra">   (GMT+10:00) Canberra</option>
                <option value="Pacific/Guam">         (GMT+10:00) Guam</option>
                <option value="Australia/Hobart">     (GMT+10:00) Hobart</option>
                <option value="Australia/Melbourne">  (GMT+10:00) Melbourne</option>
                <option value="Pacific/Port_Moresby"> (GMT+10:00) Port Moresby</option>
                <option value="Australia/Sydney">     (GMT+10:00) Sydney</option>
                <option value="Asia/Vladivostok">     (GMT+11:00) Vladivostok</option>
                <option value="Asia/Magadan">         (GMT+12:00) Magadan</option>
                <option value="Pacific/Auckland">     (GMT+12:00) Auckland</option>
                <option value="Pacific/Fiji">         (GMT+12:00) Fiji</option>
                  <option selected value="<?php echo getSetting('defaultTimeZone'); ?>">Current Time Zone is <?php echo getSetting('defaultTimeZone'); ?></option>
            </select>
        </td>
      </tr>


      <tr>
        <td align="left" valign="middle">Default Currency:</td>
        <td  align="left" valign="middle">
        <select name="currency_id" id="currency" style="width:90%" >
			<?php
                $sql=$query="SELECT * FROM currency ORDER BY id ASC";
                $result = mysqli_query($server, $query);
                $num = mysqli_num_rows($result);

			while($row = mysqli_fetch_assoc($result)){
                $id = $row['id'];
                $title = $row['title'];
            ?>
               <option <?php if(getSetting('currency_id') == $id) { echo 'selected';} ?> value="<?php echo $id; ?>"><?php echo $title; ?></option>
            <?php	}   ?>
			</select>         
            </td>
      </tr>
       <tr class="ass_2">
        <td align="left" valign="middle">School Domain<br><small>Set this if you want to access your school portal from a different domain<br>You will need to create an A-Record pointing the domain to <?=$_SERVER['SERVER_ADDR']?></small></td>
        <td  align="left" valign="middle">
        	<input type="text" name="schoolDomain" placeholder="E.g. example.com" value="<?php echo getSetting('schoolDomain'); ?>">
        </td>
      </tr>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        <input type="hidden" name="save-setting" value="yes" />
        <button class="submit" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="sendMessage" value="1" type="submit">Update Settings</button>
	</form>
     	<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Changes...</div>
        </td>
      </tr>
    </table>

  </div>

</div>
