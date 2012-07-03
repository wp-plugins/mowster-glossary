<?php

if (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"])) {
	$location = 'http://'.substr($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"], '/wp-content/'));
	header('Location: '.$location);
	die();
}

$newcontent = $content;
$shortcode = get_option('mowsterG_shortcode');

if( preg_match('/\['.$shortcode.'\]/', $newcontent) ) {

	global $wpdb; 
	$table = $wpdb->prefix.MOWSTERG_TABLE;

	if (get_option('permalink_structure')) {
		$page = get_query_var('mowsterG');	
		
		if (empty($page)) $paged = 1; else $paged = $page;			
		$base = get_permalink().get_option('mowsterG_pagination_word').'/%#%';
	} else {
		$paged = isset($_REQUEST['mowsterG'])? $_REQUEST['mowsterG'] : '1';
		$base = add_query_arg('mowsterG', '%#%');
	}
	
	$per_page = get_option('mowsterG_terms_per_page');
	$limit = (($paged - 1) * $per_page ) . ',' . $per_page;
	
	$terms = $wpdb->get_results("SELECT * FROM `$table` ORDER BY Title LIMIT ".$limit);
	$nbr_terms = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM `$table`"));
	
	
	// pagination
	$to_page_links = array(
		'base' => $base,
		'format' => '',
		'end_size' => get_option('mowsterG_end_size'),
		'mid_size' => get_option('mowsterG_mid_size'),
		'prev_text' => get_option('mowsterG_text_previous_page'),
		'next_text' => get_option('mowsterG_text_next_page'),
		'total' => ceil($nbr_terms / $per_page),
		'current' => $paged,
	);

	$page_links = paginate_links($to_page_links);

	if ($page_links) {
		$pagination_html = '<div class="page-link">';
		$pagination_html .= sprintf( 
			'<span id="mowsterG_terms">%s ' . __( 'terms', 'mowsterGL' ) . '</span> <span id="mowsterG_interval">%s &#8211; %s</span> <span id="mowsterG_pages">%s</span>',
			number_format_i18n( $nbr_terms ),
			number_format_i18n( ( $paged - 1 ) * $per_page + 1 ),
			number_format_i18n( min( $paged * $per_page, $nbr_terms ) ),
			$page_links
		); 
		$pagination_html .= '</div>';
	} else {
		$pagination_html = '';
	}	

	
	$glossary = '<div id="mowsterG">';
	if ($terms) {
		$before_term = stripslashes(get_option('mowsterG_html_before_term'));
		$after_term = stripslashes(get_option('mowsterG_html_after_term'));		
		$before_description = stripslashes(get_option('mowsterG_html_before_description'));
		$after_description = stripslashes(get_option('mowsterG_html_after_description'));		
		
		foreach ($terms as $term) { 
		$glossary .= $before_term.$term->Title.$after_term;
		$glossary .= $before_description.nl2br($term->Definition).$after_description;
		}
	}
	$glossary .= '</div>';
	$glossary .= $pagination_html;

	$newcontent = preg_replace('/\['.$shortcode.'\]/', $glossary, $newcontent);
	
	$content = $newcontent;
	
}	

?>