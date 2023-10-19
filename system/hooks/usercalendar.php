<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		usercalendar.php
Description:	This is the schedule page
Developer: 		Ynet Interactive
Date: 			10/03/2015
*/
global $server;
define('calender',1);
$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());
$calender = true;

//set defaults
$session_id = getSetting('current_session');
$term_id = getSetting('current_term');


$date = date('Y-m-d');
$today20 = increaseDate($date, '20');
$today10 = reduceDate($date, '10');
?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">

    </div>

<?php
$sql=$query = "SELECT * FROM schedules WHERE school_id = '$school_id' AND (date BETWEEN '$today10' AND '$today20') ORDER BY date DESC LIMIT 24";

if(isset($_GET['keyword'])) {
	$today10 = $_GET['from'];
	$today20 = $_GET['to'];

	$school_id = $school_id;

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
	         $clauses[] = "p.schedule LIKE '%$term%' OR p.date LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "p.schedule LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql=$query = "select * FROM schedules p WHERE p.school_id = '$school_id' AND (date BETWEEN '$today10' AND '$today20') AND $filter ORDER BY date DESC";
}
$result = mysqli_query($server, $query) or die(mysqli_error($server));
$number = mysqli_num_rows($result);
?>
	<div class="panel">
    	<div class="panel-head"><i class="fa fa-calendar"></i> School Calendar </div>
			<div class="panel-bodyC">
        	<div id="calendar"></div>
        </div>
    </div>

</div>