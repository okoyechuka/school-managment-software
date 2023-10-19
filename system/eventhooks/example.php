<?php
/*
*	This is an example event hook that runs each time a customer is created or signed up
*	This is just a dummy hook to explain how sendroid event hooks are used
*	Please do not use this feature if you do not have enough programming background
*
*/

global $hooks;
$hooks->add_action('CustomerCreate','notify_admin');
function notify_admin(){
	$vars = $_SESSION['EventVals'];
	$id = $vars['id'];
	$first_name = $vars['first_name'];
	$last_name = $vars['last_name'];
	$email = $vars['email'];
	$password = $vars['password'];
    //echo 'A new user just signed up with email '.$email;
}
?>