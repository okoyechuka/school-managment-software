<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		dashboard.php
Description:	This is main portal dashboard page
Developer: 		Ynet Interactive
Date: 			9/3/2015
*/
global $server;
define('calender',1);
$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());
$calender = true;

if(!empty($message)) { showMessage($message, $class); }
global $hooks;
$hooks->do_action('StudentDashboardBefore'); 
?><br>
<div class="wrapper row">
	<div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big" id="clock"></span>
        <span class="small"><?=date('F j, Y',time())?></span>
    </div>
    
	<a href="userfee"><div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big"><i class="fa fa-money"></i></span>
        <span class="small">School Fees</span>
    </div></a>

<?php
//if(userRole($userID) == 6) {
?>
	<a href="usertimetable"><div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big"><i class="fa fa-clock-o"></i></span>
        <span class="small">Time-Table</span>
    </div></a>

	<a href="userexam"><div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big"><i class="fa fa-book"></i></span>
        <span class="small">Exam Reports</span>
    </div></a>
<?php //} ?>

<!-- students time table -->

<!-- school calendar -->
<div class="wrapper row">
<!-- Show  -->
    <div class="col-lg-8 col-md-7 col-sm-12">
        <div class="panel">
            <div class="panel-head"><i class="fa fa-calendar"></i> School Schedules & Calendar</div>
            <div class="panel-bodyC">
                <div id="calendar"></div>
            </div>
        </div>
    </div>    
    <div class="col-lg-4 col-md-5 col-sm-12">
        <div class="panel">
            <div class="panel-head"><i class="fa fa-calendar"></i> Upcoming Events</div>
<?php
$tday=date('Y-m-d');
$query = "select * FROM schedules p WHERE p.school_id = '$school_id' AND date >= '$tday' ORDER BY date DESC LIMIT 5";
$result = mysqli_query($server, $query);
$number = mysqli_num_rows($result);
if($number > 0) {
	while($row = mysqli_fetch_assoc($result)){
?>    		
            <div class="col-sm-12" class="small_list2">
            	<small><?=date('F j, Y',strtotime($row['date']))?> <br></small>
                <?=$row['schedule']?>
            </div>
    <?php } ?>        
<?php } else { ?>            
            <div class="col-sm-12" style="background: rgba(153,204,204,0.5); color: #000; text-align:center;padding:15px;margin:20px auto; width:95%; margin-left: 2.5%;">
            	<h1><i class="fa fa-exclamation-circle"></i></h1><h5>There are currently no upcoming events for your school!</h5>
            </div>
<?php } ?>             		           
        </div>    
    </div>    
    
</div>
<script>
function showClock(){
 	var currentTime = new Date();
  	var currentHours = currentTime.getHours();
	var currentMinutes = currentTime.getMinutes();
	var timeOfDay = ( currentHours >= 12 ) ? 'PM' : 'AM';
  	currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;
  	currentHours = ( currentHours == 0 ) ? 12 : currentHours;
  	var currentTimeString = currentHours + ":" + ('0' + currentMinutes).slice(-2) + " " + timeOfDay;
   	$("#clock").html(currentTimeString);
}
$(document).ready(function(){  setInterval('showClock()', 1000); });	
</script>
<?php
global $hooks;
$hooks->do_action('StudentDashboardAfter'); 
?>