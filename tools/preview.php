<?php

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}

global $wpdb; 
$table = $wpdb->prefix.MOWSTERG_TABLE;	

if ($_REQUEST['mowsterG_term_id']) {
	$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM `$table` WHERE Title='".strtoupper(strip_tags(stripslashes($_REQUEST["mowsterG_term"])))."' AND ID <> '".$_REQUEST['mowsterG_term_id']."'"));
	if ($count > 0) echo 'error|'. __('already exists!', 'mowsterGL');
	die();
} else {
	$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM `$table` WHERE Title='".strtoupper(strip_tags(stripslashes($_REQUEST["mowsterG_term"])))."'"));
	if ($count > 0) echo 'error|'. __('already exists!', 'mowsterGL');
	die();
}

?>