<?php defined('SMSPanel') or die('Snooping around is not allowed. Please use the front door'); 
/* 
File name: 		csvexport.php
Description:	This is scripts that does all export to csv functions
Developer: 		Ynet Interactive
Date: 			26/10/2014
*/

?>
    <div id="csv-export">
        <form method="post" target="_blank" action="" id="csv">
            <input type="hidden" name="csv" value="1" />
             <a onclick="document.getElementById('csv').submit(); return false;" href="javascript:{}" title="Export to CSV">
             <img src="media/images/excel.jpg" />Export to CSV
             </a>    	
        </form>     
    </div>