<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		admindashboard.php
Description:	This is main admin dashboard page
Developer: 		Ynet Interactive
Date: 			17/05/2017
*/
$currentSession = getSetting('current_session');
$currentTerm = getSetting('current_term');

if(TOTAL_SCHOOL>0 && !DEMO_MODE && userRole($userID) < 3) {
	if(!getSetting('name') || !getSetting('email') || !getSetting('address')) {
		header('location: generalsetting?general&msg=1'); exit;
	}
	if(!getSetting('current_session')) {
		header('location: session?msg=1'); exit;
	}
	if(!getSetting('current_term')) {
		header('location: term?msg=1'); exit;
	}
	if(countSchoolClass() < 1) {
		header('location: class?msg=1'); exit;
	}
}
global $hooks;
$hooks->do_action('AdminDashboardBefore'); 
define('calender',true);
$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());
global $verssion_id;
$query="SELECT count(id) as males FROM students WHERE school_id = '$school_id' AND sex = 'Male' AND (status = 1 OR status = 2)";
$result = mysqli_query($server, $query);$row = mysqli_fetch_assoc($result);
$male = $row['males'];
$query="SELECT count(id) as females FROM students WHERE school_id = '$school_id' AND sex = 'Female' AND (status = 1 OR status = 2)";
$result = mysqli_query($server, $query);$row = mysqli_fetch_assoc($result);
$female = $row['females'];
$query="SELECT count(id) as total FROM students WHERE school_id = '$school_id' AND (status = 1 OR status = 2)";
$result = mysqli_query($server, $query);$row = mysqli_fetch_assoc($result);
$total = $row['total'];
$Pmale=round(($male/$total)*100);
$Pfemale=round(($female/$total)*100);
if(!empty($message)) { showMessage($message, $class); } 
?><br>
<div class="wrapper row">
	<div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big" id="clock"></span>
        <span class="small"><?=date('F j, Y',time())?></span>
    </div>
    
    <a href="admin/student"><div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big"><?php echo countStudent(); ?></span>
        <span class="small">Active Students</span>
    </div></a>

	<a href="admin/parent"><div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big"><?php echo countParent(); ?></span>
        <span class="small">Parents</span>
    </div></a>
<?php 
if(userRole($userID) < 3 || userRole($userID) == 7) {
?>
	<a href="admin/teacher"><div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big"><?php echo countTeacher($school_id); ?></span>
        <span class="small">Teachers</span>
    </div></a>    
<?php } ?>
<?php 
if(userRole($userID) < 4) {
?>
 
<?php } if(userRole($userID) == 1) { ?>
	<a href="admin/generaletting?general"><div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big"><i class="fa fa-cogs"></i></span>
        <span class="small">Setup Wizard</span>
    </div></a>    
<?php }
if(userRole($userID) < 3) {
?>    
	<a href="admin/admit"><div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big"><i class="fa fa-graduation-cap"></i></span>
        <span class="small">Admit Students</span>
    </div></a>
<?php } 
if(userRole($userID) < 4) {
?>
	<a href="admin/fee?pay"><div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big"><i class="fa fa-money"></i></span>
        <span class="small">Pay Fees</span>
    </div></a>
<?php } 
if(userRole($userID) < 3 || userRole($userID) == 4) { 
?>
	<a href="admin/exam"><div class="status-box col-lg-3 col-sm-12 col-md-4" >
    	<span class="big"><i class="fa fa-file"></i></span>
        <span class="small">Exams</span>
    </div></a>               
<?php } ?>
</div>

<div class="wrapper row">
	<div class="col-lg-6 col-md-6 col-sm-12">
    	<div class="progress">
          <div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="70"
          aria-valuemin="0" aria-valuemax="100" style="width:<?=$Pmale?>%">
            <?=$Pmale?>% Male Students
          </div>
        </div>
        <div class="progress">
          <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="70"
          aria-valuemin="0" aria-valuemax="100" style="width:<?=$Pfemale?>%">
            <?=$Pfemale?>% Female
          </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12">
    	<div class="infobox">
            <p><strong>Session:</strong> <?=sessionName($currentSession)?></p>
            <p><strong>Term:</strong> <?=termName($currentTerm)?></p>
        </div>    
    </div>
</div>

<div class="wrapper row">
<!-- Show  -->
	<div class="col-lg-8 col-md-7 col-sm-12">
        <div class="panel">
            <div class="panel-head"><i class="fa fa-calendar"></i> School Schedules & Calendar</div>
                <div id="calendar"></div>     
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
      <?php if(userRole($userID) < 3) { ?> 		
      		<p align="center"><a href="admin/calendar?new"><button type="button" class="submit success">Add Event</button></a></p>
      <?php } ?>      
<?php } ?>             		           
        </div>    
    </div>    
</div>

<div class="wrapper row">
	<div class="col-lg-5 col-md-5 col-sm-12">
    	<div class="panel"> 
            <div class="panel-head"><i class="fa fa-users"></i> Latest Students</div>
<?php
$query = "select * FROM students p WHERE p.school_id = '$school_id' ORDER BY id DESC LIMIT 6";
$result = mysqli_query($server, $query);
$number = mysqli_num_rows($result);
if($number > 0) {
	while($row = mysqli_fetch_assoc($result)){
		$id = $row['id'];
		$class = className(getClass($row['id'],$currentSession));
		$user = getUserID($row['id'],'6');
		$name = studentName($id);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}
?>    		
            <a href="admin/student?view=<?php echo $id;?>"><div class="col-sm-12 small_list">
            	<div class="col-xs-2">
                	<img src="media/uploads/<?php echo $picture;?>" style="width: 100%;border-radius:20%;"/>
                </div>
                <div class="col-xs-10">
                    <strong><?=$name?></strong>
                    <small><br><?=$class?></small>
                </div>
            </div></a>
    <?php } ?>        
<?php } else { ?>            
            <div class="col-sm-12" style="background: rgba(153,204,204,0.5); color: #000; text-align:center;padding:15px;margin:20px auto; width:95%; margin-left: 2.5%;">
            	<h1><i class="fa fa-exclamation-circle"></i></h1><h5>You have not admitted any students yet!</h5>
            </div>
<?php } ?>             		           
            
        </div>    
    </div>
    <div class="col-lg-7 col-md-7 col-sm-12">
        <div class="panel"> 
            <div class="panel-head"><i class="fa fa-bars"></i> Past 10 Years' Intake Overview</div>
            <div class="panel-body" style="width: 100%;">
                   <canvas id="canvas1" height="300px" width="500" style="min-width: 500px;"></canvas>
            </div>    
        </div>    
    </div> 

<?php if(userRole($userID) < 4) { ?>    
	<div class="panel"> 
    	<div class="panel-head"><i class="fa fa-bars"></i> <?php echo date('Y'); ?> Cash Flow Overview</div>
	    <div class="panel-body" style="width: 100%;">
              <canvas id="canvas3" height="300px" width="800"  style="min-width: 500px;"></canvas>
       </div>     
    </div> 
 <?php } ?>  
</div>

<?php

$cYear = date('Y');
//10 yers income
$thisYearIncome = '"'.monthIncome($cYear.'-01',$school_id).'", '.'"'.monthIncome($cYear.'-02',$school_id).'", '.'"'.monthIncome($cYear.'-03',$school_id).'", '.'"'.monthIncome($cYear.'-04',$school_id).'", '.'"'.monthIncome($cYear.'-05',$school_id).'", '.'"'.monthIncome($cYear.'-06',$school_id).'", '.'"'.monthIncome($cYear.'-07',$school_id).'", '.'"'.monthIncome($cYear.'-08',$school_id).'", '.'"'.monthIncome($cYear.'-09',$school_id).'", '.'"'.monthIncome($cYear.'-10',$school_id).'", '.'"'.monthIncome($cYear.'-11',$school_id).'", '.'"'.monthIncome($cYear.'-12',$school_id).'"';

//10 yers expense
$thisYearExpense = '"'.monthExpense($cYear.'-01',$school_id).'", '.'"'.monthExpense($cYear.'-02',$school_id).'", '.'"'.monthExpense($cYear.'-03',$school_id).'", '.'"'.monthExpense($cYear.'-04',$school_id).'", '.'"'.monthExpense($cYear.'-05',$school_id).'", '.'"'.monthExpense($cYear.'-06',$school_id).'", '.'"'.monthExpense($cYear.'-07',$school_id).'", '.'"'.monthExpense($cYear.'-08',$school_id).'", '.'"'.monthExpense($cYear.'-09',$school_id).'", '.'"'.monthExpense($cYear.'-10',$school_id).'", '.'"'.monthExpense($cYear.'-11',$school_id).'", '.'"'.monthExpense($cYear.'-12',$school_id).'"';

//10 yers expense
$yearAdmission = '"'.yearAdmission(date('Y')-10,$school_id).'", '.'"'.yearAdmission(date('Y')-9,$school_id).'", '.'"'.yearAdmission(date('Y')-8,$school_id).'", '.'"'.yearAdmission(date('Y')-7,$school_id).'", '.'"'.yearAdmission(date('Y')-6,$school_id).'", '.'"'.yearAdmission(date('Y')-5,$school_id).'", '.'"'.yearAdmission(date('Y')-4,$school_id).'", '.'"'.yearAdmission(date('Y')-3,$school_id).'", '.'"'.yearAdmission(date('Y')-2,$school_id).'", '.'"'.yearAdmission(date('Y')-1,$school_id).'", '.'"'.yearAdmission(date('Y'),$school_id).'"';

$years = '"'.(date('Y')-10).'", '.'"'.(date('Y')-9).'", '.'"'.(date('Y')-8).'", '.'"'.(date('Y')-7).'", '.'"'.(date('Y')-6).'", '.'"'.(date('Y')-5).'", '.'"'.(date('Y')-4).'", '.'"'.(date('Y')-3).'", '.'"'.(date('Y')-2).'", '.'"'.(date('Y')-1).'", '.'"'.date('Y').'"';
?>
<script>
var barChartData3 = {
		labels : ["Jan","Fed","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Noc","Dec"],
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

var barChartData1 = {
		labels : [<?php echo $years; ?>],
		datasets : [
			{
				label: "Admissions",
				fillColor : "rgba(0,120,200,0.4)",
				strokeColor : "rgba(0,120,220,1)",
				pointColor : "rgba(0,120,220,1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(220,220,220,1)",
				data : [<?php echo $yearAdmission; ?>]
			}
		] 
	}
	window.onload = function(){
		var ctx = document.getElementById("canvas1").getContext("2d");
		window.myBar = new Chart(ctx).Bar(barChartData1, {
			responsive : true
		});

<?php if(userRole($userID) < 4) { ?> 		
		var ctx = document.getElementById("canvas3").getContext("2d");
		window.myBar = new Chart(ctx).Line(barChartData3, {
			responsive : true
		});
<?php } ?>
	}

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
$hooks->do_action('AdminDashboardAfter'); 
?>