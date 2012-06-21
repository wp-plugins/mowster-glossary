<?php

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}


if (mowsterG_html_decode($_POST['submit']) == mowsterG_html_decode(__('Save', 'mowsterGL'))) {

	$checks = array(
		'mowsterG_shortcode', 'mowsterG_terms_per_page', 
		'mowsterG_html_before_term', 'mowsterG_html_after_term',
		'mowsterG_html_before_description', 'mowsterG_html_after_description',
		'mowsterG_text_previous_page', 'mowsterG_text_next_page',
		'mowsterG_end_size','mowsterG_mid_size',
		'mowsterG_pagination_word'
	);
	
	if ($_POST['mowsterG_shortcode'] != get_option('mowsterG_shortcode') 
		|| $_POST['mowsterG_pagination_word'] != get_option('mowsterG_pagination_word')) $flush_rules = 1;
	
	foreach ($checks as $check) {
		if ($_POST[$check] != get_option($check)) update_option($check, str_replace("\r\n",' ', $_POST[$check]));
	}

	if ($flush_rules == 1) { 
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
		unset($flush_rules); 
	}
	
	echo '<div id="message" class="updated fade"><p><strong>'. __('Saved!', 'mowsterGL').'</strong></p></div>';		
}

if (mowsterG_html_decode($_POST['submit']) == mowsterG_html_decode(__('Reset to defaults', 'mowsterGL'))) {
	mowsterG_defaults('defaults');

	echo '<div id="message" class="updated fade"><p><strong>'. __('Defaults restored!', 'mowsterGL').'</strong></p></div>';		
}

$action = isset($_REQUEST['action'])? $_REQUEST['action'] : ''; 
$page = isset($_REQUEST['page'])? $_REQUEST['page'] : ''; 

global $wpdb; 
$table = $wpdb->prefix.MOWSTERG_TABLE;
?>

<div class="wrap">
	<?php
		require_once( MOWSTERG_PLUGIN_PATH .'/header.php');
	?>
	<form method="post" id="admin_settings" action="" autocomplete="off">
	<table class="form-table">
	<h3><?php _e('Display', 'mowsterGL'); ?></h3>
		<tr valign="top">
				<th scope="row">
				<label for="mowsterG_shortcode">
					<?php _e('Shortcode', 'mowsterGL'); ?>
				</label>							
				</th>
				<td>
					<input type="text" name="mowsterG_shortcode" id="mowsterG_shortcode" value="<?php echo get_option('mowsterG_shortcode'); ?>" />
					<br /><?php printf(__('To display the glossary, add the following tag in posts and pages: %s', 'mowsterGL'), '<code>['.get_option('mowsterG_shortcode').']</code>'); ?>
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">
					<?php printf(__('Posts and Pages containing %s', 'mowsterGL'), '<br /><code>['.get_option('mowsterG_shortcode').']</code>'); ?>						
				</th>
				<?php
				global $wpdb;
				$table = $wpdb->prefix.'posts';					
				$result = $wpdb->get_results("SELECT ID,post_title,post_type FROM `".$table."` WHERE post_status='publish' AND post_content LIKE '%[".get_option('mowsterG_shortcode')."]%'");
				
				if ($result){
					echo '<td style="padding-left:25px;">';
					foreach ($result as $post) {
						echo '<li>'.$post->post_title.' <span id="page_id">['.$post->post_type.':'.$post->ID.']</span>'.'</li>';
						$url_part = get_permalink($post->ID);
					}
				} else echo '<td><em>'.__('No posts or pages contain the shortcode', 'mowsterGL').'</em>';
				?>
				</td>
		</tr>			
		<tr valign="top">
				<th scope="row">
				<label for="mowsterG_terms_per_page">
					<?php _e('Number of Terms per Page', 'mowsterGL');?>
				</label>							
				</th>
				<td>
					<input type="text" style="width: 40px;" name="mowsterG_terms_per_page" id="mowsterG_terms_per_page" value="<?php echo get_option('mowsterG_terms_per_page');?>" />		
				</td>
		</tr>		
		<tr valign="top">
				<th scope="row">										
				</th>
				<td>
					<input type="submit" name="submit" value="<?php _e('Save', 'mowsterGL'); ?>" class="button-primary" />
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">										
				</th>
				<td>
				
				</td>
		</tr>			
    </table>
	<table class="form-table">
	<h3><?php _e('Customize output', 'mowsterGL'); ?></h3>
		<tr valign="top">
				<th scope="row">
					<b><?php _e('Term', 'mowsterGL');?></b>
				</th>
				<td>
					
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">
				<label for="mowsterG_html_before_term">
					<?php _e('HTML to be inserted before', 'mowsterGL');?>
				</label>
				</th>
				<td>
					<input type="text" style="width: 360px;" name="mowsterG_html_before_term" id="mowsterG_html_before_term" value="<?php echo stripslashes(htmlentities(get_option('mowsterG_html_before_term'), ENT_COMPAT, MOWSTERG_CHARSET));?>" />		
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">
				<label for="mowsterG_html_after_term">
					<?php _e('HTML to be inserted after', 'mowsterGL');?>
				</label>
				</th>
				<td>
					<input type="text" style="width: 360px;" name="mowsterG_html_after_term" id="mowsterG_html_after_term" value="<?php echo stripslashes(htmlentities(get_option('mowsterG_html_after_term'), ENT_COMPAT, MOWSTERG_CHARSET));?>" />		
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">
					<b><?php _e('Description', 'mowsterGL');?></b>
				</th>
				<td>
					
				</td>
		</tr>		
		<tr valign="top">
				<th scope="row">
				<label for="mowsterG_html_before_description">
					<?php _e('HTML to be inserted before', 'mowsterGL');?>
				</label>
				</th>
				<td>
					<input type="text" style="width: 360px;" name="mowsterG_html_before_description" id="mowsterG_html_before_description" value="<?php echo stripslashes(htmlentities(get_option('mowsterG_html_before_description'), ENT_COMPAT, MOWSTERG_CHARSET));?>" />		
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">
				<label for="mowsterG_html_after_description">
					<?php _e('HTML to be inserted after', 'mowsterGL');?>
				</label>
				</th>
				<td>
					<input type="text" style="width: 360px;" name="mowsterG_html_after_description" id="mowsterG_html_after_description" value="<?php echo stripslashes(htmlentities(get_option('mowsterG_html_after_description'), ENT_COMPAT, MOWSTERG_CHARSET));?>" />		
				</td>
		</tr>			
		<tr valign="top">
				<th scope="row">										
				</th>
				<td>
					<input type="submit" name="submit" value="<?php _e('Save', 'mowsterGL'); ?>" class="button-primary" />
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">										
				</th>
				<td>
				
				</td>
		</tr>		
	</table>
	<table class="form-table">
	<h3><?php _e('Glossary navigation', 'mowsterGL'); ?></h3>
		<tr valign="top">
				<th scope="row">
				<label for="mowsterG_text_previous_page">
					<?php _e('Text For Previous Page', 'mowsterGL');?>
				</label>
				</th>
				<td>
					<input type="text" style="width: 160px;" name="mowsterG_text_previous_page" id="mowsterG_text_previous_page" value="<?php echo stripslashes(htmlentities(get_option('mowsterG_text_previous_page'), ENT_COMPAT, MOWSTERG_CHARSET));?>" />		
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">
				<label for="mowsterG_text_next_page">
					<?php _e('Text For Next Page', 'mowsterGL');?>
				</label>
				</th>
				<td>
					<input type="text" style="width: 160px;" name="mowsterG_text_next_page" id="mowsterG_text_next_page" value="<?php echo stripslashes(htmlentities(get_option('mowsterG_text_next_page'), ENT_COMPAT, MOWSTERG_CHARSET));?>" />		
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">
				<label for="mowsterG_end_size">
					<?php _e('End size', 'mowsterGL');?>
				</label>
				</th>
				<td>
					<input type="text" style="width: 60px;" name="mowsterG_end_size" id="mowsterG_end_size" value="<?php echo get_option('mowsterG_end_size');?>" />
					<br /><?php _e('How many numbers on either the start and the end list edges.', 'mowsterGL');?>
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">
				<label for="mowsterG_end_size">
					<?php _e('Mid size', 'mowsterGL');?>
				</label>
				</th>
				<td>
					<input type="text" style="width: 60px;" name="mowsterG_mid_size" id="mowsterG_mid_size" value="<?php echo get_option('mowsterG_mid_size');?>" />
					<br /><?php _e('How many numbers to either side of current page, but not including current page.', 'mowsterGL');?>
				</td>
		</tr>
		<?php if (get_option('permalink_structure')) { ?>
		<tr valign="top">
				<th scope="row">
				<label for="mowsterG_pagination_word">
					<?php _e('Pagination structure', 'mowsterGL');?>
				</label>
				</th>
				<td>
					<input type="text" style="width: 100px;" name="mowsterG_pagination_word" id="mowsterG_pagination_word" value="<?php echo stripslashes(htmlentities(get_option('mowsterG_pagination_word'), ENT_COMPAT, MOWSTERG_CHARSET));?>" />
					<br /><?php 
					if (!empty($url_part)) {
						echo '';
						_e('Page 2 URL example', 'mowsterGL');
						echo '<br />';
						echo $url_part.'<b>'.get_option('mowsterG_pagination_word').'</b>/2'; 
					}
					?>
				</td>
		</tr>		
		<?php } ?>
		<tr valign="top">
				<th scope="row">										
				</th>
				<td>
					<input type="submit" name="submit" value="<?php _e('Save', 'mowsterGL'); ?>" class="button-primary" />
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">										
				</th>
				<td>
				
				</td>
		</tr>	
	</table>
	<table class="form-table">
		<tr valign="top">
				<th scope="row">										
				</th>
				<td>
				
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">										
				</th>
				<td>
					<input type="submit" name="submit" value="<?php _e('Reset to defaults', 'mowsterGL'); ?>" class="clickable button" onClick="return confirmDefaults()"/>
				</td>
		</tr>
		<tr valign="top">
				<th scope="row">										
				</th>
				<td>
				
				</td>
		</tr>	
	</table>	
	<table class="form-table">
	<h3><?php _e('Management', 'mowsterGL'); ?></h3>
		<tr valign="top">
				<th scope="row">
				<label for="oprast_admin_users">
					<?php _e('Authorized users', 'mowsterGL'); ?>
				</label>							
				</th>
				<td style="padding-left: 25px;">
					<?php echo mowsterG_authorized_users(); ?>
				</td>
		</tr>	
	</table>
</div>