<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');  global $userID; global $LANG; global $server; $school_id = $_SESSION['school_id']; 
$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;

/*
File name: 		student.php
Description:	This is the students page
Developer: 		Ynet Interactive
Date: 			10/3/2015
*/

global $server;

$Currency = new DefaultCurrency();
$userRate = $Currency->Rate(getUser());
$userSymbul = $Currency->Symbul(getUser());

	$currentSession = getSetting('current_session');
	$currentTerm = getSetting('current_term');

if(userRole($userID) == 5) {
$parent = userProfile($userID);


//view record
if(isset($_REQUEST['student'])) {
if(userRole($userID) != 5) {
header('location: index.php');
}

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
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>

   </div>
   <div class="inside" style="background-color: #fff;">
	<div id="Attendance">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
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
        <td  align="left" style="border-bottom: 1px solid black;" valign="middle">Attendance</td>
      </tr>
   <?php
	 $i=0;
	 while($row = mysqli_fetch_assoc($result)){
   $attend = $row['attendance'];
   if($attend != 'Present') {$tag1 = '<red>'; $tag2 = '</red>';} else {$tag1 = '<green>'; $tag2 = '</green>';}
   ?>
   		<tr>
    	    <td align="left" valign="middle"><?php echo date('D d M, Y', strtotime($row['date'])); ?></td>
            <td  align="left" valign="middle">
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

if(isset($_REQUEST['view'])){
	$student = mysqli_real_escape_string($server, $_REQUEST['view']);
		//get students profile
		$sql=$query="SELECT * FROM students WHERE id = '$student'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}
		//get parent profile
		$parent = getParent($student);
		$sql2=$query2="SELECT * FROM parents WHERE id = '$parent'";
		$result2 = mysqli_query($server, $query2) or die(mysqli_error($server));
		$row2 = mysqli_fetch_assoc($result2);
		$father_name = $row2['father_name'];
?>
<div id="add-new">
   <div id="add-new-head"><?php echo studentName($student); ?>'s ID Credentials
            <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a>
   </div>
   <div class="inside" style="background-color: #CCC;">

	<div class="panel">
        <div class="panel-body panel-body2">
        	<div class="action-box">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print Credentials</button></a>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan=2 style="background-color:#fff" align="left" valign="middle"><img src="media/uploads/<?php echo $picture; ?>" style="width: 50%; max-width: 200px; height: auto; border: 2px solid #999;"/></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Addmission Number:</td>
        <td  align="left" valign="middle"><?php echo $row['admission_number'];?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Full Name:</td>
        <td  align="left" valign="middle"><?php echo $row['last_name'].' '.$row['first_name'].' '.$row['other_name']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Gender:</td>
        <td  align="left" valign="middle"><?php echo $row['sex']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Date of Birth:</td>
        <td  align="left" valign="middle"><?php echo $row['date_of_birth']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Bload Group: </td>
        <td  align="left" valign="middle"><?php echo $row['bload_group']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td colspan="2"  class="tr-heading"  valign="middle"><strong><b>Contact Information</b></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Residence Address:</td>
        <td  align="left" valign="middle"><?php echo $row['address'].', '.$row['city']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Local Council / LGA:</td>
        <td  align="left" valign="middle"><?php echo $row['local_council']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">State of Residence:</td>
        <td  align="left" valign="middle"><?php echo $row['state']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">State of Origin:</td>
        <td  align="left" valign="middle"><?php echo $row['state_origin']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Country of Residence:</td>
        <td  align="left" valign="middle"><?php echo countryName($row['country']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Nationality:</td>
        <td  align="left" valign="middle"><?php echo countryName($row['nationality']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2"  class="tr-heading"  valign="middle"><strong><b>Other Information</b></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Current Class:</td>
        <td  align="left" valign="middle"><?php echo className(getClass($row['id'],$currentSession)); ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Class Teacher:</td>
        <td  align="left" valign="middle"><?php echo teacherName(getClassTeacher(getClass($row['id'],$currentSession))); ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Assigned Hostel:</td>
        <td  align="left" valign="middle"><?php echo hostelName($row['hostel_id']); ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2"  class="tr-heading"  valign="middle"><strong><b>Parents' Information</b></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Father's Full Name:</td>
        <td  align="left" valign="middle"><?php echo $row2['father_name']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Mother's Full Name: </td>
        <td  align="left" valign="middle"><?php echo $row2['mother_name']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Residence Address:</td>
        <td  align="left" valign="middle"><?php echo $row2['address'].', '.$row2['city'].' '.$row2['state'].' '.countryName($row2['country']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email Address:</td>
        <td  align="left" valign="middle"><?php echo $row2['email']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Contact Phone Numnber:</td>
        <td  align="left" valign="middle"><?php echo $row2['phone']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Alternative Phone Numnber:</td>
        <td  align="left" valign="middle"><?php echo $row2['phone2']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2" class="tr-heading"  valign="middle"><strong><b>Guardians</b></strong></td>
      </tr>
     <?php
	$sql=$query = "SELECT * FROM student_guardian WHERE student_id = '$student' ORDER BY id DESC LIMIT 10";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);

	$i=0;
	while($row = mysqli_fetch_assoc($result)){
		$id = $row['guardian_id'];
	 ?>
      <tr>
        <td colspan="2" align="left" valign="middle"><?php echo guardianData($id); ?></td>
      </tr>
      <?php $i++; } ?>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      </table>
	</div>
  </div>
  </div>

</div>
</div>

<?php
}


//display students list

if(isset($_GET['keyword']))
{
$session_id = $_GET['session'];
$class_id = $_GET['class'];

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
	         $clauses[] = "s.first_name LIKE '%$term%' OR s.last_name LIKE '%$term%' OR s.other_name LIKE '%$term%' OR s.sex LIKE '%$term%' OR s.state LIKE '%$term%'";
	    }
	    else
	    {
	         $clauses[] = "s.first_name LIKE '%%' OR s.address LIKE '%%' OR s.sex LIKE '%%' OR s.state LIKE '%%' OR s.last_name LIKE '%%'";
	    }
	}

	//concatenate the clauses together with AND or OR, depending on what i want
	$filter = '('.implode(' OR ', $clauses).')';
	//build and execute the required SQL
	$sql = "select s.* FROM students s JOIN student_class sc ON s.id = sc.student_id JOIN classes c ON c.id = sc.class_id  JOIN student_status ss ON s.status = ss.id JOIN sessions sss ON sc.session_id = sss.id JOIN student_parent p ON p.student_id = s.id WHERE school_id = '$school_id' AND $filter AND ( sc.session_id = '$session_id' AND p.parent_id = '$parent') ";
	if(!empty($class_id)) {
	$sql .=  "AND (sc.class_id = '$class_id')";
	}

 	$result = mysqli_query($server, $sql) or die(mysqli_error($server));
	$num = mysqli_num_rows($result);

	if($num < "1")
		{
		$message = "No match found! Please try another search terms.";
		$class="blue";
		}
} else {
	$sql=$query = "SELECT s.* FROM students s JOIN student_parent p ON s.id = p.student_id WHERE s.school_id = '$school_id' AND p.parent_id = '$parent' ORDER BY s.id DESC LIMIT $pageLimit,$setLimit";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);
	if($num < "1")
		{
		$message = "No students records found!";
		$class="blue";
		}
}

?>

<div class="wrapper">
    	<div id="mess" style="position: relative; top: 0;">
            <?php if(!empty($message)) { showMessage($message,$class); } ?>
        </div>
	<div id="search-pan">
    	<form action="" method="get">
        <input type="search" name="keyword" placeholder="Search Students"/>
        <button class="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
	<div class="panel" style="border-color: transparent;">
        <div class="panel-body">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th width="7.5%" scope="col">S/N</th>
                <th width="30%" scope="col">Full Name</th>
                <th width="15%" scope="col">Class</th>
                <th width="7%" scope="col">Gender</th>
                <th width="12.5%" scope="col">Status</th>
                <th width="27%" scope="col">Action</th>
              </tr>
             </table>
               <?php

				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];
					$class_id = getClass($row['id'],$currentSession);
					$class = className(getClass($row['id'],$currentSession));
					$name = $row['last_name'].' '.$row['first_name'].' '.$row['other_name'];
					$status = $row['status'];
					$sex = $row['sex'];
					if($status==2) { $tag1='<orange>'; $tag2='</ogange>';}
					if($status==4) { $tag1='<gray>'; $tag2='</gray>';}
					if($status==1) { $tag1='<green>'; $tag2='<green>';}
					if($status==4) { $tag1='<red>'; $tag2='</red>';}
					if($status==5) { $tag1='<red>'; $tag2='</red>';}
					if($status==6) { $tag1='<blue>'; $tag2='</blue>';}

				?>
             <div class="virtualpage hidepeice">
              <table class="table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="inner">
                <td width="7.5%"> <?php echo sprintf('%07d',$id); ?></td>
                <td width="30%"> <?php echo $name; ?></td>
                <td width="15%"> <?php echo $class; ?></td>
                <td width="7%"> <?php echo $sex; ?></td>
                <td width="12.5%"> <?php echo $tag1.statusName($status).$tag2; ?></td>
                <td width="27%" valign="middle">
                <a href="userstudent?view=<?php echo $id;?>"><button class="btn-success">Profile</button></a>
                <a href="userreportcard?exam_id&student_id=<?php echo $id;?>&session_id=<?php echo $currentSession;?>&"><button>Report Card</button></a>
                <a href="userstudent?student=<?php echo $id;?>&class=<?php echo $class_id;?>&term=<?php echo $currentTerm;?>&session=<?php echo $currentSession;?>"><button>Attendance</button></a>
                </td>
              </tr>
              </table>
              </div>
              <?php
						$i++;
					} ?>

<?php displayPagination($setLimit,$page,$sql) ?>

        </div>
    </div>
<?php } else  {

	$student = userProfile($userID);

		//get students profile
		$sql=$query="SELECT * FROM students WHERE id = '$student'";
		$result = mysqli_query($server, $query) or die(mysqli_error($server));
		$row = mysqli_fetch_assoc($result);
		$picture = $row['photo'];
		if(empty($picture)) {
			$picture = 'no-body.png';
		}
		//get parent profile
		$parent = getParent($student);
		$sql2=$query2="SELECT * FROM parents WHERE id = '$parent'";
		$result2 = mysqli_query($server, $query2) or die(mysqli_error($server));
		$row2 = mysqli_fetch_assoc($result2);
		$father_name = $row2['father_name'];

?>
	<div class="panel">
        <div class="panel-body panel-body2">
        	<div class="action-box">
            <a href="" onClick="javascript:printDiv('print-this1')"><button class="submit">Print Credentials</button></a>
            </div>
        <div id="print-this1" >
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan=2 style="background-color:#fff" align="left" valign="middle"><img src="media/uploads/<?php echo $picture; ?>" style="width: 50%; max-width: 200px; height: auto; border: 2px solid #999;"/></td>
      </tr>
       <tr>
        <td align="left" valign="middle">Addmission Number:</td>
        <td  align="left" valign="middle"><?php echo $row['admission_number'];?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Full Name:</td>
        <td  align="left" valign="middle"><?php echo $row['last_name'].' '.$row['first_name'].' '.$row['other_name']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Gender:</td>
        <td  align="left" valign="middle"><?php echo $row['sex']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Date of Birth:</td>
        <td  align="left" valign="middle"><?php echo $row['date_of_birth']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Bload Group: </td>
        <td  align="left" valign="middle"><?php echo $row['bload_group']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2"  class="tr-heading" valign="middle"><strong><b>Contact Information</b></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Residence Address:</td>
        <td  align="left" valign="middle"><?php echo $row['address'].', '.$row['city']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Local Council / LGA:</td>
        <td  align="left" valign="middle"><?php echo $row['local_council']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">State of Residence:</td>
        <td  align="left" valign="middle"><?php echo $row['state']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">State of Origin:</td>
        <td  align="left" valign="middle"><?php echo $row['state_origin']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Country of Residence:</td>
        <td  align="left" valign="middle"><?php echo countryName($row['country']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Nationality:</td>
        <td  align="left" valign="middle"><?php echo countryName($row['nationality']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td align="left" colspan="2"  class="tr-heading"  valign="middle"><strong><b>Other Information</b></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Current Class:</td>
        <td  align="left" valign="middle"><?php echo className(getClass($row['id'],$currentSession)); ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Class Teacher:</td>
        <td  align="left" valign="middle"><?php echo teacherName(getClassTeacher(getClass($row['id'],$currentSession))); ?> </td>
      <tr>
        <td align="left" valign="middle">Assigned Hostel:</td>
        <td  align="left" valign="middle"><?php echo hostelName($row['hostel_id']); ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td colspan="2"  class="tr-heading"  align="left" valign="middle"><strong><b>Parents' Information</b></strong></td>
      <tr>
        <td align="left" valign="middle">Father's Full Name:</td>
        <td  align="left" valign="middle"><?php echo $row2['father_name']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Mother's Full Name: </td>
        <td  align="left" valign="middle"><?php echo $row2['mother_name']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Residence Address:</td>
        <td  align="left" valign="middle"><?php echo $row2['address'].', '.$row2['city'].' '.$row2['state'].' '.countryName($row2['country']); ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Email Address:</td>
        <td  align="left" valign="middle"><?php echo $row2['email']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Contact Phone Numnber:</td>
        <td  align="left" valign="middle"><?php echo $row2['phone']; ?> </td>
      </tr>
      <tr>
        <td align="left" valign="middle">Alternative Phone Numnber:</td>
        <td  align="left" valign="middle"><?php echo $row2['phone2']; ?></td>
      </tr>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      <tr>
        <td colspan="2"  class="tr-heading"  align="left" valign="middle"><strong><b>Guardians</b></strong></td>
      </tr>
     <?php
	$sql=$query = "SELECT * FROM student_guardian WHERE student_id = '$student' ORDER BY id DESC LIMIT 10";
	$result = mysqli_query($server, $sql);
	$num = mysqli_num_rows($result);

	$i=0;
	while($row = mysqli_fetch_assoc($result)){
		$id = $row['guardian_id'];
	 ?>
      <tr>
        <td colspan="2" align="left" valign="middle"><?php echo guardianData($id); ?></td>
      </tr>
      <?php $i++; } ?>
      <tr>
        <td align="left" valign="middle"><br /></td><td  align="left" valign="middle"></td>
      </tr>
      </table>
	</div>
  </div>
  </div>
<?php } ?>
</div>
