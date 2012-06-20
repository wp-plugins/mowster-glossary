<?php

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}

if (mowsterG_html_decode($_POST['submit']) == mowsterG_html_decode(__('Add &raquo;', 'mowsterGL'))) {
	global $wpdb; 
	$table = $wpdb->prefix.MOWSTERG_TABLE;	
	
	
	$insert = $wpdb->insert( 
					$table, 
					array( 
						'Title' => strtoupper(strip_tags(stripslashes($_REQUEST["new_term"]))), 
						'Definition' => stripslashes($_REQUEST["new_mowsterG_definition"])
					), 
					array( 
						'%s', 
						'%s' 
					));
	
	require_once( MOWSTERG_PLUGIN_PATH . '/header.php');
	
	if ($insert == 1) {
		echo '<div id="message" class="updated fade"><p>'.sprintf( __('Term %s was saved successfully!', 'mowsterGL'), '<span id="mowsterG_upper">'.strtoupper($_REQUEST["new_term"]).'</span>').'</p></div>';
		
		// locate new term
		$terms = $wpdb->get_results("SELECT Title FROM `$table` ORDER BY Title");
		while(current($terms)->Title != strtoupper(strip_tags(stripslashes($_REQUEST["new_term"])))) {
			next($terms);
			$c++;
		}
		$_REQUEST['paged'] = ceil(($c + 1) / get_option('mowsterG_terms_per_page'));
		require_once( MOWSTERG_PLUGIN_PATH . '/tools/list_terms.php' );
	}
	else {
		echo '<div id="message" class="updated fade"><p>'.__('mySQL error! It was not possible to add the term into the database.', 'mowsterGL').'</p></div>';
		require_once( MOWSTERG_PLUGIN_PATH . '/tools/list_terms.php' );
	}
	
} else {


require_once( MOWSTERG_PLUGIN_PATH . '/header.php');


?>
<form method="post" id="mowsterG_add_preview" action="" autocomplete="off">
	
	<div id="poststuff" class="metabox-holder">
		<div id="formatdiv" class="postbox" style="width: 600px;">
			<h3 class="handle">
				<span><?php _e('New term', 'mowsterGL'); ?></span>
			</h3>
		</div>
	</div>

	<div class="inside">
		<fieldset>				
			<input type="text" id="new_term" name="new_term" value="" onblur="if (this.value == '') {this.value = '<?php _e('Type here the new term', 'mowsterGL'); ?>'; this.style.color='#BBBBBB';}" onfocus="if (this.value == '<?php _e('Type here the new term', 'mowsterGL'); ?>') {this.value = ''; this.style.color='#000000';}" />
			<div id="new_term_show"></div>
			<div id="new_term_error"></div>
			
			<div id="td_mowsterG_definition">
				<?php	
				$tiny_mce = mowsterG_tiny_mce_settings();								
				wp_editor('', 'new_mowsterG_definition', array('wpautop' => true, 'textarea_name' => 'new_mowsterG_definition', 'media_buttons' => false, "tinymce" => $tiny_mce)); 
				?>
			</div>
			<div id="new_definition_show"></div>
					
			<div class="submit">	
				<input type="submit" name="submit" id="add_new" value="<?php _e('Add &raquo;', 'mowsterGL'); ?>" /><img src="<?php echo admin_url(); ?>images/wpspin_light.gif" alt="" class="ajaxsave" style="display: none;" />
				<input type="button" name="add_new_edit" id="add_new_edit" value="<?php _e('Edit', 'mowsterGL'); ?>" style="display: none;" />
				<input type="submit" name="submit" id="add_new_submit" class="button-primary" value="<?php _e('Add &raquo;', 'mowsterGL'); ?>" style="display: none;" />
			</div>	
		</fieldset>
	</div>
		
</form>

<?php } ?>