<?php

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}

global $wpdb; 
$table = $wpdb->prefix.MOWSTERG_TABLE;	

if ($_REQUEST['action'] == 'delete_term' && $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM `$table` WHERE ID='".$_REQUEST['term']."'")) > 0) {

	$title = $wpdb->get_var($wpdb->prepare("SELECT Title FROM `$table` WHERE ID=".$_REQUEST['term'].""));

	$sql="DELETE FROM `$table` WHERE ID='".$_REQUEST['term']."'";
	$delete = $wpdb->query($sql);

	if ($delete){
		echo '<div id="message" class="updated fade"><p>'.sprintf( __('Term %s was deleted successfully!', 'mowsterGL'), '<span id="mowsterG_upper">'.strtoupper($title).'</span>').'</p></div>';		
		
		// single term in last term deleted
		$paged = $_REQUEST['paged']; $per_page = get_option('mowsterG_terms_per_page');
		$limit = (($paged - 1) * $per_page ) . ',' . $per_page;		
		$term = $wpdb->get_results("SELECT ID FROM `$table` LIMIT ".$limit."");
		
		if (count($term) == 0) $_REQUEST['paged'] = ($_REQUEST['paged'] - 1);

		require_once( MOWSTERG_PLUGIN_PATH . '/tools/list_terms.php' );
	} else {
		echo '<div id="message" class="updated fade"><p>'.__('mySQL error! It was not possible to delete the term from the database.', 'mowsterGL').'</p></div>';
		require_once( MOWSTERG_PLUGIN_PATH . '/tools/list_terms.php' );
	}
		
} else {

	echo '<div id="message" class="updated fade"><p>'.__('Error! Missing definition.', 'mowsterGL').'</p></div>';		
	require_once( MOWSTERG_PLUGIN_PATH . '/tools/list_terms.php' );		
}
	