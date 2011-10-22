<?php

/*
	Plugin Name: mowsterGlossary
	Plugin URI: http://development.mowster.net
	Description: mowsterGlossary plugin is designed to give WordPress users an easy way to create and manage an online glossary of terms.
	Version: 2.0.1
	Author: PedroDM
	Author URI: http://jobs.mowster.net
*/

define('MOWSTERG_VERSION', 			'2.0.1');
define('MOWSTERG_URL_PATH', 		WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
define('MOWSTERG_PLUGIN_PATH',		realpath(dirname(__FILE__)));
define('MOWSTERG_TABLE',         	'mowster-glossary');
define('MOWSTERG_CHARSET',			get_bloginfo('charset'));

add_action('init', 'mowsterG_init');


function mowsterG_plugin_activate(){
	global $wpdb;
	
	$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix.MOWSTERG_TABLE."` (
			`ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`Title` varchar(255) NOT NULL,
			`Definition` text NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	
	$wpdb->query($sql); 
	
	if (!get_option('mowster_Glossary_Terms_Per_Page')) add_option('mowsterG_terms_per_page', 10);
	else { add_option('mowsterG_terms_per_page', get_option('mowster_Glossary_Terms_Per_Page')); delete_option('mowster_Glossary_Terms_Per_Page'); }

	mowsterG_defaults('activate'); 
	if (!get_option('mowsterG_pagination_word')) add_option('mowsterG_pagination_word', 'mowsterG');
	mowsterG_flush_rules();
}
register_activation_hook(__FILE__,'mowsterG_plugin_activate');

function mowsterG_plugin_uninstall(){
	global $wpdb;
	
	$sql = "DROP TABLE `".$wpdb->prefix.MOWSTERG_TABLE."`";
	$wpdb->query($sql); 
	
	mowsterG_defaults('uninstall');
	delete_option('mowsterG_pagination_word');
}
register_uninstall_hook(__FILE__,'mowsterG_plugin_uninstall');

function mowsterG_init(){
	load_plugin_textdomain('mowsterGL', false, basename(rtrim(dirname(__FILE__), '/')) . '/langs');
	
	add_action('wp_ajax_join_mowsterG_add_preview', 'join_mowsterG_add_preview');
	
	add_filter('the_content', 'mowsterG_shortcode_function', 99);
}

function mowsterG_styles(){
	wp_enqueue_style('mowsterG', MOWSTERG_URL_PATH . 'styles/glossary.css', '', MOWSTERG_VERSION);
}

function mowsterG_tools_scripts(){

	if (function_exists('wp_tiny_mce')) {

		add_filter('teeny_mce_before_init', create_function('$a', '

			$a["theme"] = "advanced";
			$a["skin"] = "wp_theme";
			$a["height"] = "250";
			$a["width"] = "600";
			$a["onpageload"] = "";
			$a["mode"] = "exact";
			$a["elements"] = "new_mowsterG_definition";
			$a["editor_selector"] = "theEditor";
			$a["theme_advanced_buttons1"] = "bold,italic,underline,separator,bullist,numlist,separator,undo,redo,separator,copy,paste,separator,link,unlink,separator,pasteword,pastetext,separator,spellchecker,separator,fullscreen,separator,code";
			$a["plugins"] = "fullscreen,inlinepopups,spellchecker,tabfocus,paste,wordpress,wplink,wpdialogs";		
			
			$a["forced_root_block"] = false;
			$a["force_br_newlines"] = true;
			$a["force_p_newlines"] = false;
			$a["convert_newlines_to_brs"] = true;
			
			$a["apply_source_formatting"] = true;
			$a["accessibility_focus"] = true;
		
			
			$a["handle_node_change_callback"] = "update_tinycme_border";

			return $a;'));

		wp_tiny_mce(true, array("editor_selector" => "theEditor"));
	}
	

		
	if ($_REQUEST['action'] == 'new_term' && isset($_POST['submit']) === false) wp_enqueue_script('mowsterGL_js-new_term', MOWSTERG_URL_PATH . 'js/add.js', '', MOWSTERG_VERSION);
	elseif ($_REQUEST['action'] == 'edit_term' && isset($_POST['submit']) === false) wp_enqueue_script('mowsterGL_js-edit_term', MOWSTERG_URL_PATH . 'js/edit.js', '', MOWSTERG_VERSION);
	else wp_enqueue_script('mowsterGL_js-list', MOWSTERG_URL_PATH . 'js/list.js', '', MOWSTERG_VERSION);
	
	$mowsterG_new_edit_term = array(
		'mowsterG_term_lenght_error' => __('Term must be lower than 255 chars.', 'mowsterGL'),
		'mowsterG_definition_error' => __('Definition cannot be blank.', 'mowsterGL'),
		'mowsterG_admin_url' => get_admin_url(),
	);
	wp_localize_script('mowsterGL_js-new_term', 'mowsterG', $mowsterG_new_edit_term);
	wp_localize_script('mowsterGL_js-edit_term', 'mowsterG', $mowsterG_new_edit_term);

	$mowsterG_list = array(
		'mowsterG_terms_per_page_warning' => __('Atention! This action will change the numbers of terms displayed in the public glossary. Show ', 'mowsterGL'),
		'mowsterG_terms' => __('terms per page', 'mowsterGL'),
		'mowsterG_term_confirm' => __('Erease the term', 'mowsterGL')
	);
	wp_localize_script('mowsterGL_js-list', 'mowsterG_list', $mowsterG_list);
}

function mowsterG_admin_scripts(){

	wp_enqueue_script('mowsterGL_js-options', MOWSTERG_URL_PATH . 'js/options.js', '', MOWSTERG_VERSION);
	
	$mowsterG_options = array(
		'mowsterG_default_confirm' => __('Are you sure you want to restore the dafaults', 'mowsterGL')
	);
	wp_localize_script('mowsterGL_js-options', 'mowsterG_options', $mowsterG_options);
}

function mowsterG_admin_menu(){	
	$tools_page = add_submenu_page('tools.php', __('Glossary','mowsterGL'), __('Glossary','mowsterGL'), 5, __FILE__, 'mowsterG_tools_menu');
	$admin_page = add_submenu_page('options-general.php', 'mowsterGlossary', 'mowsterGlossary', 10, __FILE__, 'mowsterG_options_menu');
	
	add_action('admin_print_styles-' . $tools_page, 'mowsterG_styles'); 
	add_action('admin_print_scripts-' . $tools_page, 'mowsterG_tools_scripts');
	
	add_action('admin_print_styles-' . $admin_page, 'mowsterG_styles'); 
	add_action('admin_print_scripts-' . $admin_page, 'mowsterG_admin_scripts');
}
add_action('admin_menu', 'mowsterG_admin_menu');


function mowsterG_tools_menu(){
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


function mowsterG_url_list($rules){
	$newrules = array();
	
	global $wpdb;
	$table = $wpdb->prefix.'posts';					
	$result = $wpdb->get_results("SELECT ID, post_type, post_name FROM `".$table."` WHERE post_status='publish' AND post_content LIKE '%[".get_option('mowsterG_shortcode')."]%'");	
	
	if (!empty($result)){
		foreach ($result as $post){
			if ($post->post_type == 'page'){
				$newrules[get_page_uri($post->ID).'/'.get_option('mowsterG_pagination_word').'/([0-9]+)/?$'] = 'index.php?page_id='.$post->ID.'&pagename='.$post->post_name.'&mowsterG=$matches[1]';
				
			} else {
				$permalink_structure = get_option('permalink_structure');
				$rule = str_replace(array('%year%'), '([0-9]{4})', $permalink_structure);
				$rule = str_replace(array('%monthnum%', '%day%', '%hour%', '%minute%', '%second%'), '([0-9]{1,2})', get_option('permalink_structure'));
				$rule = str_replace(array('%post_id%'), '([0-9]+)', get_option('permalink_structure'));				
				$rule = str_replace(array('%category%'), '(.+?)', $rule);
				$rule = str_replace(array('%author%'), '([^/]+)', $rule);
				$rule = str_replace('%postname%', '('.$post->post_name.')', $rule);
				$rule = trim($rule, '/');
				

				$gets = explode('/', trim($permalink_structure, '/'));

				$match = 0; $url = 'index.php?';
				foreach ($gets as $get) {
					$match++;
					if ($get == '%year%') $url .= 'year=$matches['.$match.']&'; 
					elseif ($get == '%monthnum%') $url .= 'monthnum=$matches['.$match.']&';
					elseif ($get == '%day%') $url .= 'day=$matches['.$match.']&';
					elseif ($get == '%hour%') $url .= 'hour=$matches['.$match.']&';
					elseif ($get == '%minute%') $url .= 'minute=$matches['.$match.']&';
					elseif ($get == '%second%') $url .= 'second=$matches['.$match.']&';
					elseif ($get == '%post_id%') $url .= 'p=$matches['.$match.']&';
					elseif ($get == '%postname%') $url .= 'name=$matches['.$match.']&';
					elseif ($get == '%category%') $url .= 'category_name=$matches['.$match.']&';
					elseif ($get == '%tag%') $url .= '%tag%&';
					elseif ($get == '%author%') $url .= 'author_name=$matches['.$match.']&';
					elseif ($get == '%author%') $url .= 'author_name=$matches['.$match.']&';
				}				

				$newrules[$rule.'/'.get_option('mowsterG_pagination_word').'/([0-9]+)/?$'] = $url.'mowsterG=$matches['.($match+1).']';
				$newrules[$rule.'/([0-9]+)/'.get_option('mowsterG_pagination_word').'/([0-9]+)/?$'] = $url.'page=$matches['.($match+1).']&mowsterG=$matches['.($match+2).']';
			}
		}
	}
	
    return $newrules + $rules;
}
add_filter('rewrite_rules_array', 'mowsterG_url_list');


function mowsterG_queryvars($query_vars){
	array_push($query_vars, 'mowsterG');

	return $query_vars;
}
add_filter('query_vars', 'mowsterG_queryvars');


function mowsterG_wp_insert_post_data($data){
	if ($data['post_status'] == 'publish') {

		global $post;
		$shortcode = get_option('mowsterG_shortcode');
		
		if ((substr_count($post->post_content, $shortcode) == 0 
			&& substr_count($data['post_content'], $shortcode) > 0) || 
			(substr_count($post->post_content, $shortcode) > 0 
			&& substr_count($data['post_content'], $shortcode) == 0)) mowsterG_flush_rules();
	}

	return $data;
}
add_filter('wp_insert_post_data', 'mowsterG_wp_insert_post_data');


function mowsterG_defaults($action){
	$defaults = array (
		'mowsterG_terms_per_page' => 10,
		'mowsterG_shortcode' => 'glossary',
		'mowsterG_html_before_term' => '<h3><strong>',
		'mowsterG_html_after_term' => '</strong></h3>',
		'mowsterG_html_before_description' => '<p>',
		'mowsterG_html_after_description' => '</p>',
		'mowsterG_text_previous_page' => '&laquo;',
		'mowsterG_text_next_page' => '&raquo;',
		'mowsterG_end_size' => 1,
		'mowsterG_mid_size' => 2
	);
		
	foreach ($defaults as $key => $value) {
		if ($action == 'activate' && (!get_option($key))) add_option($key, $value);
		elseif ($action == 'defaults') update_option($key, $value);
		elseif ($action == 'uninstall') delete_option($key);
	}

	return;
}

function mowsterG_flush_rules(){
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
	
	return;
}

?>