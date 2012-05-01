<?php

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}

if (mowsterG_html_decode($_POST['submit']) == mowsterG_html_decode(__('Save', 'mowsterGL'))) {
	update_option('mowsterG_terms_per_page', $_REQUEST['terms_per_page_input']);
	$_REQUEST['paged'] = 1; 
}

require_once(MOWSTERG_PLUGIN_PATH .'/header.php');

$paged = isset($_REQUEST['paged'])? $_REQUEST['paged'] : '1'; 
$per_page = get_option('mowsterG_terms_per_page');
$limit = (($paged - 1) * $per_page ) . ',' . $per_page;


$terms = $wpdb->get_results("SELECT * FROM `$table` ORDER BY Title LIMIT ".$limit);

// pagination
$to_page_links = array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => ceil($nbr_terms / $per_page),
	'current' => $paged
);

if ($_REQUEST['action']) {
	$to_page_links['base'] = str_replace(array('&action='.$_GET['action'], '&term='.$_REQUEST['term']), '' , $to_page_links['base']);
} 

if ($_GET['page'] != MOWSTERG_MAIN_ACTION) $to_page_links['base'] = str_replace('page='.$_GET['page'], 'page='.MOWSTERG_MAIN_ACTION, $to_page_links['base']);

$page_links = paginate_links($to_page_links);	


if ($page_links) {
	$pagination_html = '<div class="tablenav-pages">';
	$pagination_html .= sprintf( 
		'<span class="displaying-num">' . __( 'Displaying', 'mowsterGL' ) . ' %s&#8211;%s ' . __( 'of', 'mowsterGL' ) . ' <span class="current-link-count">%s</span></span>%s',
		number_format_i18n( ( $paged - 1 ) * $per_page + 1 ),
		number_format_i18n( min( $paged * $per_page, $nbr_terms ) ),
		number_format_i18n( $nbr_terms ),
		$page_links
	); 
	$pagination_html .= '</div>';
} else {
	$pagination_html = '';
}

// number of terms
if ($terms){
	$number_of_terms = '<form method="post" id="mowsterG_number_terms" action="" autocomplete="off">';
	$number_of_terms .= '<input type="text" name="terms_per_page_input" id="terms_per_page_input" value="'.$per_page.'"> '.__( 'terms per page', 'mowsterGL'); 
	$number_of_terms .= '<a href="admin.php?page='.MOWSTERG_MAIN_ACTION.'&terms_per_page=change" id="terms_per_page_submit">';
	$number_of_terms .= '<input type="submit" value="'.__('Save', 'mowsterGL').'" name="submit" />'; 
	$number_of_terms .= '<input type="hidden" name="action" value="list_terms" />'; 
	$number_of_terms .= '</a>';
	$number_of_terms .= '</form>';
}
?>

<div style="clear: both;" class="wrap">
	<div class="tablenav">
	<?php
		echo $pagination_html;
	?>
	</div>
	<form method="post" id="mowsterG_change_term" action="" autocomplete="off">
		<table border="1" class="widefat compact">
			<thead>                
				<tr>
					<th class="manage-column" style="width: 95px;">ID</th>
					<th class="manage-column"><?php _e( 'Term', 'mowsterGL'); ?> | <?php _e( 'Definition', 'mowsterGL' ); ?></th>
				</tr>
			</thead>
			
			<tbody id="the-list">
				<?php 
				if ($terms) {
					foreach ($terms as $term) { 
					$counter++;
					$rowclass = ($counter % 2)? 'alternate' : '';
					?>
					<tr class="<?php echo $rowclass.' ';?>term" id="<?php echo $term->ID;?>">
						<div id="<?php echo $term->ID;?>">
						<td>
							<?php echo $term->ID;?><br />
							<div class="actions">
								<span id="delete-<?php echo $term->ID;?>" class="trash">
									<a href="admin.php?page=<?php echo MOWSTERG_MAIN_ACTION; ?>&action=delete_term&term=<?php echo $term->ID; ?>&paged=<?php echo $paged; ?>" onClick="return confirmDelete('<?php echo strtoupper($term->Title); ?>')"><?php _e( 'Delete', 'mowsterGL' ); ?></a> | 
								</span>							
								<span id="edit-<?php echo $term->ID;?>" class="edit">
									<a href="admin.php?page=<?php echo MOWSTERG_MAIN_ACTION; ?>&action=edit_term&term=<?php echo $term->ID; ?>"><?php _e( 'Edit', 'mowsterGL' ); ?></a>
								</span>
							</div>
						</td>						
						<td>							
							<?php echo '<div class="term_show" id="term_show_'.$term->ID.'">'.$term->Title.'</div>';?>						
							<div>
								<?php echo nl2br($term->Definition);?>
							</div>
						</td>						
						</div>
					</tr>
					<?php 
					}
				} else {
					echo '<tr><td colspan="2">'. __('There are no Terms in the database.', 'mowsterGL').'</td></tr>';
				}
				?>
			</tbody>

			<tfoot>                
				<tr>
					<th class="manage-column" colspan="2"><div id="terms_per_page"><?php echo $number_of_terms; ?></div></th>
				</tr>
			</tfoot>			
		</table>
	</form>
	<div class="tablenav bottom">
	<?php
		echo $pagination_html;
	?>
	</div>
</div>
