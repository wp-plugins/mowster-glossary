<?php

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}


$action = isset($_REQUEST['action'])? $_REQUEST['action'] : ''; 
$page = isset($_REQUEST['page'])? $_REQUEST['page'] : ''; 


global $wpdb; 
$table = $wpdb->prefix.MOWSTERG_TABLE;

?>
<div class="wrap">

	<?php	
	if ($page == MOWSTERG_ADD_ACTION) require_once( MOWSTERG_PLUGIN_PATH .'/tools/add_new.php');
	elseif ($action == 'delete_term') require_once( MOWSTERG_PLUGIN_PATH .'/tools/delete_term.php');
	elseif ($action == 'edit_term') require_once( MOWSTERG_PLUGIN_PATH .'/tools/edit_term.php');
	else require_once( MOWSTERG_PLUGIN_PATH .'/tools/list_terms.php');
	?>
</div>