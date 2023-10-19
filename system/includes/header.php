<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');	
global $userID; global $LANG; global $server; global $PAGETITLE; global $URL;	
# ---------------------------------------------------------------------
#  Add all header scripts here
#  Please do not modify lines before 31
#----------------------------------------------------------------------
global $hooks;
$hooks->do_action('HeaderEvent');
$title = 'SOA';
$siteName = getSetting('name');
if(!empty($siteName))$title=$siteName;
$school_id = @$_SESSION['school_id'];
global $school_id;
if(getUser()>0) $title = $PAGETITLE[$URL];
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <!-- Core Framework Codes -->
        <base href="<?=home_base_url()?>">
        <title><?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="author" content="Ynet Interactive" />
		<link href="assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="assets/css/bootstrap.min.css?89" rel="stylesheet" type="text/css"/>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
		<link href="assets/plugins/sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/> 
        <link href="assets/plugins/featherlight/src/featherlight.css" type="text/css" rel="stylesheet" />
        <!--[if lt IE 9]>
        <script src="http://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="http://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- End of Core Framework Codes -->
        <link href="assets/css/froala_editor.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="assets/css/select2.css" type="text/css" />
        <link rel="stylesheet" type="text/css" href="assets/css/jquery.fancybox.css?v=2.1.5" media="screen" />
        <link href='assets/css/style.css?<?=time()?>' rel='stylesheet' type='text/css'>
       	<link href='assets/lchat/chat.css?<?=time()?>' rel='stylesheet' type='text/css'>        
        <!-- Framework Core Javascripts -->
        <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
        <script src="assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="assets/plugins/sweetalert/sweetalert.min.js"></script>      
        <!--- Load Current Theme ----- -->
        <?php
        $theme = getSetting('theme');
        if(!empty($theme)) {
            echo '<link href="assets/css/colors/'.$theme.'?'.time().'" rel="stylesheet" type="text/css">';	
        }
        ?>
        <link href='assets/css/tables.css?<?=time()?>' rel='stylesheet' type='text/css'>

    </head>
<?php 
$background = getSetting('background');
if(!empty($background)) {?>
	<style>
    body { background-image: url(media/images/<?php echo getSetting('background'); ?>); background-color: #333;  }
    </style>
<?php } ?>

<?php if(getUser()>0) { ?>
	<style>
    body { background-image: none; background-color: white;  }
@media print {    
    .no-print, .no-print * {
        display: none !important;
    }
@media screen {
table tr.resultsh th {text-align:center;}
.no-print, .no-print * {
        display: block !important;
    }
}
</style>     
<?php } ?>
<body>
<?php if(getUser()>0) {	?>
<audio id="audio2" src="<?=home_base_url()?>assets/lchat/notification.mp3" autoplay="false" ></audio>
<div id="header" style="position:fixed;top:0px;left:0px;z-index:9826276">
    <div id="responsive-menu" onclick="jQuery('#side-menu').toggle('show');"> <i class="fa fa-navicon"></i> </div>
<?php if(userRole(getUser())<5) { ?>
	<div id="logo"><img src="media/images/logo.png" alt="SOA" title="SOA" /></div>
<?php } else { ?>    
    <div id="logo" style="line-height:40px;"><?php echo $siteName; ?></div>
<?php } ?>    

<?php if(userRole(getUser())<5) { ?>
<a href="user_guide.pdf" title="Help & Documentation" target="_blank">
	<div id="userMail" style="margin-top: 7px;">
		<i class="fa fa-question-circle"></i>
	</div>
</a>
<a href="admin/ticket" title="Messages">
	<div id="userMail" style="margin-top: 7px;">
		<i class="fa fa-envelope"></i>
		<?php
		if(countNewNotice($userID, 0, userRole($userID), $school_id) > 0) {?>
		<counter><?php echo countNewNotice($userID, 0, userRole($userID), $school_id); ?></counter>
		<?php 	} 	?>
	</div>
</a>
<?php } else {?>   
<a href="userticket" title="Messages">
	<div id="userMail" style="margin-top: 7px;">
		<i class="fa fa-envelope"></i>
		<?php
		if(countNewNotice($userID, 0, userRole($userID), $school_id) > 0) {?>
		<counter><?php echo countNewNotice($userID, 0, userRole($userID), $school_id); ?></counter>
		<?php 	} 	?>
	</div>
</a>
<?php } ?>
</div>
<div id="body">
<style>
#add-new table tr td, #add-new table tr {
	height: 35px !important;
}
</style>
<?php } ?>
<?php if(!empty($message)) { showMessage($message, $class); } 

$page = 1; $setLimit = 30;
if(isset($_GET["page"])) $page = (int)$_GET["page"];
$pageLimit = ($page * $setLimit) - $setLimit;
?>