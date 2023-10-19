<?php
global $hooks;
if(userRole(getUser()) < 3) {
	$hooks->add_action('AddMenuExam','custome_report_kpis');
}

$hooks->add_action('ReportCardFooter','custome_report_kpis_view');

function custome_report_kpis() {
	echo "<a href=\"admin/customreportdata\"><span class=\"link sublink"; echo "\"> Custom Report-Card Data</span></a>";	
}
function custome_report_kpis_view() {
	global $server;
	$vars = $_SESSION['EventVals'];
	if(isset($_POST['update_kpi_stu'])) {
		foreach ($_POST as $key => $value ){
			$$key = mysqli_real_escape_string($server,$value);
		}
		foreach($_POST['kpi_ress'] as $reportcard_extras_id => $kpi_ress){
			$kpi_ress = mysqli_real_escape_string($server,$_POST['kpi_ress'][$reportcard_extras_id]);
			mysqli_query($server,"DELETE FROM reportcard_extras_values WHERE reportcard_extras_id = '$reportcard_extras_id' AND exam_id = '$kpi_exam_id' AND student_id = '$kpi_student_id'");
			mysqli_query($server,"INSERT INTO reportcard_extras_values (`student_id`, `exam_id`, `value`, `reportcard_extras_id`)
		VALUES ('$kpi_student_id', '$kpi_exam_id', '$kpi_ress', '$reportcard_extras_id');");
			
		}
	}
	if(($vars['student_id']>0 && $vars['exam_id']>0) ){
		$exam_id = $vars['exam_id'];
		$student_id = $vars['student_id'];
		$session_id = $vars['session_id'];
		$class_id = getClass($student_id,$session_id);
		$school_id = $_SESSION['school_id']; 
		//display titles and values for the student and exam
		?>
		<br /><br />
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
             <tr>
                <th scope="col" colspan="3"> <strong>Other Performance Reports</strong></th>
              </tr>
              <tr class="inner">
              <td scope="col" colspan="3">
				<?php
				$sql="SELECT * FROM reportcard_extras_values WHERE exam_id = '$exam_id' AND student_id = '$student_id' ORDER BY reportcard_extras_id DESC";
				$result = mysqli_query($server, $sql);
				$num = mysqli_num_rows($result);			
				$i=0;
				while($row = mysqli_fetch_assoc($result)){
					if(!empty($row['value'])) {
			?>
            	<div style="width: 49%;min-width: 300px;display:block;float:left;">
                	<div style="width: 5%;padding:10px;display:block;float:left;"><?=$i+1?></div>
                    <div style="width: 60%;padding:10px;display:block;float:left;font-weight:bold;"><?=cusKPIName($row['reportcard_extras_id'])?></div>
                    <div style="width: 30%;padding:10px;display:block;float:left;"><?=$row['value']?></div>
                </div>
             <?php $i++; } } ?> 
             </td>
             </tr>
             <?php if($i<1) {
				 echo '<tr>
                <td scope="col" colspan="3"> There are no performance reports to show for this student</th>
              	</tr>';
			 }
             if(userRole(getUser()) <= 4) {
	        ?> <tr class="inner"><td scope="col" colspan="3"><br><center><p class='no-print'><button class='btn btn-primary' onclick="$('#add-new').show();">Update Performance Reports</button></p></center></td> </tr> <?php } ?>
           </table>
       <?php       
		if(userRole(getUser()) <= 4) {
	        ?> 
            <div id="add-new" style="display:none;">
			   <div id="add-new-head">Update Record    <a href="javascript:{}" title="Close" id="closeBox" onClick="document.getElementById('add-new').style.display='none';"><div class="close">&times;</div></a></div>
               <div class="inside" style="background-color: #fff;">
			    <form method="post" action="">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="60%" align="left" valign="middle">Performance:</td>
                        <td align="left" valign="middle">Score</td>
                    </tr>
                    <?php
				   $query="SELECT * FROM reportcard_extras WHERE school_id = '$school_id' AND (class_id = '0' OR class_id = '$class_id') ORDER BY id DESC";
					$result = mysqli_query($server, $query);
					$num = mysqli_num_rows($result);
					while($row = mysqli_fetch_assoc($result)){
						if($row['parent_only']>0) {
							echo '<tr><td colspan="2" align="left" valign="middle">'.$row['title'].'</td><tr>';
						} else {
						?>
						<tr>
						<td align="left" valign="middle"><?=$row['title']?></td>
						<td align="left" valign="middle">
						<input type="text" name="kpi_ress[<?=$row['id']?>]" required="required"  value="<?=get_students_kpi_score($exam_id,$student_id,$row['id'])?>">
						</td>
						</tr>
					<?php	}  }	?>    
                    <tr>
                     <td align="left" valign="top">&nbsp;</td>
                     <td width="69%" align="left" valign="top">
	                    <input type="hidden" name="update_kpi_stu" value="yes" />
                        <input type="hidden" name="kpi_student_id" value="<?=$student_id?>" />
                        <input type="hidden" name="kpi_exam_id" value="<?=$exam_id?>" />
	                    <button class="btn btn-success" onClick="document.getElementById('login-loading').style.visibility='visible'; return true;" name="admit" value="1" type="submit">Update Record</button>
            			<div id="login-loading"><i class="fa fa-spinner fa-spin"></i> Saving Changes...</div>
                       </td>
                    </tr>
                   </table> 
                 </form>       
                </div>
            </div>
            <?php
		}
	}
}

function get_students_kpi_score($exam,$student,$kpi) {
	global $server;
	$query="SELECT * FROM reportcard_extras_values WHERE exam_id = '$exam' AND student_id = '$student' AND reportcard_extras_id = '$kpi' LIMIT 1";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	return $row['value'];	
}
function cusKPIName($id) {
	global $server;
	$query="SELECT * FROM reportcard_extras WHERE id = '$id' LIMIT 1";
	$result = mysqli_query($server, $query);
	$row = mysqli_fetch_assoc($result);
	return $row['title'];
}
?>