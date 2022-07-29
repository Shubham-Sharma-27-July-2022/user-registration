jQuery(document).ready(function(){
	let otpType = eduumalog.otpType;
	if(otpType === "phone"){
    jQuery("#thim-form-login input[name='log']").attr("placeholder", "Username , Email or Phone");
	}

})