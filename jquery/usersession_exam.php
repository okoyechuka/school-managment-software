<?php
ob_start();
define('SOA', true);
define('INDEX', 2);
include('../initiator.inc');
	$id = $_REQUEST['id'];
    $query="SELECT * FROM exams WHERE school_id = '$school_id' AND session_id = '$id' ORDER BY id DESC";
    $result = mysql_query($query);
    $num = mysql_num_rows($result);		
              
    for($i = 0; $i < $num; $i++){
    $g_id = mysql_result($result,$i,'id');
    $title = mysql_result($result,$i,'title');
    ?> 			
    <option value="<?php echo $g_id; ?>"><?php echo $title; ?></option>
    <?php  }   ?>
	<option value="0"><?=sessionName($id) ?> Cumulative Result</option>