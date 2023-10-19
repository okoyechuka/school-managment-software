<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		class.php
Description:	This is the parent page
Developer: 		Ynet Interactive
Date: 			22/02/2015
*/
global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

if(userRole($userID) != 5) {
header('location: index.php');
}


//view record
if(isset($_REQUEST['student'])) {
	$student = filterinp($_REQUEST['student']);
	$class = filterinp($_REQUEST['class']);
	$term = filterinp($_REQUEST['term']);
	$session = filterinp($_REQUEST['session']);

		$sql=$query="SELECT * FROM student_attendance WHERE session_id = '$session' AND class_id = '$class' AND term_id = '$term' AND student_id = '$student'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$num = mysqli_num_rows($result);
?>
<div id="add-new">
   <div id="add-new-head"><?php echo sessionName($session).' '.termName($term) ?> Attendance for <?php echo studentName($student); ?>

   </div>
   <div class="inside" style="background-color: #fff;">
	<div id="Attendance">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <!-- Sender ID -->
      <tr>
        <td align="right"  valign="middle"><strong><blue></blue></strong></td>
        <td>
        <a href="" onClick="javascript:printDiv('Attendance')"> <button class="submit">Print Report</button></a>
        </td>
      </tr>
      <tr>
        <td align="right" colspan="2" valign="middle"><strong><blue></blue></strong><br /></td>
        <td>
      </tr>
      <tr>
        <td align="left" width="60%" style="border-bottom: 1px solid black;" valign="middle">Date</td>
        <td width="60%" align="left" style="border-bottom: 1px solid black;" valign="middle">Attendance</td>
      </tr>
   <?php
	 $i=0;
	 while($row = mysqli_fetch_assoc($result)){
   $attend = $row['attendance'];
   if($attend != 'Present') {$tag1 = '<red>'; $tag2 = '</red>';} else {$tag1 = '<green>'; $tag2 = '</green>';}
   ?>
   		<tr>
    	    <td align="left" valign="middle"><?php echo date('D d M, Y', strtotime($row['date'])); ?></td>
            <td width="60%" align="left" valign="middle">
			<?php echo $tag1.$row['attendance'].$tag2; ?>
            </td>
    	</tr>
		<?php
   		$i++;
		}
		?>

      <!-- Submit Buttons -->
      <tr>
        <td align="left" valign="top">&nbsp;</td>
        <td width="69%" align="left" valign="top">
        </td>
      </tr>
    </table>
	</div>
	</div>
</div>
<?php
}
?>
</div>