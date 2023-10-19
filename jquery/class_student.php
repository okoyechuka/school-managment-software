<?php
ob_start();
define('SOA', true);
define('INDEX', 2);
include('../a.initiator.inc');
 
     $query="SELECT * FROM students s JOIN student_class c ON c.student_id = s.id WHERE s.school_id = '$school_id' AND c.class_id = ".$_REQUEST['id']." ORDER BY first_name ASC";
    $result = mysql_query($query);
    $num = mysql_num_rows($result);		
              
    for($i = 0; $i < $num; $i++){
    $g_id = mysql_result($result,$i,'id');
    $title = studentName($g_id);
    ?> 			
    <option value="<?php echo $g_id; ?>"><?php echo $title; ?></option>
    <?php  }   ?>
