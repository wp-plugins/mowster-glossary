<?php

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}

if (mowsterG_html_decode($_POST['submit']) == mowsterG_html_decode(__('Confirm &raquo;', 'mowsterGL'))) {
	global $wpdb; 
	$table = $wpdb->prefix.MOWSTERG_TABLE;	

	$update = $wpdb->update( 
		$table, 
		array( 
			'Title' => strtoupper(strip_tags(stripslashes($_REQUEST['edit_term']))),
			'Definition' => stripslashes($_REQUEST['edit_mowsterG_definition'])
		), 
		array( 'ID' => $_REQUEST['edit_term_id'] ), 
		array( '%s', '%s' )
	);
	
	require_once( MOWSTERG_PLUGIN_PATH . '/header.php');
	
	if ($update) {
		echo '<div id="message" class="updated fade"><p>'.sprintf( __('Term %s was updated successfully!', 'mowsterGL'), '<span id="mowsterG_upper">'.strtoupper($_REQUEST["edit_term"]).'</span>').'</p></div>';
		
		// locate new term
		$terms = $wpdb->get_results("SELECT Title FROM `$table` ORDER BY Title");
		while(current($terms)->Title != strtoupper(strip_tags(stripslashes($_REQUEST["edit_term"])))) {
			next($terms);
			$c++;
		}
		$_REQUEST['paged'] = ceil(($c + 1) / get_option('mowsterG_terms_per_page'));
		
		require_once( MOWSTERG_PLUGIN_PATH . '/tools/list_terms.php' );
	}
	else {
		echo '<div id="message" class="updated fade"><p>'.__('mySQL error! It was not possible to update the term in the database.', 'mowsterGL').'</p></div>';
		require_once( MOWSTERG_PLUGIN_PATH . '/tools/list_terms.php' );
	}
	
} else {

require_once( MOWSTERG_PLUGIN_PATH . '/header.php');

$term = $wpdb->get_row("SELECT * FROM `$table` WHERE ID='".$_REQUEST['term']."'");

?>
<form method="post" id="mowsterG_edit_preview" action="" autocomplete="off">
	
	<div id="poststuff" class="metabox-holder">
		<div id="formatdiv" class="postbox" style="width: 600px;">
			<h3 class="handle">
				<span><?php _e('Edit term', 'mowsterGL'); ?> : <span class="edit_term_show"><?php echo $term->Title; ?></span></span>
			</h3>
		</div>
	</div>

	<div class="inside">
		<fieldset>				
			<input type="text" id="edit_term" name="edit_term" value="<?php echo $term->Title; ?>" />
			<div id="edit_term_show"></div>
			<div id="edit_term_error"></div>
			
			<div id="td_mowsterG_definition">
				<?php 		
				$tiny_mce = mowsterG_tiny_mce_settings();								
				wp_editor($term->Definition, 'new_mowsterG_definition', array('wpautop' => true, 'textarea_name' => 'edit_mowsterG_definition', 'media_buttons' => false, "tinymce" => $tiny_mce)); 
				?>
			</div>
			<div id="edit_definition_show"></div>
					
			<div class="submit">	
				<input type="submit" name="submit" id="edit_term_st" value="<?php _e('Edit', 'mowsterGL'); ?>" /><img src="<?php echo admin_url(); ?>images/wpspin_light.gif" alt="" class="ajaxsave" style="display: none;" />
				<input type="button" name="edit_term_edit" id="edit_term_edit" value="<?php _e('Edit', 'mowsterGL'); ?>" style="display: none;" />
				<input type="submit" name="submit" id="edit_term_submit" class="button-primary" value="<?php _e('Confirm &raquo;', 'mowsterGL'); ?>" style="display: none;" />
				<input type="hidden" name="edit_term_id" id="edit_term_id" value="<?php echo $term->ID; ?>" />
			</div>	
		</fieldset>
	</div>

</form>

<?php } ?>