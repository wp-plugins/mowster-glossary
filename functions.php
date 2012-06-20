<?php

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}


function mowsterG_tools_menu(){
	require_once( MOWSTERG_PLUGIN_PATH . '/tools.php' );
}

function mowsterG_tools_menu_add(){
	require_once( MOWSTERG_PLUGIN_PATH . '/tools.php' );
}

function mowsterG_options_menu(){
	require_once( MOWSTERG_PLUGIN_PATH . '/options.php' );
}

function mowsterG_shortcode_function($content){
	require_once( MOWSTERG_PLUGIN_PATH . '/shortcode.php' );
	return $content;
}

function join_mowsterG_add_preview(){
	require_once( MOWSTERG_PLUGIN_PATH . '/tools/preview.php' );
}

function mowsterG_html_decode($string){
	return html_entity_decode($string, ENT_QUOTES, "".MOWSTERG_CHARSET."");
}



function mowsterG_tiny_mce_settings(){
	$tiny_mce["theme"] = "advanced";
	$tiny_mce["skin"] = "wp_theme";
	$tiny_mce["height"] = "250";
	$tiny_mce["width"] = "100%";
	$tiny_mce["onpageload"] = "";
	$tiny_mce["mode"] = "exact";
	$tiny_mce["editor_selector"] = "theeditor";
	$tiny_mce["theme_advanced_buttons1"] = "bold,italic,underline,separator,bullist,numlist,separator,undo,redo,separator,copy,paste,separator,link,unlink,separator,pasteword,pastetext,separator,spellchecker,separator,fullscreen";
	$tiny_mce["theme_advanced_buttons2"] = "";
	$tiny_mce["theme_advanced_buttons3"] = "";
	$tiny_mce["plugins"] = "fullscreen,inlinepopups,spellchecker,tabfocus,paste,wordpress,wplink,wpdialogs";		
	
	$tiny_mce["forced_root_block"] = false;
	$tiny_mce["force_br_newlines"] = false;
	$tiny_mce["force_p_newlines"] = false;
	$tiny_mce["convert_newlines_to_brs"] = false;
	
	$tiny_mce["apply_source_formatting"] = true;
	$tiny_mce["accessibility_focus"] = true;

	$tiny_mce["handle_node_change_callback"] = "update_tinycme_border";	
	
	return $tiny_mce;
}


function mowsterG_authorized_users(){
	global $wpdb; 
	$wp_user_search = $wpdb->get_results("SELECT ID FROM $wpdb->users ORDER BY ID");
	
	unset($return);
	foreach ($wp_user_search as $user_id) {
		$user = get_userdata($user_id->ID);		
		if ($user->user_level == 10 || get_user_meta($user_id->ID, 'mowsterG_user_access', true) == 'yes'){
			if (current_user_can('list_users')) {
				$link1 = '<a href="'.admin_url().'/user-edit.php?user_id='.$user_id->ID.'">';
				$link2 = '</a>';
			}
			$return .= '<li>'. $link1 . $user->display_name . $link2 .' [ ' . $user_id->ID .' ]</li>';
		}
	}
	return $return;
}

?>