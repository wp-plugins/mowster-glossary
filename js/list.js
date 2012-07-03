jQuery(document).ready(function () {
			
		jQuery('#terms_per_page_submit').click(function(){
			var intRegex = /^\d+$/;
			var str = jQuery('#terms_per_page_input').val();

			if (!intRegex.test(str) || str == "" || str < 1 ) {
				jQuery("#terms_per_page_input").css('border-color','red');
				return false;
			}

			if (confirmChange(str) == true) return true;
			else return false;
		
		});
		
		jQuery('#terms_per_page_input').click(function(){
			jQuery("#terms_per_page_input").css('border','1px solid #DFDFDF');			
		});
		
		
		jQuery('.term').each(function(i) {  
			var tr = jQuery(this).closest('tr');			
			jQuery('#edit-'+tr[0].id).hide();
			jQuery('#delete-'+tr[0].id).hide();
			jQuery('.actions').css('display', 'inline');
		});
		
		jQuery('tr').mouseover(function() { 						
			var tr = jQuery(this).closest('tr');
			jQuery('#edit-'+tr[0].id).show();
			jQuery('#delete-'+tr[0].id).show();
			var color = jQuery(this).css("background-color");

			jQuery(this).css('background-color','#FFFFE0');
			
			jQuery('tr').mouseout(function() {
				jQuery('#edit-'+tr[0].id).hide();
				jQuery('#delete-'+tr[0].id).hide();
				jQuery(this).css('background-color', color);
			});
		});
			
		
});

function confirmChange(number){
		message = mowsterG_list.mowsterG_terms_per_page_warning + number + ' ' + mowsterG_list.mowsterG_terms + ' ?';
		var agreeChange=confirm(message);
		if (agreeChange)
		return true;
		else
		return false;
}

function confirmDelete(term){
		message = mowsterG_list.mowsterG_term_confirm + ' ' + term.toUpperCase() + ' ?';
		var agreeDelete=confirm(message);
		if (agreeDelete)
		return true;
		else
		return false;
}