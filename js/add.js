jQuery(document).ready(function () {

	jQuery('#new_term').focus();

	jQuery('#add_new').click(function() {
	
		var error = null;
		
		if (jQuery("#new_term").val()=="" || jQuery("#new_term").val()==mowsterG.mowsterG_term_default) {
			jQuery("#new_term").css('border-color','red');
			error = 1;
		}

		if (jQuery("#new_term").val().length > 255) {
			jQuery("#new_term").css('border-color','red');
			jQuery("#new_term_error").show();
			jQuery("#new_term_error").text(mowsterG.mowsterG_term_lenght_error);
			error = 1;
		}

		var content = tinyMCE.getInstanceById('new_mowsterG_definition').getContent();

		if (content.replace(/<\/?[^>]+>/gi, '')=="") {
			jQuery("#td_mowsterG_definition").css('border-color','red');
			if (error == null) alert(mowsterG.mowsterG_definition_error);
			error = 1;
		}
		
		if (error == 1) return false;
	
		var mowsterG_term = jQuery("#new_term").val();
		var mowsterG_definition = content;
		
		var data = {
			action: 'join_mowsterG_add_preview',
			mowsterG_term: mowsterG_term,
			mowsterG_definition: mowsterG_definition
		};
		
		jQuery.new_term_clean();
		jQuery("#add_new").hide();
		jQuery(".ajaxsave").show();
		
		jQuery.post(mowsterG.mowsterG_admin_url+"admin-ajax.php", data,
		function(response){
			jQuery(".ajaxsave").hide();
			
			output = response.split('|');

			if (jQuery.trim(output[0]) == 'error'){
				jQuery("#new_term").css('border-color','red');
				jQuery("#new_term_error").show();
				jQuery("#new_term_error").text(output[1]);
				jQuery("#add_new").show();
				return false;
			}
			jQuery("#new_term").hide();
			jQuery("#new_term_show").show();
			jQuery("#new_term_show").html(mowsterG_term);
			
			jQuery("#td_mowsterG_definition").hide();
			jQuery("#new_definition_show").show();
			jQuery("#new_definition_show").html(mowsterG_definition);
			
			jQuery("#add_new_edit").show();
			jQuery("#add_new_submit").show();
			return false;
		});		
		return false;
		
	
	});
	
		
	jQuery("#new_term").click(function() {
		jQuery.new_term_clean();
	});
	
	jQuery("#new_term").keypress(function() {
		jQuery.new_term_clean();
	});
	
	jQuery("#add_new_edit").click(function() {
		
		var arr_show = [ "new_term", "td_mowsterG_definition", "add_new" ];		
		var arr_hide = [ "new_term_show", "new_definition_show", "add_new_edit", "add_new_submit" ];
		
		jQuery.each(arr_show, function() {
			jQuery("#"+this).show();
		});
		
		jQuery.each(arr_hide, function() {
			jQuery("#"+this).hide();
		});
		
	});

});

jQuery.new_term_clean = function() {
	jQuery("#new_term").css('border','1px solid #DFDFDF');
	jQuery("#new_term_error").hide();
};
	

function update_tinycme_border(){
	document.getElementById('td_mowsterG_definition').style.borderColor="transparent";
}





