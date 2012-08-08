jQuery(document).ready(function () {

	jQuery('#new_mowsterG_definition').focus();

	jQuery('#edit_term_st').click(function() {
	
		var error = null;
		
		if (jQuery("#edit_term").val()=="") {
			jQuery("#edit_term").css('border-color','red');
			error = 1;
		}

		if (jQuery("#edit_term").val().length > 255) {
			jQuery("#edit_term").css('border-color','red');
			jQuery("#edit_term_error").show();
			jQuery("#edit_term_error").text(mowsterG.mowsterG_term_lenght_error);
			error = 1;
		}

		var content = tinyMCE.getInstanceById('new_mowsterG_definition').getContent();

		if (content.replace(/<\/?[^>]+>/gi, '')=="") {
			jQuery("#td_mowsterG_definition").css('border-color','red');
			if (error == null) alert(mowsterG.mowsterG_definition_error);
			error = 1;
		}
		
		if (error == 1) return false;
	
		var mowsterG_term = jQuery("#edit_term").val();
		var mowsterG_definition = content;
		var mowsterG_term_id = jQuery("#edit_term_id").val();
		
		var data = {
			action: 'join_mowsterG_add_preview',
			mowsterG_term: mowsterG_term,
			mowsterG_definition: mowsterG_definition,
			mowsterG_term_id: mowsterG_term_id
		};
		
		jQuery.edit_term_clean();
		jQuery("#edit_term_st").hide();
		jQuery(".ajaxsave").show();
		
		jQuery.post(mowsterG.mowsterG_admin_url+"admin-ajax.php", data,
		function(response){
			jQuery(".ajaxsave").hide();
			
			output = response.split('|');

			if (jQuery.trim(output[0]) == 'error'){
				jQuery("#edit_term").css('border-color','red');
				jQuery("#edit_term_error").show();
				jQuery("#edit_term_error").text(output[1]);
				jQuery("#edit_term_st").show();
				return false;
			}
			
			jQuery("#edit_term").hide();
			jQuery("#edit_term_show").show();
			jQuery("#edit_term_show").html(mowsterG_term);
			
			jQuery("#td_mowsterG_definition").hide();
			jQuery("#edit_definition_show").show();
			jQuery("#edit_definition_show").html(mowsterG_definition);
			
			jQuery("#edit_term_edit").show();
			jQuery("#edit_term_submit").show();
			return false;
		});		
		return false;
		
	
	});
	
		
	jQuery("#edit_term").click(function() {
		jQuery.edit_term_clean();
	});
	
	jQuery("#edit_term").keypress(function() {
		jQuery.edit_term_clean();
	});
	
	jQuery("#edit_term_edit").click(function() {
		
		var arr_show = [ "edit_term", "td_mowsterG_definition", "edit_term_st" ];		
		var arr_hide = [ "edit_term_show", "edit_definition_show", "edit_term_edit", "edit_term_submit" ];
		
		jQuery.each(arr_show, function() {
			jQuery("#"+this).show();
		});
		
		jQuery.each(arr_hide, function() {
			jQuery("#"+this).hide();
		});
		
	});

});

jQuery.edit_term_clean = function() {
	jQuery("#edit_term").css('border','1px solid #DFDFDF');
	jQuery("#edit_term_error").hide();
};
	

function update_tinycme_border(){
	document.getElementById('td_mowsterG_definition').style.borderColor="transparent";
}