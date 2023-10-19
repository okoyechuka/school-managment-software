<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$school_id = @$_SESSION['school_id'];
global $school_id;
global $hooks;$hooks->do_action('PreMenuLoad');
function adminMenu() {
	global $userID; global $URL;
?>
<div id="side-menu" style="position: fixed;top:44px;left:0px;">
    	<a href="admin.php"><span class="link <?php if($URL=="admindashboard"): echo 'active'; endif ?>"><i class="fa fa-dashboard"></i> Dashboard</span></a>
        <?php global $hooks;$hooks->do_action('AddMenuTop');?>
        <?php  if(isOwner($userID)) {?>
        	<?php if(HASENTERPRISE > 1) { ?>
        	<a href="admin/schools"><span class="link <?php if($URL=='schools'): echo 'active'; endif ?>"><i class="fa fa-list"></i> Manage Schools</span></a>
            <?php } else { ?>
            <a href="admin/schools?enterprise"><span class="link <?php if($URL=='schools'): echo 'active'; endif ?>"><i class="fa fa-cube"></i> Get SOA Enterprise</span></a>
            <?php } ?>
        <?php } ?>
        
<?php if(userRole($userID) == 1 || userRole($userID) == 2) { ?>        
        <span id="item5" onclick="togleShowMenu('resellerMenu5')" class="link <?php if($URL=='admit' || $URL == 'applicant'): echo 'active'; endif ?>"><i class="fa fa-graduation-cap"></i> Admission</span>
		    <div class="resellerMenuLists"  id="resellerMenu5">
                <a href="admin/admit"><span class="link sublink">Admit New Student</span></a>
            	<a href="admin/applicant"><span class="link sublink">Manage Applicants</span></a>
                <?php global $hooks;$hooks->do_action('AddMenuAdmission');?>
            </div>        
<?php } ?>        

<?php if(userRole($userID) < 3) { ?>        
        <span id="item6" onclick="togleShowMenu('resellerMenu6')" class="link <?php if($URL=='student' || $URL =='parent' || $URL =='guardian'): echo 'active'; endif ?>"><i class="fa fa-users"></i> People</span>
		    <div class="resellerMenuLists"  id="resellerMenu6">
                <a href="admin/student"><span class="link sublink">Students</span></a>
            	<a href="admin/parent"><span class="link sublink">Parents</span></a>
                <a href="admin/guardian"><span class="link sublink">Guardians</span></a>
                <a href="admin/teacher"><span class="link sublink">Teachers</span></a>
                <?php global $hooks;$hooks->do_action('AddMenuPeople');?>
            </div>
<?php } else {?>        
        <a href="admin/student"><span class="link <?php if($URL=='student'): echo 'active'; endif ?>"><i class="fa fa-graduation-cap"></i> Students</span></a>
        
        <a href="admin/parent"><span class="link <?php if($URL=='parent'): echo 'active'; endif ?>"><i class="fa fa-users"></i> Parents</span></a>
        
        <a href="admin/guardian"><span class="link <?php if($URL=='guardian'): echo 'active'; endif ?>"><i class="fa fa-users"></i> Guardians</span></a>        
<?php } ?>        

<?php if(userRole($userID) == 4 || userRole($userID) == 7) { ?>
        <a href="admin/subject"><span class="link <?php if($URL=='subject'): echo 'active'; endif ?>"><i class="fa fa-tasks"></i> Subjects & Syllabus</span></a>
        <a href="admin/timetable"><span class="link <?php if($URL=='timetable'): echo 'active'; endif ?>"><i class="fa fa-clock-o"></i> Class Time-Table</span></a>
        
<?php } ?>

<?php if(userRole($userID) < 3 || userRole($userID) == 8) { ?>
        <span id="item55" onclick="togleShowMenu('resellerMenu55')" class="link <?php if($URL=='library'): echo 'active'; endif ?>"><i class="fa fa-book"></i> Library</span>
		    <div class="resellerMenuLists"  id="resellerMenu55">
                <a href="admin/library"><span class="link sublink">Manage Books</span></a>
                <a href="admin/librarycategory"><span class="link sublink">Book Categories</span></a>
                <a href="admin/library?issue"><span class="link sublink">Issue Books</span></a>
                <a href="admin/library?return"><span class="link sublink">Return Books</span></a>
                <a href="admin/bookhistory"><span class="link sublink">Issue History</span></a>
                <?php global $hooks;$hooks->do_action('AddMenuLibrary');?>
            </div>       
<?php } ?>

<?php if(userRole($userID) < 3) { ?>
        <span id="item3" onclick="togleShowMenu('resellerMenu3')" class="link <?php if($URL=='subject' || $URL =='attendance' || $URL=='timetable'): echo 'active'; endif ?>"><i class="fa fa-institution"></i> School Management Tools</span>
		    <div class="resellerMenuLists"  id="resellerMenu3">
          	  <a href="admin/subject"><span class="link sublink">Manage Subjects </span></a>
              <a href="admin/grade"><span class="link sublink">Manage Grades </span></a>
            	<a href="admin/class"><span class="link sublink">Manage Classes</span></a>
                <a href="admin/session"><span class="link sublink">Manage Sessions </span></a>
                <a href="admin/term"><span class="link sublink">Manage Terms </span></a>
                <a href="admin/attendance"><span class="link sublink">Manage Class Attendance</span></a>
                <a href="admin/timetable"><span class="link sublink">Class Time-Tables </span></a>
                <a href="admin/calendar"><span class="link sublink">School Calendar & Schedules</span></a>
                <?php global $hooks;$hooks->do_action('AddMenuTools');?>
			</div>
<?php } ?>
<?php if(userRole($userID) > 2) { ?>
        <a href="admin/calendar"><span class="link <?php if($URL=='calendar'): echo 'active'; endif ?>"><i class="fa fa-calendar"></i> School Schedules</span></a>
<?php } ?>        
<?php if(userRole($userID) == 1 || userRole($userID) == 2 || userRole($userID) == 3 || userRole($userID) == 7) { ?>  
        <span id="item1" onclick="togleShowMenu('resellerMenu')" class="link <?php if($URL=='fee'): echo 'active'; endif ?>"><i class="fa fa-money"></i> Fees</span>
		    <div class="resellerMenuLists"  id="resellerMenu">
            	<a href="admin/fee"><span class="link sublink">List Fees</span></a>
                <?php if(userRole($userID) == 1 || userRole($userID) == 2 || userRole($userID) == 3) { ?>
                <?php if(userRole($userID)<3) { ?>
                <a href="admin/fee?new"><span class="link sublink">Create New Fee </span></a>
                <?php } ?>
                <a href="admin/fee?pay"><span class="link sublink">Pay Fees </span></a>
                <?php } ?>
                <a href="admin/payment"><span class="link sublink">Fee Payment Reports</span></a>
                <?php global $hooks;$hooks->do_action('AddMenuFees');?>
			</div>
<?php } ?>

<?php if(userRole($userID) == 1 || userRole($userID) == 2 || userRole($userID) == 4 ) { ?>
		<a href="admin/assignment"><span class="link <?php if($URL=='assignment'): echo 'active'; endif ?>"><i class="fa fa-pencil"></i> Assignments</span></a>
        <span id="item2" onclick="togleShowMenu('resellerMenu2')" class="link <?php if($URL=='exam'): echo 'active'; endif ?>"><i class="fa fa-file-text-o"></i> Exams & Records</span>
		    <div class="resellerMenuLists"  id="resellerMenu2">
            	<a href="admin/exam"><span class="link sublink">List Exams</span></a>
                <?php if( userRole($userID) < 3) { ?>  
                <a href="admin/exam?new"><span class="link sublink">Create Exam </span></a>
                <?php } ?>
                <a href="admin/reportcard"><span class="link sublink">View Exam Reports </span></a>
                <?php if( userRole($userID) < 3) { ?>  
                <a href="admin/promotion"><span class="link sublink">Promote Students </span></a>
                <?php } ?>
                <?php global $hooks;$hooks->do_action('AddMenuExam');?>
			</div>
            
        <span id="item2cbt" onclick="togleShowMenu('resellerMenu2cbt')" class="link <?php if($URL=='cbt'||$URL=='courses'): echo 'active'; endif ?>"><i class="fa fa-desktop"></i> E-Learning Tools</span>
		    <div class="resellerMenuLists"  id="resellerMenu2cbt">
                <a href="admin/courses"><span class="link sublink">Manage Courses </span></a>
                 <?php if( userRole($userID) < 3) { ?>  
                <a href="admin/courses?new"><span class="link sublink">Create Courses </span></a>
                <?php } ?>
            	<a href="admin/cbt"><span class="link sublink">Computer-based Tests</span></a>
                <?php global $hooks;$hooks->do_action('AddMenuElearning');?>
			</div>    
<?php } ?>

<?php if( userRole($userID) == 4) { ?>            
        <a href="admin/attendance"><span class="link <?php if($URL=='attendance'): echo 'active'; endif ?>"><i class="fa fa-check-square-o"></i> Attendance</span></a>
<?php } ?>     

        <span onclick="togleShowMenu('resellerMenu2nw')" class="link <?php if($URL=='gallery'||$URL=='store'||$URL=='yearbook'||$URL=='document'): echo 'active'; endif ?>"><i class="fa fa-cubes"></i> Extras & More</span>
		    <div class="resellerMenuLists"  id="resellerMenu2nw">
            	<a href="admin/admingallery"><span class="link sublink">Photo Gallery</span></a>
                <?php if(userRole($userID) == 1 || userRole($userID) == 2 || userRole($userID) == 10) { ?>                   
                <a href="admin/store"><span class="link sublink">Store </span></a>
                <?php } ?>
                <a href="admin/yearbook"><span class="link sublink">Year Book </span></a>
                <a href="admin/document"><span class="link sublink">Documents </span></a>
                <?php global $hooks;$hooks->do_action('AddMenuExtras');?>
			</div>
<?php if(userRole($userID) == 1 || userRole($userID) == 2 || userRole($userID) == 7) { ?>           
        <a href="admin/hostel"><span class="link <?php if($URL=='hostel'): echo 'active'; endif ?>"><i class="fa fa-university"></i> Hostels</span></a>
<?php } ?>

<?php if(userRole($userID) == 1 || userRole($userID) == 2 || userRole($userID) == 7) { ?>           
        <a href="admin/transport"><span class="link <?php if($URL=='transport'): echo 'active'; endif ?>"><i class="fa fa-car"></i> Transport</span></a>
<?php } ?>

<?php if(userRole($userID) < 4) { ?>        
        <span id="item7" onclick="togleShowMenu('resellerMenu7')" class="link <?php if($URL=='account'): echo 'active'; endif ?>"><i class="fa fa-dollar"></i> Accounting Tools</span>
		    <div class="resellerMenuLists"  id="resellerMenu7">
                <a href="admin/account"><span class="link sublink">Cash-flow Report</span></a>
                <a href="admin/account?new"><span class="link sublink">Add Income/Expense</span></a>
            	<a href="admin/admintransaction"><span class="link sublink">Transactions</span></a>
   <a href="admin/adminalert"><span class="link sublink">Payment Notifications</span></a>
   				<?php global $hooks;$hooks->do_action('AddMenuAccount');?>
            </div>
<?php } ?>
<?php if(userRole($userID) < 4) { ?>            
       <span id="item17" onclick="togleShowMenu('resellerMenu17')" class="link <?php if($URL=='payroll'): echo 'active'; endif ?>"><i class="fa fa-briefcase"></i> Human Resource</span>
		    <div class="resellerMenuLists"  id="resellerMenu17">
   <?php if(userRole($userID) < 3) { ?>           
                <a href="admin/teacher?new"><span class="link sublink">Create School Teachers</span></a>                
                <a href="admin/staff?new"><span class="link sublink">Create Other Staffs</span></a>
                <a href="admin/teacher"><span class="link sublink">Manage Teachers</span></a>
                <a href="admin/staff"><span class="link sublink">Manage Other Staffs</span></a>
   <?php } ?>             
                <a href="admin/payroll"><span class="link sublink">Employee Payroll</span></a>
                <a href="admin/payslip"><span class="link sublink">Manage Pay Slip</span></a>
                <?php global $hooks;$hooks->do_action('AddMenuHR');?>
			</div>
     
     <?php if(userRole($userID) < 3) { ?>       
       <span id="item4" onclick="togleShowMenu('resellerMenu4')" class="link <?php if($URL=='setting'): echo 'active'; endif ?>"><i class="fa fa-shield"></i> Admin Tools</span>
		    <div class="resellerMenuLists"  id="resellerMenu4">
                <a href="admin/pin"><span class="link sublink">Manage Scratch Card PINs</span></a>
                <a href="admin/adminuser"><span class="link sublink">Manage Users</span></a>
                <a href="admin/email"><span class="link sublink">Send Email</span></a>
                <a href="admin/sms"><span class="link sublink">Send SMS</span></a>
                <a href="admin/notice"><span class="link sublink">Create Notice</span></a>            
				<?php if(userRole($userID) == 1) { ?>
                <a href="admin/generalsetting?general"><span class="link sublink">School Setup</span></a>
            <?php } ?> 
                <?php global $hooks;$hooks->do_action('AddMenuAdmin');?>
            </div>  
       <?php } ?>     
<?php } ?> 

     <?php if(userRole($userID) < 3) { ?>       
       <span id="item41" onclick="togleShowMenu('resellerMenu41')" class="link <?php if($URL==117): echo 'active'; endif ?>"><i class="fa fa-cogs"></i> Settings & Tools</span>
		    <div class="resellerMenuLists"  id="resellerMenu41">
            	<?php  if(isOwner($userID)) { ?>
                <a href="admin/backup"><span class="link sublink">Database Backup Tools</span></a>
                <?php } ?>
            <?php if(userRole($userID) == 1) { ?>
           		<a href="admin/generalsetting"><span class="link sublink">School Settings</span></a>
                <a href="admin/customfields"><span class="link sublink">Custom Fields</span></a>
                <a href="admin/currency"><span class="link sublink">Currency Settings</span></a>
                <a href="admin/paymentgateway"><span class="link sublink">Payment Gateway Settings</span></a>
                <a href="admin/smsgateway"><span class="link sublink">SMS Gateway Settings</span></a>
                <?php  if(isOwner($userID)) {?>
                <a href="admin/license"><span class="link sublink">System Information</span></a>
               <?php } ?>

            <?php } ?> 
                 <?php global $hooks;$hooks->do_action('AddMenuSettings');?>
            </div>  
       <?php } ?>     
        <?php global $hooks;$hooks->do_action('AddMenuBottom');?>
        <a href="admin.php?logout"><span class="link"><i class="fa fa-power-off"></i> Logout</span></a>
        
    </div>
<?php	
}
function applicantMenu() {
	global $userID;	global $URL;
?>
<div id="side-menu">
   	<a href="apply.php"><span class="link <?php if(!isset($_GET['status'])): echo 'active'; endif ?>"><i class="fa fa-dashboard"></i> Manage Application</span></a>       
     <a href="apply.php?status"><span class="link <?php if(isset($_GET['status'])): echo 'active'; endif ?>"><i class="fa fa-clock-o"></i> Check Status</span></a>     
     <?php global $hooks;$hooks->do_action('AddMenuApplicantMenu');?>
     <a href="apply.php?logout"><span class="link"><i class="fa fa-power-off"></i> Logout</span></a>
 </div>
<?php		
}

function studentMenu() {
	global $userID; global $URL;
?>
<div id="side-menu">
    <a href="index.php"><span class="link <?php if($URL=='dashboard'): echo 'active'; endif ?>"><i class="fa fa-dashboard"></i> Dashboard</span></a> 
    <?php global $hooks;$hooks->do_action('AddMenuPortalMenuTop');?>      
<?php if(userRole($userID) == 5) { ?>        
    <a href="userstudent"><span class="link <?php if($URL=='userstudent'): echo 'active'; endif ?>"><i class="fa fa-users"></i> My Children</span></a>
<?php } else {?>        
    <a href="userstudent"><span class="link <?php if($URL=='userstudent'): echo 'active'; endif ?>"><i class="fa fa-user"></i> My Profile</span></a>
<?php } ?>        
<?php if(userRole($userID) == 6) { ?>
    <a href="usertimetable"><span class="link <?php if($URL=='usertimetable'): echo 'active'; endif ?>"><i class="fa fa-clock-o"></i> Time-Table</span></a>
<?php } ?>
<?php if(userRole($userID) == 6) { ?>
    <a href="usersubject"><span class="link <?php if($URL=='usersubject'): echo 'active'; endif ?>"><i class="fa fa-tasks"></i> My Subjects</span></a>
    <a href="userassignment"><span class="link <?php if($URL=='userassignment'): echo 'active'; endif ?>"><i class="fa fa-pencil"></i> My Class Assignments</span></a> 
    <span id="item2cbt" onclick="togleShowMenu('resellerMenu2cbt')" class="link <?php if($URL=='usercbt'||$URL=='usercourses'): echo 'active'; endif ?>"><i class="fa fa-desktop"></i> E-Learning Tools</span>
		    <div class="resellerMenuLists"  id="resellerMenu2cbt">
                <a href="usercourses"><span class="link sublink">My Courses </span></a>
            	<a href="usercbt"><span class="link sublink">Computer-based Tests</span></a>
                <?php global $hooks;$hooks->do_action('AddMenuElearning');?>
			</div>
    <a href="userreportcard"><span class="link <?php if($URL=='userreportcard'): echo 'active'; endif ?>"><i class="fa fa-file-o"></i> My Exam Reports</span></a>        
<?php } else { ?>
	<a href="userassignment"><span class="link <?php if($URL=='userassignment'): echo 'active'; endif ?>"><i class="fa fa-pencil"></i> Assignments</span></a> 
    <a href="userreportcard"><span class="link <?php if($URL=='userreportcard'): echo 'active'; endif ?>"><i class="fa fa-file-o"></i> Exam Reports</span></a>   
    <?php } ?>
    <a href="userdocument"><span class="link <?php if($URL=='userdocument'): echo 'active'; endif ?>"><i class="fa fa-file-pdf-o"></i> Documents</span></a>
    <a href="gallery"><span class="link <?php if($URL=='gallery'): echo 'active'; endif ?>"><i class="fa fa-photo"></i> Gallery</span></a>
    <a href="usercalendar"><span class="link <?php if($URL=='usercalendar'): echo 'active'; endif ?>"><i class="fa fa-calendar"></i> School Schedules</span></a>
     <span id="item1" onclick="togleShowMenu('resellerMenu')" class="link <?php if($URL=='userfee'): echo 'active'; endif ?>"><i class="fa fa-money"></i> Fees</span>
		    <div class="resellerMenuLists"  id="resellerMenu">
            	<a href="userfee"><span class="link sublink">List My Fees</span></a>
			</div>
    <a href="useryearbook"><span class="link <?php if($URL=='useryearbook'): echo 'active'; endif ?>"><i class="fa fa-photo"></i> Year Book</span></a>
    <a href="usertransaction"><span class="link <?php if($URL=='usertransaction'): echo 'active'; endif ?>"><i class="fa fa-dollar"></i> My Transactions</span></a>            
    <?php global $hooks;$hooks->do_action('AddMenuPortalMenuBottom');?>
    <a href="index.php?logout"><span class="link"><i class="fa fa-power-off"></i> Logout</span></a>
 </div>
<?php	
}

if(getUser()>0) {
	$working_file=basename($_SERVER['PHP_SELF']);
	switch ($working_file) {
		case 'admin.php': 
		adminMenu();
		break;
		case 'apply.php': 
		applicantMenu();
		break;
		default: studentMenu();
	}
?>    
    <div id="main-body">
    	<div id="title">
        	<div id="labt" class="title"><?php echo $title; ?></div>
            <?php if($working_file!='apply.php') { ?>
            <div class="action">Hi <?php echo @end(explode(' ',userData('name',$userID))); ?>, <strong>Last login</strong>: <?php echo date('M d, Y', strtotime(userData('last_login',$userID))); ?>. </div><?php } ?>
        </div>
<?php } ?>
<?php
$sql = "SELECT * FROM paymentalerts WHERE status = 'Pending' ORDER BY id DESC LIMIT 15";
$result = mysqli_query($server,$sql);
$num_alert = mysqli_num_rows($result);

if(($num_alert > 0)) {
	$message = 'You have <strong>'.$num_alert.'</strong> Payment Notifications.';
	$class = 'blue';
}

//check session
$currentSession = getSetting('current_session');
$currentTerm = getSetting('current_term');
global $hooks;$hooks->do_action('PostMenuLoad');
if (sessionEnded($currentSession)) {
	$message = 'Your current school session ('.sessionName($currentSession).') has ended!. <br>You need to create and activate a new session before you proceed.<br><br>';
	if(userRole($userID) <3) {
		$message .= '<a href="admin/session?" title="Difine Active Session">Click Here</a> to set a new active session now.';
	} else {
		$message .= 'Consult your school administrator for assistance';
	}
	$message .= '<br><br>Remember to set New active Term and Promote students to new classes.';
	$class='yellow';;
}
if((@$_SESSION['mana_school_id'] < 1) && (TOTAL_SCHOOL > 1) && (strpos($_SERVER['REQUEST_URI'], 'schools') === false)) {
	if(isOwner($userID)) {
		showSelectSchool();
	}
}
if(isset($_REQUEST['switch_school']) && $_REQUEST['switch_school'] > 0) {
	 if(isOwner($userID)) {
		$_SESSION['mana_school_id'] = $_REQUEST['switch_school'];
		$_SESSION['school_id'] = $_REQUEST['switch_school'];
		header('location: admindashboard');
		exit;
	 }
}