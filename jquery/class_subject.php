<?php
ob_start();
define('SOA', true);
define('INDEX', 2);
include('../a.initiator.inc');
	
    $query="SELECT * FROM subject WHERE school_id = '$school_id' AND class_id = ".$_REQUEST['id']." OR class_id = 0 ORDER BY title ASC";
    $result = mysql_query($query);
    $num = mysql_num_rows($result);		
    ?> <option value="0">All Subjects</option><?php          
    for($i = 0; $i < $num; $i++){
    $g_id = mysql_result($result,$i,'id');
    $title = mysql_result($result,$i,'title');
    ?> 			
    <option value="<?php echo $g_id; ?>"><?php echo $title; ?></option>
    <?php  }   ?>
