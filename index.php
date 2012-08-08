<?php
/*
	Plugin Name: mowsterGlossary
	Plugin URI: http://wordpress.mowster.net
	Description: mowsterGlossary plugin is designed to give WordPress users an easy way to create and manage an online glossary of terms.
	Author: mowster
	Version: 2.4.2
	License: GPLv2 or later
	Author URI: http://jobs.mowster.net
*/

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}

define('MOWSTERG_VERSION', 			'2.4.2');
define('MOWSTERG_URL_PATH', 		WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
define('MOWSTERG_PLUGIN_PATH',		realpath(dirname(__FILE__)));
define('MOWSTERG_TABLE',         	'mowster-glossary');
define('MOWSTERG_CHARSET',			get_bloginfo('charset'));
define('MOWSTERG_MAIN_ACTION',		'mowsterG');
define('MOWSTERG_ADD_ACTION',		'mowsterGadd');


add_action('init', 'mowsterG_init');

function mowsterG_init(){
	load_plugin_textdomain('mowsterGL', false, basename(rtrim(dirname(__FILE__), '/')) . '/langs');
	
	if (is_admin()) require_once( MOWSTERG_PLUGIN_PATH . '/users.php' );
	
	require_once( MOWSTERG_PLUGIN_PATH . '/functions.php' );
	
	add_action('wp_ajax_join_mowsterG_add_preview', 'join_mowsterG_add_preview');
	
	add_filter('the_content', 'mowsterG_shortcode_function', 99);
}



// admin menu
function mowsterG_styles(){
	wp_enqueue_style('mowsterG', MOWSTERG_URL_PATH . 'styles/glossary.css', '', MOWSTERG_VERSION);
}

function mowsterG_tools_scripts(){


	$mowsterG_new_edit_term = array(
		'mowsterG_term_default' => __('Type here the new term', 'mowsterGL'),
		'mowsterG_term_lenght_error' => __('Term must be lower than 255 chars.', 'mowsterGL'),
		'mowsterG_definition_error' => __('Definition cannot be blank.', 'mowsterGL'),
		'mowsterG_admin_url' => get_admin_url(),
	);
	
	$mowsterG_list = array(
		'mowsterG_terms_per_page_warning' => __('Atention! This action will change the numbers of terms displayed in the public glossary. Show ', 'mowsterGL'),
		'mowsterG_terms' => __('terms per page', 'mowsterGL'),
		'mowsterG_term_confirm' => __('Erease the term', 'mowsterGL')
	);	
		
	if ($_REQUEST['page'] == MOWSTERG_ADD_ACTION && isset($_POST['submit']) === false) {
		wp_enqueue_script('mowsterGL_js-new_term', MOWSTERG_URL_PATH . 'js/add.js', array('jquery'), MOWSTERG_VERSION);
		
		wp_localize_script('mowsterGL_js-new_term', 'mowsterG', $mowsterG_new_edit_term);
	}
	elseif ($_REQUEST['action'] == 'edit_term' && isset($_POST['submit']) === false) {
		wp_enqueue_script('mowsterGL_js-edit_term', MOWSTERG_URL_PATH . 'js/edit.js', array('jquery'), MOWSTERG_VERSION);
		
		wp_localize_script('mowsterGL_js-edit_term', 'mowsterG', $mowsterG_new_edit_term);		
	} else {
		wp_enqueue_script('mowsterGL_js-list', MOWSTERG_URL_PATH . 'js/list.js', array('jquery'), MOWSTERG_VERSION);
		
		wp_localize_script('mowsterGL_js-list', 'mowsterG_list', $mowsterG_list);
	}
	
}

function mowsterG_admin_scripts(){


	wp_enqueue_script('jquery');

	wp_enqueue_script('mowsterGL_js-options', MOWSTERG_URL_PATH . 'js/options.js', array('jquery'), MOWSTERG_VERSION);
	
	$mowsterG_options = array(
		'mowsterG_default_confirm' => __('Are you sure you want to restore the dafaults', 'mowsterGL')
	);
	wp_localize_script('mowsterGL_js-options', 'mowsterG_options', $mowsterG_options);
}

function mowsterG_admin_menu(){	

	$admin_page = add_submenu_page('options-general.php', 'mowsterGlossary', 'mowsterGlossary', 10, __FILE__, 'mowsterG_options_menu');
	add_action('admin_print_styles-' . $admin_page, 'mowsterG_styles'); 
	add_action('admin_print_scripts-' . $admin_page, 'mowsterG_admin_scripts');
	
	
	global $current_user;
	
	if (($current_user->roles[0] == 'administrator') || get_user_meta($current_user->ID, 'mowsterG_user_access', true) == 'yes') {
		
		$menu_page = add_utility_page( __('Glossary','mowsterGL'), __('Glossary','mowsterGL'), 2, MOWSTERG_MAIN_ACTION, 'mowsterG_tools_menu', MOWSTERG_URL_PATH . '/images/menu_icon.png' );
		add_action('admin_print_styles-' . $menu_page, 'mowsterG_styles'); 
		add_action('admin_print_scripts-' . $menu_page, 'mowsterG_tools_scripts');

		$menu[1] = add_submenu_page( MOWSTERG_MAIN_ACTION , __('Terms','mowsterGL'), __('Terms','mowsterGL'), 2, MOWSTERG_MAIN_ACTION, 'mowsterG_tools_menu');
		$menu[2] = add_submenu_page( MOWSTERG_MAIN_ACTION , __('Add term','mowsterGL'), __('Add term','mowsterGL'), 2, MOWSTERG_ADD_ACTION, 'mowsterG_tools_menu_add');	

		for ($i = 1; $i <= count($menu); $i++) {
			add_action('admin_print_styles-' . $menu[$i], 'mowsterG_styles'); 
			add_action('admin_print_scripts-' . $menu[$i], 'mowsterG_tools_scripts');
		}
		
	}
	
}
add_action('admin_menu', 'mowsterG_admin_menu');



// rewrite rules
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
				$rule = str_replace(array('%monthnum%', '%day%', '%hour%', '%minute%', '%second%'), '([0-9]{1,2})', $rule);
				$rule = str_replace(array('%post_id%', '%category%', '%author%', '%postname%'), array('([0-9]+)', '(.+?)', '([^/]+)', '('.$post->post_name.')'), $rule);
				$rule = trim($rule, '/');

				$gets = explode('/', trim($permalink_structure, '/'));

				$match = 0; $url = 'index.php?';
				foreach ($gets as $get) {
					
					switch ($get) {
						case "%year%":
							$url .= 'year=$matches['.$match.']&'; break;
						case "%monthnum%":
							$url .= 'monthnum=$matches['.$match.']&'; break;
						case "%day%":
							$url .= 'day=$matches['.$match.']&'; break;
						case "%day%":
							$url .= 'day=$matches['.$match.']&'; break;
						case "%hour%":
							$url .= 'hour=$matches['.$match.']&'; break;
						case "%minute%":
							$url .= 'minute=$matches['.$match.']&'; break;
						case "%second%":
							$url .= 'second=$matches['.$match.']&'; break;	
						case "%post_id%":
							$url .= 'p=$matches['.$match.']&'; break;
						case "%postname%":
							$url .= 'name=$matches['.$match.']&'; break;
						case "%category%":
							$url .= 'category_name=$matches['.$match.']&'; break;
						case "%tag%":
							$url .= '%tag%&'; break;
						case "%author%":
							$url .= 'author_name=$matches['.$match.']&'; break;								 
					}	
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



// insert post data
function mowsterG_wp_insert_post_data($data){
	if ($data['post_status'] == 'publish') {

		global $post;
		$shortcode = get_option('mowsterG_shortcode');
		
		// shortcode added or removed from a post / page
		if ((substr_count($post->post_content, $shortcode) == 0 && substr_count($data['post_content'], $shortcode) > 0) || 
			(substr_count($post->post_content, $shortcode) > 0 && substr_count($data['post_content'], $shortcode) == 0)) {
				global $wp_rewrite;
				$wp_rewrite->flush_rules();			
			}	
	}

	return $data;
}
add_filter('wp_insert_post_data', 'mowsterG_wp_insert_post_data', 99, 2);



// install / uninstall
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
	
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
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

?>