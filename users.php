<?php

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}

/* user_meta */
add_action('show_user_profile', 'mowsterG_action_show_user_profile');
add_action('edit_user_profile', 'mowsterG_action_show_user_profile');
add_action('edit_user_profile_update', 'mowsterG_action_process_option_update');

function mowsterG_action_show_user_profile($user){ 
	global $current_user;
	
	if ($user->roles[0] != 'administrator' && $current_user->roles[0] == 'administrator'){
	?>	
		<h3><?php _e('Glossary', 'mowsterGL'); ?></h3>
		 
		<table class="form-table">
		<tr>
		<th><label for="something"><?php _e('Authorization access', 'mowsterGL'); ?></label></th>
		<td>
			<input type="checkbox" name="mowsterG_user_access" id="none" value="yes" <?php if (get_user_meta($user->ID, 'mowsterG_user_access', true) == 'yes') echo 'checked'; ?> /> <?php _e('Provide this user the access to mowsterGlossary management features', 'mowsterGL'); ?>
		</td>
		</tr>
		</table>
		<?php
	}
}

function mowsterG_action_process_option_update($user_id){
	if (empty($_POST['mowsterG_user_access'])) $_POST['mowsterG_user_access'] = 'no';
	update_usermeta($user_id, 'mowsterG_user_access', $_POST['mowsterG_user_access']);
}
?>