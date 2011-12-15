function confirmDefaults(){
		message = mowsterG_options.mowsterG_default_confirm + ' ?';
		var agreeConfirm=confirm(message);
		if (agreeConfirm)
		return true;
		else
		return false;
}