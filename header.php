<?php

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}

?>

<a href="admin.php?page=mowsterG">
<div id="icon-glossary" class="icon32">
	<br>
</div>
</a>

<h2>
	<?php _e('Glossary', 'mowsterGL'); ?>
	<?php
	$nbr_terms = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM `$table`"));
	$nbr_terms_print = sprintf(
		_n(
			'%d term',
			'%d terms',
			$nbr_terms, 
			'mowsterGL'
		),
		$nbr_terms
	);
	?>
	<a class="add-new-h2" href="admin.php?page=<?php echo MOWSTERG_MAIN_ACTION;?>">
		<?php echo $nbr_terms_print; ?>
	</a>
	<a class="add-new-h2" href="admin.php?page=<?php echo MOWSTERG_ADD_ACTION;?>">
		<?php _e('Add term', 'mowsterGL'); ?>
	</a>
	<a id="jobs" href="http://jobs.mowster.net" target="_blank">
		<img src="<?php echo plugins_url('images/mowsterGlossary_logo.gif', __FILE__); ?>" alt="jobs.mowster.net" title="jobs.mowster.net" style="vertical-align: middle; padding-left: 20px;">
	</a>
</h2>